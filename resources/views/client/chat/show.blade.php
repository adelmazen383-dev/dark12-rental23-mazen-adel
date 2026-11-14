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

        <a href="{{ route('client.apartments.show', $conversation->apartment_id) }}" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition text-xs flex items-center gap-1" title="عرض تفاصيل الشقة">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    {{-- ===== DEAL STATUS BADGE ===== --}}
    @php
        $dealStatus = $conversation->deal_status;
        $statusMap = [
            'negotiating'    => ['bg-slate-100 text-slate-700',   'fa-comments-dollar', 'قيد التفاوض'],
            'offer_sent'     => ['bg-amber-100 text-amber-800',   'fa-file-invoice',    'وصلك عرض رسمي من المالك — راجعه أدناه'],
            'confirmed'      => ['bg-emerald-100 text-emerald-800','fa-circle-check',   'تم تأكيد حجزك بنجاح ✅'],
            'cancelled'      => ['bg-rose-100 text-rose-800',     'fa-xmark-circle',    'تم إلغاء الصفقة'],
            'caught_offline' => ['bg-red-100 text-red-900',       'fa-triangle-exclamation','⚠️ تم رصد محاولة تحايل — راجع الإدارة'],
        ];
        [$badgeClass, $badgeIcon, $badgeLabel] = $statusMap[$dealStatus] ?? ['bg-slate-100 text-slate-600','fa-circle','غير معروف'];
    @endphp
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $badgeClass }} text-xs font-bold border border-slate-200/80 shadow-sm flex-shrink-0">
        <i class="fa-solid {{ $badgeIcon }}"></i>
        <span>حالة المحادثة: {{ $badgeLabel }}</span>
    </div>

    {{-- ===== OFFER ACCEPTANCE CARD ===== --}}
    @if($dealStatus === 'offer_sent' && $conversation->offer_price && $conversation->offer_checkin_date && $conversation->offer_checkout_date)
    @php
        $nights = \Carbon\Carbon::parse($conversation->offer_checkin_date)
                    ->diffInDays(\Carbon\Carbon::parse($conversation->offer_checkout_date));
    @endphp
    <div class="bg-amber-50 border-2 border-amber-400 rounded-2xl shadow-md p-5 flex-shrink-0">
        <div class="flex items-center gap-2 mb-3">
            <i class="fa-solid fa-file-invoice-dollar text-amber-600 text-xl"></i>
            <p class="font-black text-sm text-amber-900">📋 عرض حجز رسمي من المالك</p>
        </div>
        <div class="grid grid-cols-2 gap-3 text-xs mb-4">
            <div class="bg-white rounded-xl p-3 border border-amber-200">
                <span class="text-slate-500 block mb-0.5">السعر الإجمالي</span>
                <strong class="text-amber-700 text-base font-black">{{ number_format($conversation->offer_price, 2) }} ر.س</strong>
            </div>
            <div class="bg-white rounded-xl p-3 border border-amber-200">
                <span class="text-slate-500 block mb-0.5">مدة الإقامة</span>
                <strong class="text-slate-800 font-bold">{{ $nights }} ليلة</strong>
            </div>
            <div class="bg-white rounded-xl p-3 border border-amber-200">
                <span class="text-slate-500 block mb-0.5">تاريخ الدخول</span>
                <strong class="text-slate-800 font-bold">{{ $conversation->offer_checkin_date }}</strong>
            </div>
            <div class="bg-white rounded-xl p-3 border border-amber-200">
                <span class="text-slate-500 block mb-0.5">تاريخ الخروج</span>
                <strong class="text-slate-800 font-bold">{{ $conversation->offer_checkout_date }}</strong>
            </div>
            @if($conversation->offer_notes)
            <div class="col-span-2 bg-white rounded-xl p-3 border border-amber-200">
                <span class="text-slate-500 block mb-0.5">ملاحظات المالك</span>
                <strong class="text-slate-700">{{ $conversation->offer_notes }}</strong>
            </div>
            @endif
        </div>
        <form action="{{ route('client.chat.accept-offer', $conversation->customer_token) }}" method="POST">
            @csrf
            <button type="submit"
                    onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟ سيتم تأكيد الحجز فورًا.')"
                    class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl transition flex items-center justify-center gap-2 shadow-md text-sm">
                <i class="fa-solid fa-circle-check"></i>
                قبول العرض وتأكيد الحجز الآن
            </button>
        </form>
        <p class="text-[10px] text-slate-500 text-center mt-2">
            <i class="fa-solid fa-shield-halved ml-1"></i>
            الحجز مضمون ومحمي من قِبَل إدارة البرج. ستصلك شيفرة الاستقبال فور التأكيد.
        </p>
    </div>
    @endif

    {{-- ===== CONFIRMED BOOKING INFO ===== --}}
    @if($dealStatus === 'confirmed' && $conversation->booking)
    <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-4 text-xs flex items-start gap-3 shadow-sm flex-shrink-0">
        <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mt-0.5"></i>
        <div>
            <p class="font-black text-sm text-emerald-900">🎉 تم تأكيد حجزك بنجاح!</p>
            <p class="mt-1 text-emerald-800">رقم الحجز: <strong>#{{ $conversation->booking->reference_number }}</strong></p>
            <div class="mt-2 bg-white rounded-xl p-3 border border-emerald-200 inline-block">
                <span class="text-slate-500 text-[10px] block mb-0.5">شيفرة الاستقبال (أبرزها عند الوصول)</span>
                <strong class="font-mono text-2xl tracking-widest text-emerald-700">{{ $conversation->booking->reception_code }}</strong>
            </div>
            <p class="mt-1 text-slate-500">الدخول: {{ $conversation->booking->checkin_date }} | الخروج: {{ $conversation->booking->checkout_date }}</p>
        </div>
    </div>
    @endif

    {{-- ===== SURVEY WIDGET (client) ===== --}}
    @if(in_array($dealStatus, ['confirmed', 'negotiating']) && !$conversation->client_survey_response)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex-shrink-0">
        <p class="text-xs font-bold text-slate-700 mb-3">
            <i class="fa-solid fa-clipboard-question text-amber-500 ml-1"></i>
            هل اتفقت مع المالك خارج المنصة؟ إجابتك سرية وتحمي حقوقك.
        </p>
        <form action="{{ route('client.chat.survey', $conversation->customer_token) }}" method="POST" class="flex gap-3">
            @csrf
            <button type="submit" name="response" value="deal_done"
                    class="flex-1 py-2 bg-amber-100 hover:bg-amber-200 text-amber-800 font-bold rounded-xl text-xs transition border border-amber-300">
                ✅ نعم، تم الحجز عبر المنصة
            </button>
            <button type="submit" name="response" value="no_deal"
                    class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border border-slate-300">
                ❌ لم يحدث أي شيء
            </button>
        </form>
    </div>
    @endif

    <!-- Chat Messages Window -->
    <div id="messagesWindow" class="flex-1 bg-white rounded-2xl border border-slate-200 p-4 shadow-sm overflow-y-auto space-y-3 flex flex-col">
        <div class="text-center my-2">
            <span class="px-3 py-1 rounded-full text-[10px] bg-slate-100 text-slate-500 font-medium">
                بداية المحادثة الآمنة بينك وبين مالك الشقة
            </span>
        </div>

        <div id="messagesList" class="space-y-3 flex-1 flex flex-col justify-end">
            @forelse($conversation->messages as $msg)
                <div class="flex flex-col {{ $msg->sender_type === 'customer' ? 'items-start' : 'items-end' }}">
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
                            <span>{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <p class="whitespace-pre-line leading-relaxed">{{ $msg->message }}</p>
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

    function scrollToBottom() {
        messagesWindow.scrollTop = messagesWindow.scrollHeight;
    }
    scrollToBottom();

    // AJAX form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        // Instantly append temporary message
        const tempDiv = document.createElement('div');
        tempDiv.className = 'flex flex-col items-start';
        tempDiv.innerHTML = `
            <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl p-3 text-xs shadow-sm bg-amber-500 text-slate-950 font-medium rounded-br-none">
                <div class="flex items-center justify-between gap-3 mb-1 text-[10px] text-slate-900/80 font-bold">
                    <span><i class="fa-solid fa-user ml-1"></i> أنت</span>
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

            html += `
                <div class="flex flex-col ${alignClass}">
                    <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl p-3 text-xs shadow-sm ${bubbleClass}">
                        <div class="flex items-center justify-between gap-3 mb-1 text-[10px] ${headerClass}">
                            <span>${senderLabel}</span>
                            <span>${m.created_at}</span>
                        </div>
                        <p class="whitespace-pre-line leading-relaxed">${m.message.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
                    </div>
                </div>
            `;
        });
        messagesList.innerHTML = html;
    }
</script>
@endsection
