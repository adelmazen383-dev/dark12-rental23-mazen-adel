@extends('layouts.investor')

@section('title', 'محادثة مع ' . $conversation->customer_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Top info bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0 font-bold border border-emerald-500/20">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-sm text-slate-900">محادثة النزيل: {{ $conversation->customer_name }}</h2>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">شقة {{ $conversation->apartment->apartment_number }} (الدور {{ $conversation->apartment->floor_number }})</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if($conversation->booking)
                        <span class="text-emerald-700 font-bold font-sans">مرتبط بحجز مؤكد #{{ $conversation->booking->reference_number }}</span>
                    @else
                        <span class="text-slate-400">محادثة واستفسار مباشر وآمن داخل المنصة</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('investor.chats.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                العودة للقائمة
            </a>
        </div>
    </div>

    {{-- ===== DEAL STATUS BADGE ===== --}}
    @php
        $dealStatus = $conversation->deal_status;
        $statusMap = [
            'negotiating'    => ['bg-slate-100 text-slate-700',   'fa-comments',      'محادثة نشطة'],
            'confirmed'      => ['bg-emerald-100 text-emerald-800','fa-circle-check',   'تم الحجز بشكل رسمي ✅'],
            'cancelled'      => ['bg-rose-100 text-rose-800',     'fa-xmark-circle',    'تم إلغاء الصفقة'],
            'caught_offline' => ['bg-red-100 text-red-900',       'fa-triangle-exclamation','⚠️ تنبيه نظام'],
        ];
        [$badgeClass, $badgeIcon, $badgeLabel] = $statusMap[$dealStatus] ?? ['bg-slate-100 text-slate-600','fa-circle','محادثة جارية'];
    @endphp
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $badgeClass }} text-xs font-bold border border-slate-200/80 shadow-sm">
        <i class="fa-solid {{ $badgeIcon }}"></i>
        <span>حالة المحادثة: {{ $badgeLabel }}</span>
    </div>

    {{-- ===== BYPASS ALERT (investor view) ===== --}}
    @if($conversation->has_bypass_attempt)
    <div class="bg-rose-50 border border-rose-300 rounded-2xl p-4 text-xs text-rose-800 flex items-start gap-3 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl mt-0.5"></i>
        <div>
            <p class="font-black text-sm text-rose-900">⚠️ تنبيه أمني — محاولة تجاوز المنصة</p>
            <p class="mt-1 text-rose-700">{{ $conversation->bypass_attempt_reason }}</p>
            <p class="mt-1 text-rose-500">الرجاء الالتزام بسياسات المنصة وإجراء جميع المحادثات وتأكيد الحجز عبر النظام الرسمي.</p>
        </div>
    </div>
    @endif

    {{-- ===== CONFIRMED BOOKING INFO ===== --}}
    @if($dealStatus === 'confirmed' && $conversation->booking)
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs flex items-start gap-3 shadow-sm">
        <i class="fa-solid fa-circle-check text-emerald-500 text-xl mt-0.5"></i>
        <div>
            <p class="font-black text-sm text-emerald-900">تم تأكيد الحجز بنجاح</p>
            <p class="mt-1 text-emerald-800">رقم الحجز: <strong>#{{ $conversation->booking->reference_number }}</strong></p>
            <p class="text-slate-600 mt-0.5">كود استلام المفتاح: <strong>{{ $conversation->booking->reception_code }}</strong></p>
        </div>
    </div>
    @endif

    <!-- Chat Messages Window -->
    <div id="messagesWindow" class="h-[520px] bg-white rounded-2xl border border-slate-200 p-4 shadow-sm overflow-y-auto space-y-3 flex flex-col">
        <div class="text-center my-1">
            <span class="px-3 py-1 rounded-full text-[10px] bg-slate-100 text-slate-500 font-medium">
                سجل الرسائل المتبادلة مع النزيل
            </span>
        </div>

        <div id="messagesList" class="space-y-3 flex-1 flex flex-col justify-end">
            @forelse($conversation->messages as $msg)
                <div class="flex flex-col {{ $msg->sender_type === 'investor' ? 'items-start' : 'items-end' }}" id="msg-box-{{ $msg->id }}">
                    <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm
                        @if($msg->sender_type === 'investor')
                            bg-emerald-600 text-white font-medium rounded-br-none
                        @elseif($msg->sender_type === 'customer')
                            bg-slate-100 text-slate-900 border border-slate-200 rounded-bl-none
                        @else
                            bg-indigo-600 text-white rounded-bl-none
                        @endif">
                        <div class="flex items-center justify-between gap-3 mb-1 text-[10px] {{ $msg->sender_type === 'investor' ? 'text-emerald-200 font-bold' : ($msg->sender_type === 'admin' ? 'text-indigo-200' : 'text-slate-500') }}">
                            <span>
                                @if($msg->sender_type === 'investor')
                                    <i class="fa-solid fa-user-tie ml-1"></i> أنت (المالك)
                                @elseif($msg->sender_type === 'admin')
                                    <i class="fa-solid fa-shield-halved ml-1"></i> إدارة البرج
                                @else
                                    <i class="fa-solid fa-user ml-1"></i> النزيل ({{ $msg->sender_name }})
                                @endif
                            </span>
                            <div class="flex items-center gap-1.5">
                                @if($msg->is_edited && !$msg->is_deleted)
                                    <span class="text-[9px] opacity-80">(معدّلة)</span>
                                @endif
                                <span>{{ $msg->created_at->format('H:i') }}</span>
                                @if($msg->sender_type === 'investor' && !$msg->is_deleted)
                                    <button onclick="editMsg({{ $msg->id }}, '{{ addslashes($msg->message) }}')" class="hover:text-amber-200 text-emerald-200 ml-1" title="تعديل الرسالة">
                                        <i class="fa-solid fa-pencil text-[10px]"></i>
                                    </button>
                                    <button onclick="deleteMsg({{ $msg->id }})" class="hover:text-rose-300 text-emerald-200" title="حذف الرسالة">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @if($msg->is_deleted)
                            <p class="italic opacity-70 text-[11px]"><i class="fa-solid fa-ban ml-1"></i> تم حذف هذه الرسالة</p>
                        @else
                            <p class="whitespace-pre-line leading-relaxed" id="msg-text-{{ $msg->id }}">{{ $msg->message }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-slate-400">
                    <p class="text-xs">لا توجد رسائل سابقة</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Message Input Bar -->
    <div class="bg-white rounded-2xl p-2.5 border border-slate-200 shadow-sm">
        <form id="chatForm" action="{{ route('investor.chats.send', $conversation->id) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input type="text" id="messageInput" name="message" required placeholder="اكتب ردك للنزيل..." autocomplete="off" class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-xs text-slate-900">
            <button type="submit" id="sendBtn" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <span>إرسال الرد</span>
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </form>
    </div>

</div>

<script>
    const messagesWindow = document.getElementById('messagesWindow');
    const messagesList = document.getElementById('messagesList');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendUrl = @json(route('investor.chats.send', $conversation->id));
    const getUrl = @json(route('investor.chats.messages', $conversation->id));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function scrollToBottom() {
        messagesWindow.scrollTop = messagesWindow.scrollHeight;
    }
    scrollToBottom();

    // AJAX form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        messageInput.value = '';

        fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(r => r.json())
        .then(() => fetchMessages())
        .catch(err => console.error(err));
    });

    // Edit message
    window.editMsg = function(msgId, oldText) {
        const newText = prompt('تعديل الرسالة:', oldText);
        if (newText === null || newText.trim() === '' || newText === oldText) return;

        fetch(`/investor/chats/{{ $conversation->id }}/messages/${msgId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: newText.trim() })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                fetchMessages();
            } else {
                alert(res.message || 'تعذر تعديل الرسالة');
            }
        })
        .catch(err => console.error(err));
    };

    // Delete message
    window.deleteMsg = function(msgId) {
        if (!confirm('هل أنت متأكد من حذف هذه الرسالة؟')) return;

        fetch(`/investor/chats/{{ $conversation->id }}/messages/${msgId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                fetchMessages();
            } else {
                alert(res.message || 'تعذر حذف الرسالة');
            }
        })
        .catch(err => console.error(err));
    };

    function fetchMessages() {
        fetch(getUrl, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.messages) {
                renderMessages(data.messages);
            }
        })
        .catch(err => console.error(err));
    }

    // Polling every 3.5 seconds
    setInterval(fetchMessages, 3500);

    function renderMessages(messages) {
        let html = '';
        messages.forEach(m => {
            const isMe = m.sender_type === 'investor';
            const isAdmin = m.sender_type === 'admin';
            const isCustomer = m.sender_type === 'customer';

            let bubbleClass = isMe ? 'bg-emerald-600 text-white font-medium rounded-br-none'
                            : (isAdmin ? 'bg-indigo-600 text-white rounded-bl-none'
                            : 'bg-slate-100 text-slate-900 border border-slate-200 rounded-bl-none');
            let alignClass = isMe ? 'items-start' : 'items-end';
            let headerClass = isMe ? 'text-emerald-200 font-bold'
                            : (isAdmin ? 'text-indigo-200' : 'text-slate-500');

            let senderLabel = isMe ? '<i class="fa-solid fa-user-tie ml-1"></i> أنت (المالك)'
                            : (isAdmin ? '<i class="fa-solid fa-shield-halved ml-1"></i> إدارة البرج'
                            : `<i class="fa-solid fa-user ml-1"></i> النزيل (${m.sender_name})`);

            let actionBtns = '';
            if (isMe && !m.is_deleted) {
                const escaped = m.message.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                actionBtns = `
                    <button onclick="editMsg(${m.id}, '${escaped}')" class="hover:text-amber-200 text-emerald-200 ml-1" title="تعديل">
                        <i class="fa-solid fa-pencil text-[10px]"></i>
                    </button>
                    <button onclick="deleteMsg(${m.id})" class="hover:text-rose-300 text-emerald-200" title="حذف">
                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                    </button>
                `;
            }

            let msgBody = m.is_deleted
                ? `<p class="italic opacity-70 text-[11px]"><i class="fa-solid fa-ban ml-1"></i> تم حذف هذه الرسالة</p>`
                : `<p class="whitespace-pre-line leading-relaxed">${m.message.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>`;

            let editedBadge = (m.is_edited && !m.is_deleted) ? `<span class="text-[9px] opacity-80">(معدّلة)</span>` : '';

            html += `
                <div class="flex flex-col ${alignClass}" id="msg-box-${m.id}">
                    <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm ${bubbleClass}">
                        <div class="flex items-center justify-between gap-3 mb-1 text-[10px] ${headerClass}">
                            <span>${senderLabel}</span>
                            <div class="flex items-center gap-1.5">
                                ${editedBadge}
                                <span>${m.created_at}</span>
                                ${actionBtns}
                            </div>
                        </div>
                        ${msgBody}
                    </div>
                </div>
            `;
        });
        messagesList.innerHTML = html;
    }
</script>
@endsection
