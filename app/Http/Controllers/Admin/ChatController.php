<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Conversation, Investor, Apartment, Message};
use App\Services\AuditService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Admin Oversight: List all conversations across the entire tower
     */
    public function index(Request $request)
    {
        $query = Conversation::with(['apartment', 'investor', 'latestMessage'])
            ->orderByDesc('last_message_at');

        if ($request->filled('investor_id')) {
            $query->where('investor_id', $request->investor_id);
        }

        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $conversations = $query->paginate(20);
        $investors = Investor::where('status', 'approved')->get();
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();

        return view('admin.chat.index', compact('conversations', 'investors', 'totalConversations', 'totalMessages'));
    }

    /**
     * Admin Oversight: View full transcript of a conversation
     */
    public function show(Conversation $conversation)
    {
        $conversation->load(['apartment', 'investor.user', 'booking', 'messages']);

        // Mark as read by admin
        $conversation->messages()->where('is_read_by_admin', false)->update(['is_read_by_admin' => true]);

        return view('admin.chat.show', compact('conversation'));
    }

    /**
     * Admin Intervention: Post official message to conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $msg = $conversation->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => auth()->id(),
            'sender_name' => 'إدارة البرج',
            'message' => $validated['message'],
            'is_read_by_customer' => false,
            'is_read_by_investor' => false,
            'is_read_by_admin' => true,
        ]);

        $conversation->update(['last_message_at' => now()]);

        AuditService::log('admin_sent_chat_message', $conversation, null, ['message' => $validated['message']]);

        $conversation->loadMissing('apartment.investor');
        $investor = $conversation->apartment?->investor ?? $conversation->investor;
        if ($investor) {
            \App\Services\WebPushService::sendToInvestor(
                $investor,
                "🏛 رسالة من إدارة المنصة - شقة {$conversation->apartment?->apartment_number}",
                $validated['message'],
                route('investor.chats.show', $conversation->id),
                'chat-admin-' . $conversation->id
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->format('H:i'),
                ]
            ]);
        }

        return back()->with('success', 'تم إرسال التدخل الإداري بنجاح');
    }

    /**
     * Polling messages endpoint for Admin
     */
    public function getMessages(Conversation $conversation)
    {
        $conversation->messages()->where('is_read_by_admin', false)->update(['is_read_by_admin' => true]);

        $messages = $conversation->messages()->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'message' => $m->message,
                'is_edited' => (bool)$m->is_edited,
                'original_message' => $m->original_message,
                'edited_at' => $m->edited_at ? $m->edited_at->format('H:i d/m') : null,
                'is_deleted' => (bool)$m->is_deleted,
                'deleted_at' => $m->deleted_at ? $m->deleted_at->format('H:i d/m') : null,
                'deleted_by' => $m->deleted_by,
                'created_at' => $m->created_at->format('H:i'),
                'is_me' => $m->sender_type === 'admin',
            ];
        });

        return response()->json([
            'status' => $conversation->status,
            'messages' => $messages,
        ]);
    }

    /**
     * Toggle conversation status (open/closed)
     */
    public function toggleStatus(Conversation $conversation)
    {
        $newStatus = $conversation->status === 'open' ? 'closed' : 'open';
        $conversation->update(['status' => $newStatus]);

        AuditService::log('conversation_status_toggled', $conversation, null, ['status' => $newStatus]);

        return back()->with('success', 'تم تعديل حالة المحادثة بنجاح');
    }
}
