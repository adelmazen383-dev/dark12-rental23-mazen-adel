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
                'message' => $m->message,
                'created_at' => $m->created_at->format('H:i'),
                'is_me' => $m->sender_type === 'investor',
            ];
        });

        return response()->json([
            'status' => $conversation->status,
            'deal_status' => $conversation->deal_status,
            'offer_price' => $conversation->offer_price,
            'messages' => $messages,
        ]);
    }

    /**
     * Investor makes an official binding offer to the client in chat:
     * Specifying dates and negotiated price.
     */
    public function makeOffer(Request $request, Conversation $conversation)
    {
        $investor = auth()->user()->investor;
        if ($conversation->investor_id !== $investor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'offer_price' => 'required|numeric|min:1',
            'offer_checkin_date' => 'required|date|after_or_equal:today',
            'offer_checkout_date' => 'required|date|after:offer_checkin_date',
            'offer_notes' => 'nullable|string|max:500',
        ]);

        $conversation->update([
            'deal_status' => 'offer_sent',
            'offer_price' => $validated['offer_price'],
            'offer_checkin_date' => $validated['offer_checkin_date'],
            'offer_checkout_date' => $validated['offer_checkout_date'],
            'offer_notes' => $validated['offer_notes'] ?? null,
            'last_message_at' => now(),
        ]);

        $nights = Carbon::parse($validated['offer_checkin_date'])->diffInDays(Carbon::parse($validated['offer_checkout_date']));

        // Post system offer notice in the chat
        $conversation->messages()->create([
            'sender_type' => 'investor',
            'sender_id' => auth()->id(),
            'sender_name' => $investor->full_name,
            'message' => "🏷️ عرض حجز رسمي مقدم من المالك:\n" .
                         "• الإقامة: من {$validated['offer_checkin_date']} إلى {$validated['offer_checkout_date']} ({$nights} ليالٍ)\n" .
                         "• السعر الإجمالي المتفق عليه: {$validated['offer_price']} ريال سعودي\n" .
                         ($validated['offer_notes'] ? "• ملاحظات: {$validated['offer_notes']}\n" : "") .
                         "👉 يمكنك قبول هذا العرض بالأسفل لإصدار كود استلام المفتاح من الاستقبال فوراً.",
            'is_read_by_customer' => false,
            'is_read_by_investor' => true,
            'is_read_by_admin' => false,
        ]);

        AuditService::log('investor_sent_deal_offer', $conversation, null, $validated);

        return back()->with('success', 'تم إرسال عرض الحجز الرسمي للنزيل في المحادثة بنجاح.');
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
