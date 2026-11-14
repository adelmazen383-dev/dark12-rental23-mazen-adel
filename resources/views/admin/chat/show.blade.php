@extends('layouts.admin')

@section('title', 'مراقبة محادثة: ' . $conversation->customer_name . ' - ' . $conversation->apartment->apartment_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Top info bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg flex-shrink-0 font-bold border border-amber-500/20">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-sm text-slate-900">
                        مراقبة محادثة: {{ $conversation->customer_name }} ↔ {{ $conversation->investor?->full_name ?? 'إدارة البرج' }}
                    </h2>
                    @if($conversation->status === 'open')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">قناة مفتوحة</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">قناة مغلقة</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    شقة {{ $conversation->apartment->apartment_number }} (الدور {{ $conversation->apartment->floor_number }}) | جوال النزيل: <span class="font-mono">{{ $conversation->customer_phone }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('admin.chats.toggle-status', $conversation->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                    {{ $conversation->status === 'open' ? 'إغلاق المحادثة' : 'إعادة فتح المحادثة' }}
                </button>
            </form>
            <a href="{{ route('admin.chats.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                العودة
            </a>
        </div>
    </div>

    {{-- ===== BYPASS ALERT BANNER ===== --}}
    @if($conversation->has_bypass_attempt)
    <div class="bg-rose-50 border-2 border-rose-400 rounded-2xl p-4 flex items-start gap-4 shadow-md">
        <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <p class="font-black text-rose-900 text-sm">🚨 تنبيه: محاولة تجاوز العمولة مرصودة</p>
                @php
                    $dealStatus = $conversation->deal_status;
                    $statusColors = [
                        'negotiating'    => 'bg-slate-100 text-slate-700',
                        'offer_sent'     => 'bg-amber-100 text-amber-800',
                        'confirmed'      => 'bg-emerald-100 text-emerald-800',
                        'caught_offline' => 'bg-red-100 text-red-900',
                        'cancelled'      => 'bg-rose-100 text-rose-800',
                    ];
                    $statusLabels = [
                        'negotiating'    => 'قيد التفاوض',
                        'offer_sent'     => 'عرض مُرسَل',
                        'confirmed'      => 'مؤكد رسميًا',
                        'caught_offline' => '⚠️ تحايل مرصود',
                        'cancelled'      => 'ملغي',
                    ];
                @endphp
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusColors[$dealStatus] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $statusLabels[$dealStatus] ?? $dealStatus }}
                </span>
            </div>
            <p class="mt-1.5 text-xs text-rose-700 font-medium">{{ $conversation->bypass_attempt_reason }}</p>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('admin.investors.show', $conversation->investor_id) }}"
                   class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs transition">
                    <i class="fa-solid fa-user-tie ml-1"></i> ملف المستثمر
                </a>
                <a href="{{ route('admin.chats.index', ['bypass_only' => 1]) }}"
                   class="px-3 py-1.5 bg-white border border-rose-300 text-rose-700 hover:bg-rose-50 font-bold rounded-lg text-xs transition">
                    عرض كل التنبيهات
                </a>
            </div>
        </div>
    </div>
    @else
    @php
        $dealStatus = $conversation->deal_status ?? 'negotiating';
        $statusColors = [
            'negotiating'    => 'bg-slate-100 text-slate-700',
            'offer_sent'     => 'bg-amber-100 text-amber-800',
            'confirmed'      => 'bg-emerald-100 text-emerald-800',
            'caught_offline' => 'bg-red-100 text-red-900',
            'cancelled'      => 'bg-rose-100 text-rose-800',
        ];
        $statusLabels = [
            'negotiating'    => 'قيد التفاوض',
            'offer_sent'     => 'عرض رسمي مُرسَل',
            'confirmed'      => 'تم تأكيد الحجز ✅',
            'caught_offline' => '⚠️ تحايل مرصود',
            'cancelled'      => 'ملغي',
        ];
    @endphp
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $statusColors[$dealStatus] ?? 'bg-slate-100 text-slate-600' }} text-xs font-bold border border-slate-200/80 shadow-sm">
        <i class="fa-solid fa-comments-dollar"></i>
        <span>حالة الصفقة: {{ $statusLabels[$dealStatus] ?? $dealStatus }}</span>
    </div>
    @endif

    <!-- Participants Quick Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3 flex items-center justify-between">
            <div>
                <span class="text-amber-800 font-bold block">طرف النزيل (المستأجر):</span>
                <span class="text-slate-900 font-semibold">{{ $conversation->customer_name }}</span>
            </div>
            <a href="https://wa.me/{{ preg_replace('/^0/', '966', $conversation->customer_phone) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-amber-300 text-amber-900 font-mono text-[11px] hover:bg-amber-100 transition">
                <i class="fa-brands fa-whatsapp text-emerald-600 ml-1"></i> {{ $conversation->customer_phone }}
            </a>
        </div>

        <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-xl p-3 flex items-center justify-between">
            <div>
                <span class="text-indigo-800 font-bold block">طرف المالك (المستثمر):</span>
                <span class="text-slate-900 font-semibold">{{ $conversation->investor?->full_name ?? 'إدارة البرج' }}</span>
            </div>
            @if($conversation->investor && $conversation->investor->phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '966', $conversation->investor->phone) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-300 text-indigo-900 font-mono text-[11px] hover:bg-indigo-100 transition">
                    <i class="fa-brands fa-whatsapp text-emerald-600 ml-1"></i> {{ $conversation->investor->phone }}
                </a>
            @endif
        </div>
    </div>

    <!-- Live Transcript Window -->
    <div id="messagesWindow" class="h-[500px] bg-white rounded-2xl border border-slate-200 p-4 shadow-sm overflow-y-auto space-y-3 flex flex-col">
        <div class="text-center my-1">
            <span class="px-3 py-1 rounded-full text-[10px] bg-slate-100 text-slate-500 font-medium">
                سجل المراقبة الإدارية المباشر - مراقبة كافة الرسائل لحظياً
            </span>
        </div>

        <div id="messagesList" class="space-y-3 flex-1 flex flex-col justify-end">
            @forelse($conversation->messages as $msg)
                <div class="flex flex-col {{ $msg->sender_type === 'admin' ? 'items-center' : ($msg->sender_type === 'investor' ? 'items-start' : 'items-end') }}">
                    <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm
                        @if($msg->sender_type === 'admin')
                            bg-indigo-700 text-white font-medium border border-indigo-600 text-center
                        @elseif($msg->sender_type === 'investor')
                            bg-emerald-50 text-slate-900 border border-emerald-200 rounded-br-none
                        @else
                            bg-amber-50 text-slate-900 border border-amber-200 rounded-bl-none
                        @endif">
                        <div class="flex items-center justify-between gap-4 mb-1 text-[10px] {{ $msg->sender_type === 'admin' ? 'text-indigo-200' : ($msg->sender_type === 'investor' ? 'text-emerald-700 font-bold' : 'text-amber-700 font-bold') }}">
                            <span>
                                @if($msg->sender_type === 'admin')
                                    <i class="fa-solid fa-shield-halved ml-1"></i> تدخل رسمي: إدارة البرج
                                @elseif($msg->sender_type === 'investor')
                                    <i class="fa-solid fa-user-tie ml-1"></i> المالك: {{ $msg->sender_name }}
                                @else
                                    <i class="fa-solid fa-user ml-1"></i> النزيل: {{ $msg->sender_name }}
                                @endif
                            </span>
                            <span class="font-mono text-slate-400">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="whitespace-pre-line leading-relaxed text-slate-800 {{ $msg->sender_type === 'admin' ? 'text-white' : '' }}">{{ $msg->message }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-slate-400">
                    <p class="text-xs">لا توجد رسائل سابقة في هذه المحادثة</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Administrative Intervention Input -->
    <div class="bg-white rounded-2xl p-3 border border-slate-200 shadow-sm">
        <div class="text-[11px] font-bold text-slate-700 mb-2 flex items-center gap-1.5">
            <i class="fa-solid fa-bullhorn text-amber-500"></i>
            <span>إرسال تنبيه أو تدخل رسمي من إدارة البرج يظهر للطرفين في المحادثة:</span>
        </div>
        <form id="chatForm" action="{{ route('admin.chats.send', $conversation->id) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <input type="text" id="messageInput" name="message" required placeholder="اكتب رسالة رسمية أو توضيح من إدارة البرج..." autocomplete="off" class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs text-slate-900">
            <button type="submit" id="sendBtn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <span>إرسال التدخل</span>
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
    const sendUrl = @json(route('admin.chats.send', $conversation->id));
    const getUrl = @json(route('admin.chats.messages', $conversation->id));

    function scrollToBottom() {
        messagesWindow.scrollTop = messagesWindow.scrollHeight;
    }
    scrollToBottom();

    // AJAX form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        const tempDiv = document.createElement('div');
        tempDiv.className = 'flex flex-col items-center';
        tempDiv.innerHTML = `
            <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm bg-indigo-700 text-white font-medium border border-indigo-600 text-center">
                <div class="flex items-center justify-between gap-4 mb-1 text-[10px] text-indigo-200">
                    <span><i class="fa-solid fa-shield-halved ml-1"></i> تدخل رسمي: إدارة البرج</span>
                    <span>الآن</span>
                </div>
                <p class="whitespace-pre-line leading-relaxed">${text.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
            </div>
        `;
        messagesList.appendChild(tempDiv);
        scrollToBottom();
        messageInput.value = '';

        fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        }).then(r => r.json()).catch(err => console.error(err));
    });

    // Polling every 3.5 seconds
    setInterval(() => {
        fetch(getUrl, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                renderMessages(data.messages);
            }
        })
        .catch(err => console.error(err));
    }, 3500);

    function renderMessages(messages) {
        let html = '';
        messages.forEach(m => {
            const isAdmin = m.sender_type === 'admin';
            const isInvestor = m.sender_type === 'investor';
            const isCustomer = m.sender_type === 'customer';

            let bubbleClass = isAdmin ? 'bg-indigo-700 text-white font-medium border border-indigo-600 text-center'
                            : (isInvestor ? 'bg-emerald-50 text-slate-900 border border-emerald-200 rounded-br-none'
                            : 'bg-amber-50 text-slate-900 border border-amber-200 rounded-bl-none');
            let alignClass = isAdmin ? 'items-center' : (isInvestor ? 'items-start' : 'items-end');
            let headerClass = isAdmin ? 'text-indigo-200' : (isInvestor ? 'text-emerald-700 font-bold' : 'text-amber-700 font-bold');

            let senderLabel = isAdmin ? '<i class="fa-solid fa-shield-halved ml-1"></i> تدخل رسمي: إدارة البرج'
                            : (isInvestor ? `<i class="fa-solid fa-user-tie ml-1"></i> المالك: ${m.sender_name}`
                            : `<i class="fa-solid fa-user ml-1"></i> النزيل: ${m.sender_name}`);

            html += `
                <div class="flex flex-col ${alignClass}">
                    <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm ${bubbleClass}">
                        <div class="flex items-center justify-between gap-4 mb-1 text-[10px] ${headerClass}">
                            <span>${senderLabel}</span>
                            <span class="font-mono text-slate-400">${m.created_at}</span>
                        </div>
                        <p class="whitespace-pre-line leading-relaxed ${isAdmin ? 'text-white' : 'text-slate-800'}">${m.message.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
                    </div>
                </div>
            `;
        });
        messagesList.innerHTML = html;
    }
</script>
@endsection
