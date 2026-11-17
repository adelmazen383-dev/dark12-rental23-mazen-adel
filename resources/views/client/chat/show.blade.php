@extends('layouts.client')

@section('title', 'محادثة المالك - شقة ' . $conversation->apartment->apartment_number)

@section('content')
<div class="max-w-2xl mx-auto px-2 sm:px-4 py-3 flex-1 flex flex-col h-[calc(100vh-5rem)]">

    <!-- Chat Header Card -->
    <div class="bg-white rounded-2xl p-3.5 border border-slate-200 shadow-sm flex items-center justify-between mb-3 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg flex-shrink-0 font-bold border border-amber-500/20">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-sm text-slate-900">
                        محادثة صاحب شقة {{ $conversation->apartment->apartment_number }} (الدور {{ $conversation->apartment->floor_number }})
                    </h2>
                    @if($conversation->status === 'open')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            مباشر
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">مغلقة</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    المالك: <strong class="text-slate-700">{{ $conversation->investor?->full_name ?? 'إدارة البرج' }}</strong> | مرحباً بك يا <strong class="text-amber-600">{{ $conversation->customer_name }}</strong>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('client.chat.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5" title="قائمة كل محادثاتي">
                <i class="fa-solid fa-comments text-amber-500"></i>
                <span class="hidden sm:inline">كل محادثاتي</span>
            </a>
            <a href="{{ route('client.apartments.show', $conversation->apartment_id) }}" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition text-xs flex items-center gap-1" title="عرض تفاصيل الشقة">
                <i class="fa-solid fa-hotel text-slate-500"></i>
            </a>
        </div>
    </div>

    {{-- ===== DEAL STATUS BADGE ===== --}}
    @php
        $dealStatus = $conversation->deal_status;
        $statusMap = [
            'negotiating'    => ['bg-slate-100 text-slate-700',   'fa-comments',        'محادثة جارية ومباشرة'],
            'confirmed'      => ['bg-emerald-100 text-emerald-800','fa-circle-check',   'تم تأكيد حجزك بنجاح ✅'],
            'cancelled'      => ['bg-rose-100 text-rose-800',     'fa-xmark-circle',    'تم إلغاء الصفقة'],
            'caught_offline' => ['bg-red-100 text-red-900',       'fa-triangle-exclamation','⚠️ تنبيه نظام'],
        ];
        [$badgeClass, $badgeIcon, $badgeLabel] = $statusMap[$dealStatus] ?? ['bg-slate-100 text-slate-600','fa-circle','محادثة نشطة'];
    @endphp
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $badgeClass }} text-xs font-bold border border-slate-200/80 shadow-sm flex-shrink-0">
        <i class="fa-solid {{ $badgeIcon }}"></i>
        <span>حالة المحادثة: {{ $badgeLabel }}</span>
    </div>

    {{-- ===== CONFIRMED BOOKING INFO ===== --}}
    @if($dealStatus === 'confirmed' && $conversation->booking)
    <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-4 text-xs flex items-start gap-3 shadow-sm flex-shrink-0">
        <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mt-0.5"></i>
        <div>
            <p class="font-black text-sm text-emerald-900">🎉 تم تأكيد حجزك بنجاح!</p>
            <p class="mt-1 text-emerald-800">رقم الحجز: <strong>#{{ $conversation->booking->reference_number }}</strong></p>
            <div class="mt-2 bg-white rounded-xl p-3 border border-emerald-200 inline-block">
                <span class="text-slate-500 text-[10px] block mb-0.5">كود استلام المفتاح من الاستقبال</span>
                <strong class="font-mono text-2xl tracking-widest text-emerald-700">{{ $conversation->booking->reception_code }}</strong>
            </div>
            <p class="mt-1 text-slate-500">الدخول: {{ $conversation->booking->checkin_date }} | الخروج: {{ $conversation->booking->checkout_date }}</p>
        </div>
    </div>
    @endif

    <!-- Chat Messages Window -->
    <div id="messagesWindow" class="flex-1 bg-white rounded-2xl border border-slate-200 p-4 shadow-sm overflow-y-auto space-y-3 flex flex-col">
        <div class="text-center my-2">
            <span class="px-3 py-1 rounded-full text-[10px] bg-slate-100 text-slate-500 font-medium">
                بداية المحادثة الآمنة والمباشرة مع مالك الشقة
            </span>
        </div>

        <div id="messagesList" class="space-y-3 flex-1 flex flex-col justify-end">
            @forelse($conversation->messages as $msg)
                <div class="flex flex-col {{ $msg->sender_type === 'customer' ? 'items-start' : 'items-end' }}" id="msg-box-{{ $msg->id }}">
                    <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl p-3 text-xs shadow-sm
                        @if($msg->sender_type === 'customer')
                            bg-amber-500 text-slate-950 font-medium rounded-br-none
                        @elseif($msg->sender_type === 'investor')
                            bg-slate-100 text-slate-900 border border-slate-200 rounded-bl-none
                        @else
                            bg-indigo-600 text-white rounded-bl-none
                        @endif">
                        <div class="flex items-center justify-between gap-3 mb-1 text-[10px] {{ $msg->sender_type === 'customer' ? 'text-slate-900/80 font-bold' : ($msg->sender_type === 'admin' ? 'text-indigo-200' : 'text-slate-500') }}">
                            <span>
                                @if($msg->sender_type === 'investor')
                                    <i class="fa-solid fa-user-tie ml-1"></i> المالك ({{ $msg->sender_name }})
                                @elseif($msg->sender_type === 'admin')
                                    <i class="fa-solid fa-shield-halved ml-1"></i> إدارة البرج
                                @else
                                    <i class="fa-solid fa-user ml-1"></i> أنت
                                @endif
                            </span>
                            <div class="flex items-center gap-1.5">
                                @if($msg->is_edited && !$msg->is_deleted)
                                    <span class="text-[9px] opacity-80">(معدّلة)</span>
                                @endif
                                <span>{{ $msg->created_at->format('H:i') }}</span>
                                @if($msg->sender_type === 'customer' && !$msg->is_deleted)
                                    <button onclick="editMsg({{ $msg->id }}, '{{ addslashes($msg->message) }}')" class="hover:text-amber-800 text-slate-950 ml-1" title="تعديل">
                                        <i class="fa-solid fa-pencil text-[10px]"></i>
                                    </button>
                                    <button onclick="deleteMsg({{ $msg->id }})" class="hover:text-rose-700 text-slate-950" title="حذف">
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
                    <p class="text-xs">لا توجد رسائل بعد. اكتب رسالتك بالأسفل للتواصل مع صاحب الشقة.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Message Input Bar -->
    <div class="mt-3 bg-white rounded-2xl p-2 border border-slate-200 shadow-sm flex-shrink-0">
        <form id="chatForm" action="{{ route('client.chat.send', $conversation->customer_token) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input type="text" id="messageInput" name="message" required placeholder="اكتب استفسارك أو رسالتك لمالك الشقة..." autocomplete="off" class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-xs text-slate-900">
            <button type="submit" id="sendBtn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <span>إرسال</span>
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </form>
    </div>

</div>

<script>
    const token = @json($conversation->customer_token);
    const messagesWindow = document.getElementById('messagesWindow');
    const messagesList = document.getElementById('messagesList');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendUrl = @json(route('client.chat.send', $conversation->customer_token));
    const getUrl = @json(route('client.chat.messages', $conversation->customer_token));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Clear any previous persistent tokens for strict privacy
    try {
        localStorage.removeItem('dark_last_chat_token');
        localStorage.removeItem('dark_last_chat_apt');
    } catch(e) {}

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

        fetch(`/chat/${token}/messages/${msgId}`, {
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

        fetch(`/chat/${token}/messages/${msgId}`, {
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
            const isMe = m.sender_type === 'customer';
            const isAdmin = m.sender_type === 'admin';
            const isInvestor = m.sender_type === 'investor';

            let bubbleClass = isMe ? 'bg-amber-500 text-slate-950 font-medium rounded-br-none'
                            : (isAdmin ? 'bg-indigo-600 text-white rounded-bl-none'
                            : 'bg-slate-100 text-slate-900 border border-slate-200 rounded-bl-none');
            let alignClass = isMe ? 'items-start' : 'items-end';
            let headerClass = isMe ? 'text-slate-900/80 font-bold'
                            : (isAdmin ? 'text-indigo-200' : 'text-slate-500');

            let senderLabel = isMe ? '<i class="fa-solid fa-user ml-1"></i> أنت'
                            : (isAdmin ? '<i class="fa-solid fa-shield-halved ml-1"></i> إدارة البرج'
                            : `<i class="fa-solid fa-user-tie ml-1"></i> المالك (${m.sender_name})`);

            let actionBtns = '';
            if (isMe && !m.is_deleted) {
                const escaped = m.message.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                actionBtns = `
                    <button onclick="editMsg(${m.id}, '${escaped}')" class="hover:text-amber-800 text-slate-950 ml-1" title="تعديل">
                        <i class="fa-solid fa-pencil text-[10px]"></i>
                    </button>
                    <button onclick="deleteMsg(${m.id})" class="hover:text-rose-700 text-slate-950" title="حذف">
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
                    <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl p-3 text-xs shadow-sm ${bubbleClass}">
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
