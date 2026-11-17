@extends('layouts.client')

@section('title', 'محادثاتي مع ملّاك الشقق - منصة دَارِك')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(empty($phone))
        <!-- =========================================================
             DEFAULT PRIVATE STATE: NO PHONE STORED, NO CHATS VISIBLE
             ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 text-center space-y-6">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-3xl font-bold border border-amber-500/20">
                <i class="fa-solid fa-user-shield"></i>
            </div>

            <div class="max-w-md mx-auto space-y-2">
                <h1 class="text-2xl font-black text-slate-900">محادثاتي مع المُلّاك</h1>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    لحماية خصوصيتك وسرية مفاوضاتك، لا يتم تخزين المحادثات على الجهاز، وتختفي تلقائياً بمجرد مغادرة الموقع.
                </p>
            </div>

            <!-- Phone Entry Form -->
            <form action="{{ route('client.chat.lookup') }}" method="POST" class="max-w-md mx-auto space-y-3 pt-2">
                @csrf
                <div class="text-right">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">أدخل رقم جوالك لعرض محادثاتك *</label>
                    <div class="relative">
                        <input type="text" name="phone" required dir="ltr" autofocus
                               placeholder="05xxxxxxxx" autocomplete="off"
                               class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 text-base font-bold focus:outline-none focus:ring-2 focus:ring-amber-500 text-left bg-slate-50">
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">رقم الجوال الذي قمت باستخدامه عند مراسلة المالك</span>
                </div>

                <button type="submit" class="w-full py-3.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-2xl shadow-md transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-comments"></i>
                    <span>عرض محادثاتي المباشرة</span>
                </button>
            </form>

            <!-- Security Assurance -->
            <div class="max-w-lg mx-auto pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-right">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-2.5">
                    <i class="fa-solid fa-lock text-emerald-600 text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        <strong>خصوصية كاملة:</strong> لن يرى أي شخص آخر يستخدم هذا الجهاز رسائلك أو أرقامك.
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-start gap-2.5">
                    <i class="fa-solid fa-eye-slash text-amber-600 text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        <strong>إغلاق فوري:</strong> بمجرد الخروج من الصفحة يتم مسح الجلسة وتختفي الشاتات تماماً.
                    </p>
                </div>
            </div>
        </div>

    @else
        <!-- =========================================================
             ACTIVE SEARCH STATE: USER EXPLICITLY ENTERED THEIR PHONE
             ========================================================= -->
        <!-- Top Bar with Exit/Hide Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-5 shadow-xs border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-500/20 flex-shrink-0">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-black text-slate-900">محادثاتي مع المُلّاك</h1>
                        <span class="font-mono text-xs font-bold text-amber-900 bg-amber-50 px-2.5 py-0.5 rounded-lg border border-amber-200" dir="ltr">
                            {{ $phone }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        معروضة مؤقتاً في هذه الجلسة وتختفي بمجرد إغلاق الموقع
                    </p>
                </div>
            </div>

            <!-- Lock and Hide Button (Clears Screen Immediately) -->
            <a href="{{ route('client.chat.index') }}" 
               class="px-4 py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition flex items-center justify-center gap-2 self-start sm:self-center shadow-2xs">
                <i class="fa-solid fa-lock"></i>
                <span>إخفاء الشاتات والخروج فوراً</span>
            </a>
        </div>

        @if($conversations->isNotEmpty())
            <!-- Conversations Cards -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                        <span>المحادثات المتاحة لهذا الرقم:</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 text-[11px] font-bold">{{ $conversations->count() }}</span>
                    </h2>
                    <span class="text-[11px] text-slate-400">تحديث فوري</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($conversations as $conv)
                        @php
                            $unreadCount = $conv->unreadMessagesForCustomerCount();
                            $apt = $conv->apartment;
                            $cover = $apt?->media?->first()?->file_path ?? null;
                        @endphp
                        <div class="bg-white rounded-3xl p-5 shadow-xs border {{ $unreadCount > 0 ? 'border-amber-400 ring-2 ring-amber-400/20' : 'border-slate-200' }} hover:shadow-md transition duration-200 flex flex-col justify-between space-y-4">
                            
                            <!-- Apartment & Landlord Header -->
                            <div class="flex items-start gap-3.5">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                                    @if($cover)
                                        <img src="{{ asset('storage/' . $cover) }}" alt="شقة {{ $apt?->apartment_number }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('images/apartment-default.svg') }}';">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                            <i class="fa-solid fa-hotel text-xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-500/15 text-amber-900 border border-amber-300">
                                            شقة رقم {{ $apt?->apartment_number ?? '—' }} (الدور {{ $apt?->floor_number ?? '—' }})
                                        </span>
                                        @if($unreadCount > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white animate-pulse">
                                                <i class="fa-solid fa-bell"></i>
                                                <span>رد جديد ({{ $unreadCount }})</span>
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-slate-600 truncate">
                                        المالك: <strong class="text-slate-900">{{ $conv->investor?->full_name ?? 'إدارة البرج' }}</strong>
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $apt?->rooms_count ?? 1 }} غرف • {{ number_format($apt?->daily_price ?? 0) }} ريال/اليوم
                                    </p>
                                </div>
                            </div>

                            <!-- Last Message Preview -->
                            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 text-xs">
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                    <span>آخر رسالة:</span>
                                    <span>{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '—' }}</span>
                                </div>
                                <p class="text-slate-700 font-medium line-clamp-2">
                                    @if($conv->latestMessage)
                                        @if($conv->latestMessage->is_deleted)
                                            <span class="text-slate-400 italic">تم حذف هذه الرسالة</span>
                                        @else
                                            {{ $conv->latestMessage->message }}
                                        @endif
                                    @else
                                        <span class="text-slate-400">لا توجد رسائل بعد</span>
                                    @endif
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                @if($conv->deal_status === 'confirmed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>حجز مؤكد</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-700">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>محادثة جارية</span>
                                    </span>
                                @endif

                                <a href="{{ route('client.chat.show', $conv->customer_token) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition shadow-xs">
                                    <span>فتح المحادثة</span>
                                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- No chats found for this number -->
            <div class="bg-white rounded-3xl p-8 text-center space-y-4 border border-slate-200">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-sm text-slate-800">لا توجد محادثات مسجلة برقم (<span dir="ltr">{{ $phone }}</span>)</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        تأكد من كتابة نفس رقم الجوال الذي استخدمته عند بدء المحادثة مع صاحب الشقة.
                    </p>
                </div>
                <div class="pt-2 flex items-center justify-center gap-3">
                    <a href="{{ route('client.chat.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        تجربة رقم آخر
                    </a>
                    <a href="{{ route('client.apartments.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition">
                        تصفح الشقق
                    </a>
                </div>
            </div>
        @endif

    @endif

</div>
@endsection
