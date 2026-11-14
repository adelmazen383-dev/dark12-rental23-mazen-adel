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
                <p class="text-xs text-slate-500 mt-0.5 font-mono">
                    <i class="fa-solid fa-phone text-slate-400 ml-1"></i> {{ $conversation->customer_phone }}
                    @if($conversation->booking)
                        | <span class="text-emerald-700 font-bold font-sans">مرتبط بحجز #{{ $conversation->booking->reference_number }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="https://wa.me/{{ preg_replace('/^0/', '966', $conversation->customer_phone) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition flex items-center gap-1.5 border border-emerald-200">
                <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                <span>واتساب النزيل</span>
            </a>
            <a href="{{ route('investor.chats.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                العودة
            </a>
        </div>
    </div>

    {{-- ===== DEAL STATUS BADGE ===== --}}
    @php
        $dealStatus = $conversation->deal_status;
        $statusMap = [
            'negotiating'    => ['bg-slate-100 text-slate-700',   'fa-comments-dollar', 'قيد التفاوض'],
            'offer_sent'     => ['bg-amber-100 text-amber-800',   'fa-file-invoice',    'عرض رسمي مُرسَل — بانتظار موافقة النزيل'],
            'confirmed'      => ['bg-emerald-100 text-emerald-800','fa-circle-check',   'تم الحجز بشكل رسمي ✅'],
            'cancelled'      => ['bg-rose-100 text-rose-800',     'fa-xmark-circle',    'تم إلغاء الصفقة'],
            'caught_offline' => ['bg-red-100 text-red-900',       'fa-triangle-exclamation','⚠️ تم رصد محاولة تحايل — قيد المراجعة الإدارية'],
        ];
        [$badgeClass, $badgeIcon, $badgeLabel] = $statusMap[$dealStatus] ?? ['bg-slate-100 text-slate-600','fa-circle','غير معروف'];
    @endphp
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $badgeClass }} text-xs font-bold border border-slate-200/80 shadow-sm">
        <i class="fa-solid {{ $badgeIcon }}"></i>
        <span>حالة الصفقة: {{ $badgeLabel }}</span>
    </div>

    {{-- ===== BYPASS ALERT (investor view) ===== --}}
    @if($conversation->has_bypass_attempt)
    <div class="bg-rose-50 border border-rose-300 rounded-2xl p-4 text-xs text-rose-800 flex items-start gap-3 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl mt-0.5"></i>
        <div>
            <p class="font-black text-sm text-rose-900">⚠️ تنبيه أمني — محاولة تجاوز العمولة</p>
            <p class="mt-1 text-rose-700">{{ $conversation->bypass_attempt_reason }}</p>
            <p class="mt-1 text-rose-500">الرجاء الالتزام بسياسات المنصة وإجراء جميع الصفقات عبر النظام الرسمي.</p>
        </div>
    </div>
    @endif

    {{-- ===== MAKE OFFER FORM ===== --}}
    @if(in_array($dealStatus, ['negotiating', 'offer_sent']) && !$conversation->booking_id)
    <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
        <button onclick="document.getElementById('offerPanel').classList.toggle('hidden')"
                class="w-full flex items-center justify-between px-5 py-3.5 text-sm font-black text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition">
            <span><i class="fa-solid fa-file-invoice-dollar ml-2"></i> إرسال عرض حجز رسمي للنزيل</span>
            <i class="fa-solid fa-chevron-down text-emerald-500"></i>
        </button>
        <div id="offerPanel" class="{{ $dealStatus === 'offer_sent' ? '' : 'hidden' }} p-5">
            @if($dealStatus === 'offer_sent')
            <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                <strong>⏳ يوجد عرض مُرسَل بالفعل:</strong>
                السعر: <strong>{{ number_format($conversation->offer_price, 2) }} ر.س</strong> |
                الدخول: <strong>{{ $conversation->offer_checkin_date }}</strong> |
                الخروج: <strong>{{ $conversation->offer_checkout_date }}</strong><br>
                @if($conversation->offer_notes) ملاحظات: {{ $conversation->offer_notes }} @endif
                <p class="mt-1 text-amber-600">يمكنك إرسال عرض جديد لتحديث الشروط.</p>
            </div>
            @endif

            <form action="{{ route('investor.chats.offer', $conversation->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">السعر الإجمالي للإقامة (ر.س) <span class="text-rose-500">*</span></label>
                    <input type="number" name="offer_price" min="1" step="0.01"
                           value="{{ old('offer_price', $conversation->offer_price) }}"
                           placeholder="مثال: 1500.00"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-400 outline-none" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الدخول <span class="text-rose-500">*</span></label>
                    <input type="date" name="offer_checkin_date"
                           value="{{ old('offer_checkin_date', $conversation->offer_checkin_date) }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-400 outline-none" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الخروج <span class="text-rose-500">*</span></label>
                    <input type="date" name="offer_checkout_date"
                           value="{{ old('offer_checkout_date', $conversation->offer_checkout_date) }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-400 outline-none" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">ملاحظات العرض (اختياري)</label>
                    <input type="text" name="offer_notes"
                           value="{{ old('offer_notes', $conversation->offer_notes) }}"
                           placeholder="مثال: يشمل الفطار، أو بالسعر المتفق عليه"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-400 outline-none">
                </div>
                <div class="sm:col-span-2 flex gap-3 mt-1">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-paper-plane"></i>
                        إرسال العرض الرسمي للنزيل
                    </button>
                </div>
                <p class="sm:col-span-2 text-[10px] text-slate-400 -mt-2">
                    <i class="fa-solid fa-circle-info ml-1"></i>
                    عند قبول النزيل للعرض، يتم تأكيد الحجز تلقائيًا وخصم رسوم الحجز من محفظتك، ويُولَّد كود الاستقبال.
                </p>
            </form>
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
            <p class="mt-0.5 text-emerald-700">كود الاستقبال للنزيل: <strong class="font-mono text-lg tracking-widest">{{ $conversation->booking->reception_code }}</strong></p>
            <p class="mt-0.5 text-slate-500">الدخول: {{ $conversation->booking->checkin_date }} | الخروج: {{ $conversation->booking->checkout_date }}</p>
        </div>
    </div>
    @endif

    {{-- ===== SURVEY WIDGET (investor) ===== --}}
    @if(in_array($dealStatus, ['confirmed', 'negotiating']) && !$conversation->investor_survey_response)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <p class="text-xs font-bold text-slate-700 mb-3">
            <i class="fa-solid fa-clipboard-question text-amber-500 ml-1"></i>
            هل تمت صفقة إيجار مع هذا النزيل؟ (يُساعدنا في ضمان حقوقك)
        </p>
        <form action="{{ route('investor.chats.survey', $conversation->id) }}" method="POST" class="flex gap-3">
            @csrf
            <button type="submit" name="response" value="deal_done"
                    class="flex-1 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold rounded-xl text-xs transition border border-emerald-300">
                ✅ نعم، تمت الصفقة بشكل رسمي
            </button>
            <button type="submit" name="response" value="no_deal"
                    class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border border-slate-300">
                ❌ لا، لم تتم أي صفقة
            </button>
        </form>
    </div>
    @endif

    <!-- Chat Messages Window -->
    <div id="messagesWindow" class="h-[520px] bg-white rounded-2xl border border-slate-200 p-4 shadow-sm overflow-y-auto space-y-3 flex flex-col">
        <div class="text-center my-1">
            <span class="px-3 py-1 rounded-full text-[10px] bg-slate-100 text-slate-500 font-medium">
                سجل الرسائل المتبادلة مع النزيل (مراقب من الإدارة لضمان الجودة)
            </span>
        </div>

        <div id="messagesList" class="space-y-3 flex-1 flex flex-col justify-end">
            @forelse($conversation->messages as $msg)
                <div class="flex flex-col {{ $msg->sender_type === 'investor' ? 'items-start' : 'items-end' }}">
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
                            <span>{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <p class="whitespace-pre-line leading-relaxed">{{ $msg->message }}</p>
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
            <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm bg-emerald-600 text-white font-medium rounded-br-none">
                <div class="flex items-center justify-between gap-3 mb-1 text-[10px] text-emerald-200 font-bold">
                    <span><i class="fa-solid fa-user-tie ml-1"></i> أنت (المالك)</span>
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

            html += `
                <div class="flex flex-col ${alignClass}">
                    <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-sm ${bubbleClass}">
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
