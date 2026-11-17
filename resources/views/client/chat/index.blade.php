@extends('layouts.client')

@section('title', 'محادثاتي مع ملّاك الشقق - منصة دَارِك')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-6 shadow-xs border border-slate-200">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-500/20 flex-shrink-0">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900">محادثاتي مع المُلّاك</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    سجل محادثاتك المباشرة مع أصحاب الشقق لمتابعة المفاوضات والحجوزات
                </p>
            </div>
        </div>

        @if(!empty($phone))
            <div class="flex items-center gap-2 self-start sm:self-center">
                <span class="text-xs text-slate-500">الرقم الحالي:</span>
                <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200" dir="ltr">
                    {{ $phone }}
                </span>
                <button type="button" onclick="document.getElementById('lookupSection').classList.toggle('hidden')" 
                        class="text-xs font-bold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-xl border border-amber-200 transition">
                    <i class="fa-solid fa-arrows-rotate ml-1"></i> تغيير الرقم
                </button>
            </div>
        @endif
    </div>

    <!-- Phone Lookup Card (Shown if no phone or when toggled) -->
    <div id="lookupSection" class="{{ empty($phone) || $conversations->isEmpty() ? '' : 'hidden' }} bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-amber-500/30">
        <div class="max-w-xl mx-auto text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-2xl border border-amber-400/30">
                <i class="fa-solid fa-phone-volume"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-white">استرجاع محادثاتك السابقة</h3>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                    أدخل رقم الجوال الذي استخدمته عند بدء المحادثة مع أي مالك شقة لعرض كافة محادثاتك فوراً من أي هاتف أو جهاز كمبيوتر.
                </p>
            </div>

            <form action="{{ route('client.chat.lookup') }}" method="POST" class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto pt-2">
                @csrf
                <div class="relative flex-1">
                    <input type="text" name="phone" value="{{ old('phone', $phone) }}" required dir="ltr"
                           placeholder="05xxxxxxxx"
                           class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 text-left">
                </div>
                <button type="submit" class="py-3 px-6 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>عرض محادثاتي</span>
                </button>
            </form>

            <div class="pt-3 text-[11px] text-slate-400 flex items-center justify-center gap-2 border-t border-white/10">
                <i class="fa-solid fa-shield-halved text-amber-400"></i>
                <span>خصوصية تامة: لا يمكن لأي نزيل آخر الاطلاع على محادثاتك أو رسائلك.</span>
            </div>
        </div>
    </div>

    <!-- Conversations List -->
    @if($conversations->isNotEmpty())
        <div class="space-y-3">
            <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                <span>المحادثات النشطة والسابقة</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">{{ $conversations->count() }}</span>
            </h2>

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
                                    <img src="{{ asset('storage/' . $cover) }}" alt="شقة {{ $apt?->apartment_number }}" class="w-full h-full object-cover">
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
    @elseif(!empty($phone))
        <div class="bg-white rounded-3xl p-8 text-center space-y-3 border border-slate-200">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <h3 class="font-bold text-sm text-slate-800">لا توجد محادثات سابقة مسجلة برقم ({{ $phone }})</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                إذا قمت ببدء محادثة برقم آخر، يمكنك إدخاله بالأعلى، أو يمكنك تصفح الشقق وبدء محادثة مباشرة مع أي مالك شقة.
            </p>
            <div class="pt-3">
                <a href="{{ route('client.apartments.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-xs">
                    <i class="fa-solid fa-hotel"></i>
                    <span>تصفح الشقق المتاحة</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
