<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Booking, Conversation, Message, Setting};
use App\Services\{AuditService, ChatSecurityService, NotificationService, WalletService};
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * List all conversations belonging to the client by their phone number
     */
    public function index(Request $request)
    {
        $phone = $request->query('phone') ?? session('client_phone') ?? $request->cookie('dark_client_phone');

        $conversations = collect();
        if ($phone) {
            $conversations = Conversation::where('customer_phone', $phone)
                ->with(['apartment.media', 'investor.user', 'latestMessage'])
                ->orderByDesc('last_message_at')
                ->get();
        }

        return view('client.chat.index', compact('conversations', 'phone'));
    }

    /**
     * Lookup client conversations by phone
     */
    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
        ], [
            'phone.required' => 'يرجى إدخال رقم الجوال.',
        ]);

        $phone = trim($validated['phone']);
        session(['client_phone' => $phone]);
        cookie()->queue(cookie('dark_client_phone', $phone, 60 * 24 * 30));

        return redirect()->route('client.chat.index', ['phone' => $phone]);
    }

    /**
     * Start or resume a conversation from client side
     */
    public function start(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'initial_message' => 'nullable|string|max:1000',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        session(['client_phone' => $validated['customer_phone']]);
        cookie()->queue(cookie('dark_client_phone', $validated['customer_phone'], 60 * 24 * 30));

        // Find existing open conversation for this apartment & phone, or create new
        $conversation = Conversation::where('apartment_id', $apartment->id)
            ->where('customer_phone', $validated['customer_phone'])
            ->where('status', 'open')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'apartment_id' => $apartment->id,
                'investor_id' => $apartment->investor_id,
                'booking_id' => $validated['booking_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'subject' => "محادثة حول شقة {$apartment->apartment_number} (الدور {$apartment->floor_number})",
                'last_message_at' => now(),
                'status' => 'open',
                'deal_status' => 'negotiating',
            ]);
        }

        // Add initial message if provided
        if (!empty($validated['initial_message'])) {
            $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['initial_message'], 'customer');

            $conversation->messages()->create([
                'sender_type' => 'customer',
                'sender_name' => $conversation->customer_name,
                'message' => $guardResult['cleaned_message'],
                'is_read_by_customer' => true,
                'is_read_by_investor' => false,
                'is_read_by_admin' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);

            // Notify investor via phone/WhatsApp & Web Push (works even when site is closed)
            $apartment = $conversation->apartment;
            if ($apartment && $apartment->investor) {
                if ($apartment->investor->whatsapp) {
                    NotificationService::sendWhatsApp(
                        $apartment->investor->whatsapp,
                        "💬 استفسار ومحادثة جديدة من النزيل ({$conversation->customer_name}) حول شقة {$apartment->apartment_number}:\n\"{$guardResult['cleaned_message']}\"\nرابط الرد الفوري من بوابتك:\n" . route('investor.chats.show', $conversation->id),
                        'investor',
                        'chat',
                        $conversation->id
                    );
                }

                \App\Services\WebPushService::sendToInvestor(
                    $apartment->investor,
                    "💬 رسالة من ({$conversation->customer_name}) - شقة {$apartment->apartment_number}",
                    $guardResult['cleaned_message'],
                    route('investor.chats.show', $conversation->id),
                    'chat-' . $conversation->id
                );
            }
        }

        return redirect()->route('client.chat.show', $conversation->customer_token);
    }

    /**
     * Show chat room for customer
     */
    public function show($token)
    {
        $conversation = Conversation::where('customer_token', $token)
            ->with(['apartment.investor', 'messages', 'booking'])
            ->firstOrFail();

        session(['client_phone' => $conversation->customer_phone]);
        cookie()->queue(cookie('dark_client_phone', $conversation->customer_phone, 60 * 24 * 30));

        // Mark incoming messages as read by customer
        $conversation->messages()
            ->whereIn('sender_type', ['investor', 'admin'])
            ->where('is_read_by_customer', false)
            ->update(['is_read_by_customer' => true]);

        return view('client.chat.show', compact('conversation'));
    }

    /**
     * Send message from customer
     */
    public function sendMessage(Request $request, $token)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['message'], 'customer');

        $msg = $conversation->messages()->create([
            'sender_type' => 'customer',
            'sender_name' => $conversation->customer_name,
            'message' => $guardResult['cleaned_message'],
            'is_read_by_customer' => true,
            'is_read_by_investor' => false,
            'is_read_by_admin' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Notify investor via phone/WhatsApp & Web Push (reaches investor even if site/browser is closed)
        $apartment = $conversation->apartment;
        if ($apartment && $apartment->investor) {
            if ($apartment->investor->whatsapp) {
                NotificationService::sendWhatsApp(
                    $apartment->investor->whatsapp,
                    "💬 رسالة جديدة من النزيل ({$conversation->customer_name}) حول شقة {$apartment->apartment_number}:\n\"{$guardResult['cleaned_message']}\"\nرابط الرد الفوري في بوابتك:\n" . route('investor.chats.show', $conversation->id),
                    'investor',
                    'chat',
                    $conversation->id
                );
            }

            \App\Services\WebPushService::sendToInvestor(
                $apartment->investor,
                "💬 رسالة من ({$conversation->customer_name}) - شقة {$apartment->apartment_number}",
                $guardResult['cleaned_message'],
                route('investor.chats.show', $conversation->id),
                'chat-' . $conversation->id
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'flagged' => $guardResult['flagged'],
                'message' => [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->format('H:i'),
                ]
            ]);
        }

        return back()->with('success', 'تم إرسال رسالتك');
    }

    /**
     * Polling messages endpoint for customer
     */
    public function getMessages($token)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();

        // Mark incoming messages as read
        $conversation->messages()
            ->whereIn('sender_type', ['investor', 'admin'])
            ->where('is_read_by_customer', false)
            ->update(['is_read_by_customer' => true]);

        $messages = $conversation->messages()->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'message' => $m->is_deleted ? 'تم حذف هذه الرسالة' : $m->message,
                'is_deleted' => (bool)$m->is_deleted,
                'is_edited' => (bool)$m->is_edited,
                'created_at' => $m->created_at->format('H:i'),
                'is_me' => $m->sender_type === 'customer',
            ];
        });

        return response()->json([
            'status' => $conversation->status,
            'deal_status' => $conversation->deal_status,
            'messages' => $messages,
        ]);
    }

    /**
     * Client edits their own message
     */
    public function updateMessage(Request $request, $token, Message $message)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();
        if ($message->conversation_id !== $conversation->id) {
            abort(403);
        }

        if ($message->sender_type !== 'customer') {
            return response()->json(['success' => false, 'message' => 'لا يمكنك تعديل رسائل الطرف الآخر'], 403);
        }

        if ($message->is_deleted) {
            return response()->json(['success' => false, 'message' => 'لا يمكن تعديل رسالة تم حذفها'], 422);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['message'], 'customer');

        // Preserve original text on first edit
        if (!$message->original_message) {
            $message->original_message = $message->message;
        }

        $message->message = $guardResult['cleaned_message'];
        $message->is_edited = true;
        $message->edited_at = now();
        $message->save();

        AuditService::log('message_edited_by_customer', $conversation, null, [
            'message_id' => $message->id,
            'original' => $message->original_message,
            'new' => $message->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->message,
            'is_edited' => true,
        ]);
    }

    /**
     * Client deletes their own message (soft delete with admin audit trail)
     */
    public function deleteMessage(Request $request, $token, Message $message)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();
        if ($message->conversation_id !== $conversation->id) {
            abort(403);
        }

        if ($message->sender_type !== 'customer') {
            return response()->json(['success' => false, 'message' => 'لا يمكنك حذف رسائل الطرف الآخر'], 403);
        }

        $message->is_deleted = true;
        $message->deleted_at = now();
        $message->deleted_by = 'customer';
        $message->save();

        AuditService::log('message_deleted_by_customer', $conversation, null, [
            'message_id' => $message->id,
            'deleted_text' => $message->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرسالة بنجاح',
        ]);
    }

    /**
     * Submit client deal closure survey
     */
    public function submitSurvey(Request $request, $token)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();

        $validated = $request->validate([
            'response' => 'required|in:deal_done,no_deal',
            'notes' => 'nullable|string|max:500',
        ]);

        $conversation->update([
            'client_survey_response' => $validated['response'],
        ]);

        // Check if there is discrepancy with investor survey
        if ($validated['response'] === 'deal_done' && $conversation->investor_survey_response === 'no_deal') {
            $conversation->update([
                'has_bypass_attempt' => true,
                'deal_status' => 'caught_offline',
                'bypass_attempt_reason' => 'تضارب استبيان إتمام الصفقة: النزيل أكد الاتفاق بينما أنكر المالك الاتفاق للتهرب من العمولة',
            ]);

            AuditService::log('deal_discrepancy_alert', $conversation, null, [
                'client' => 'deal_done',
                'investor' => 'no_deal',
            ]);
        }

        return back()->with('success', 'شكراً لمشاركتنا تقييم حالة التفاوض.');
    }
}
