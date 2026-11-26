<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{Conversation, Message, Booking};
use App\Services\{AuditService, ChatSecurityService, NotificationService};
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * List all conversations for investor
     */
    public function index(Request $request)
    {
        $investor = auth()->user()->investor;

        $query = Conversation::where('investor_id', $investor->id)
            ->with(['apartment', 'latestMessage'])
            ->orderByDesc('last_message_at');

        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%");
            });
        }

        $conversations = $query->paginate(15);
        $apartments = $investor->apartments;

        return view('investor.chat.index', compact('conversations', 'apartments', 'investor'));
    }

    /**
     * Show conversation chat room for investor
     */
    public function show(Conversation $conversation)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id) {
            abort(403);
        }

        $conversation->load(['apartment', 'booking', 'messages']);

        // Mark unread customer/admin messages as read by investor
        $conversation->messages()
            ->whereIn('sender_type', ['customer', 'admin'])
            ->where('is_read_by_investor', false)
            ->update(['is_read_by_investor' => true]);

        return view('investor.chat.show', compact('conversation', 'investor'));
    }

    /**
     * Send message from investor
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['message'], 'investor');

        $msg = $conversation->messages()->create([
            'sender_type' => 'investor',
            'sender_id' => auth()->id(),
            'sender_name' => $investor->full_name,
            'message' => $guardResult['cleaned_message'],
            'is_read_by_customer' => false,
            'is_read_by_investor' => true,
            'is_read_by_admin' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $conversation->loadMissing('apartment', 'booking');
        $aptNumber = $conversation->apartment?->apartment_number ?? '';

        // Notify customer via phone with direct link to return to their chat!
        if ($conversation->customer_phone) {
            \App\Services\NotificationService::sendWhatsApp(
                $conversation->customer_phone,
                "💬 رد مالك شقة ({$aptNumber}):\n\"{$guardResult['cleaned_message']}\"\nرابط متابعة المحادثة والرد مباشرة:\n" . route('client.chat.show', $conversation->customer_token),
                'customer',
                'chat',
                $conversation->id
            );
        }

        // Send instant background Web Push notification to client (even if website is closed)
        \App\Services\WebPushService::sendToClientByConversation(
            $conversation,
            "💬 رد جديد من مالك شقة {$aptNumber}",
            \Illuminate\Support\Str::limit($guardResult['cleaned_message'], 120),
            route('client.chat.show', $conversation->customer_token),
            'client-chat-' . $conversation->id
        );

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

        return back()->with('success', 'تم إرسال الرد بنجاح');
    }

    /**
     * Polling messages endpoint for investor
     */
    public function getMessages(Conversation $conversation)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id) {
            abort(403);
        }

        // Mark incoming messages as read
        $conversation->messages()
            ->whereIn('sender_type', ['customer', 'admin'])
            ->where('is_read_by_investor', false)
            ->update(['is_read_by_investor' => true]);

        $messages = $conversation->messages()->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'message' => $m->is_deleted ? 'تم حذف هذه الرسالة' : $m->message,
                'is_deleted' => (bool)$m->is_deleted,
                'is_edited' => (bool)$m->is_edited,
                'created_at' => $m->created_at->format('H:i'),
                'is_me' => $m->sender_type === 'investor',
            ];
        });

        return response()->json([
            'status' => $conversation->status,
            'deal_status' => $conversation->deal_status,
            'messages' => $messages,
        ]);
    }

    /**
     * Edit investor's own message
     */
    public function updateMessage(Request $request, Conversation $conversation, Message $message)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id || $message->conversation_id !== $conversation->id) {
            abort(403);
        }

        if ($message->sender_type !== 'investor') {
            return response()->json(['success' => false, 'message' => 'لا يمكنك تعديل رسائل الطرف الآخر'], 403);
        }

        if ($message->is_deleted) {
            return response()->json(['success' => false, 'message' => 'لا يمكن تعديل رسالة تم حذفها'], 422);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['message'], 'investor');

        // Save original text on first edit
        if (!$message->original_message) {
            $message->original_message = $message->message;
        }

        $message->message = $guardResult['cleaned_message'];
        $message->is_edited = true;
        $message->edited_at = now();
        $message->save();

        AuditService::log('message_edited_by_investor', $conversation, null, [
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
     * Delete investor's own message (soft delete with admin audit trail)
     */
    public function deleteMessage(Request $request, Conversation $conversation, Message $message)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id || $message->conversation_id !== $conversation->id) {
            abort(403);
        }

        if ($message->sender_type !== 'investor') {
            return response()->json(['success' => false, 'message' => 'لا يمكنك حذف رسائل الطرف الآخر'], 403);
        }

        $message->is_deleted = true;
        $message->deleted_at = now();
        $message->deleted_by = 'investor';
        $message->save();

        AuditService::log('message_deleted_by_investor', $conversation, null, [
            'message_id' => $message->id,
            'deleted_text' => $message->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرسالة بنجاح',
        ]);
    }

    /**
     * Submit investor deal closure survey
     */
    public function submitSurvey(Request $request, Conversation $conversation)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'response' => 'required|in:deal_done,no_deal',
            'notes' => 'nullable|string|max:500',
        ]);

        $conversation->update([
            'investor_survey_response' => $validated['response'],
        ]);

        // Check for discrepancy
        if ($validated['response'] === 'no_deal' && $conversation->client_survey_response === 'deal_done') {
            $conversation->update([
                'has_bypass_attempt' => true,
                'deal_status' => 'caught_offline',
                'bypass_attempt_reason' => 'تضارب استبيان إتمام الصفقة: النزيل أكد إتمام الصفقة بينما أنكر المالك للتهرب من عمولة المنصة',
            ]);

            AuditService::log('deal_discrepancy_alert', $conversation, null, [
                'client' => 'deal_done',
                'investor' => 'no_deal',
            ]);
        }

        return back()->with('success', 'تم تسجيل إفادتك حول حالة الصفقة بنجاح.');
    }
}
