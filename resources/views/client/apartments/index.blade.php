@extends('layouts.client')

@section('title', 'منصة دارك لتنظيم الإيجار - تصفح الوحدات المتاحة')

@section('content')
<div class="space-y-6">
    <!-- Hero / Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950 text-white p-6 sm:p-8 shadow-xl border border-amber-500/20">
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-white p-1 shadow-xl border-2 border-amber-400 flex-shrink-0">
                <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-xl">
            </div>

            <div class="flex-1 text-center md:text-right space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    <i class="fa-solid fa-shield-halved text-amber-400"></i>
                    <span>منصة رسمية معتمدة لحماية وتوثيق الإيجار</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    منصة دَارِك لتنظيم الإيجار
                </h1>
                <p class="text-sm text-slate-300 leading-relaxed max-w-2xl">
                    منصة متخصصة في تنظيم عمليات تأجير الشقق والوحدات السكنية، وتسهيل وصول العملاء إلى الوحدات المتاحة بكل سهولة وأمان.
                </p>
            </div>
        </div>

        <div class="absolute -left-12 -bottom-12 w-64 h-64 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
    </div>

    <!-- Security Advisory & Platform Mission Banner -->
    <div class="bg-gradient-to-r from-amber-50 via-amber-50/60 to-white rounded-2xl border-2 border-amber-300/80 p-5 sm:p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1 space-y-3">
                <div>
                    <h3 class="font-black text-base text-amber-950 flex items-center gap-2">
                        <span>تنويه أمني للعملاء:</span>
                    </h3>
                    <p class="text-xs text-amber-800 mt-0.5">يرجى قراءة الإرشادات التالية بعناية لضمان سلامة التعامل وأمان حجزك:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs text-slate-800">
                    <div class="flex items-center gap-2 bg-white/80 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-circle-exclamation text-amber-600 flex-shrink-0"></i>
                        <span>احذر من المحتالين والحسابات الوهمية.</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/80 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 flex-shrink-0"></i>
                        <span class="font-bold text-amber-950">التعامل والحجز يكون عبر منصة دارك فقط.</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/80 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-ban text-rose-600 flex-shrink-0"></i>
                        <span>عدم تحويل أي مبالغ إلى حسابات شخصية غير معتمدة.</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/80 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-building-circle-check text-sky-600 flex-shrink-0"></i>
                        <span>التأكد من بيانات الوحدة ورقمها قبل إتمام الحجز.</span>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex items-center gap-2 bg-amber-100/70 p-2.5 rounded-xl border border-amber-300 text-amber-900 font-bold">
                        <i class="fa-solid fa-stamp text-amber-700 flex-shrink-0"></i>
                        <span>المنصة هي القناة المعتمدة لتنظيم ومتابعة عمليات الإيجار.</span>
                    </div>
                </div>

                <!-- Mission Statement -->
                <div class="pt-2 border-t border-amber-200/70 flex items-center gap-2 text-xs font-bold text-amber-900">
                    <i class="fa-solid fa-bullseye text-amber-600"></i>
                    <span>هدفنا: تنظيم الإيجار، وحماية العملاء، وتعزيز الثقة والأمان في جميع عمليات الحجز.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <form action="{{ route('client.apartments.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
            <div>
                <label class="block font-semibold text-gray-600 mb-1">الدور</label>
                <select name="floor" class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">جميع الأدوار</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>الدور {{ $floor }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-gray-600 mb-1">رقم الشقة</label>
                <input type="text" name="apartment_number" value="{{ request('apartment_number') }}" placeholder="رقم الشقة..."
                    class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-gray-600 mb-1">عدد الغرف</label>
                <select name="rooms" class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">أي عدد</option>
                    <option value="1" {{ request('rooms') == 1 ? 'selected' : '' }}>غرفة واحدة فأكثر</option>
                    <option value="2" {{ request('rooms') == 2 ? 'selected' : '' }}>غرفتان فأكثر</option>
                    <option value="3" {{ request('rooms') == 3 ? 'selected' : '' }}>3 غرف فأكثر</option>
                </select>
            </div>

            <div class="flex items-end gap-1.5 col-span-2 sm:col-span-1">
                <button type="submit" class="flex-1 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl transition flex items-center justify-center gap-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>تصفية</span>
                </button>
                @if(request()->hasAny(['floor', 'apartment_number', 'rooms']))
                    <a href="{{ route('client.apartments.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition" title="إلغاء التصفية">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Apartments Grid -->
    @if($apartments->isEmpty())
        <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto flex items-center justify-center text-gray-400 text-2xl mb-3">
                <i class="fa-solid fa-door-closed"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800">لا توجد شقق متاحة حالياً وفق هذه المعايير</h3>
            <p class="text-xs text-gray-500 mt-1">يرجى تعديل معايير البحث أو اختيار دور آخر.</p>
            <a href="{{ route('client.apartments.index') }}" class="inline-block mt-4 text-xs font-bold text-amber-600 bg-amber-50 px-4 py-2 rounded-xl">
                عرض جميع الشقق
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($apartments as $apt)
                @php
                    $coverImage = $apt->images->first();
                    $hasVideo = $apt->videos->isNotEmpty();
                @endphp
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col group">
                    <!-- Image / Preview -->
                    <div class="relative h-48 bg-slate-900 overflow-hidden">
                        @if($coverImage)
                            <img src="{{ asset('storage/' . $coverImage->file_path) }}" alt="شقة {{ $apt->apartment_number }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-500 bg-gradient-to-b from-slate-800 to-slate-900">
                                <i class="fa-solid fa-building text-3xl mb-1 text-slate-600"></i>
                                <span class="text-xs">جاري تجهيز الصور</span>
                            </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-3 right-3 flex flex-wrap gap-1.5">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-slate-900/85 backdrop-blur-md text-amber-400 shadow">
                                شقة {{ $apt->apartment_number }}
                            </span>
                            <span class="px-2 py-1 rounded-lg text-xs font-bold bg-white/90 backdrop-blur-md text-gray-800 shadow">
                                الدور {{ $apt->floor_number }}
                            </span>
                        </div>

                        @if($hasVideo)
                            <div class="absolute bottom-3 right-3">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-600/90 backdrop-blur-md text-white flex items-center gap-1 shadow">
                                    <i class="fa-solid fa-circle-play animate-pulse"></i>
                                    <span>فيديو متوفر</span>
                                </span>
                            </div>
                        @endif

                        <div class="absolute bottom-3 left-3">
                            @if($apt->is_featured_on_homepage)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500 text-slate-950 shadow flex items-center gap-1">
                                    <i class="fa-solid fa-crown text-[9px]"></i> مختارة ومميزة
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500 text-white shadow">
                                    متاحة الآن
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-3">
                        <div class="space-y-3">
                            <!-- Specifications Row -->
                            <div class="flex items-center flex-wrap gap-2 text-xs text-gray-700 pb-2.5 border-b border-gray-100">
                                <span class="flex items-center gap-1 font-bold bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                    <i class="fa-solid fa-bed text-amber-500"></i> {{ $apt->rooms_count }} غرف
                                </span>
                                @if($apt->living_rooms_count)
                                    <span class="flex items-center gap-1 font-bold bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                        <i class="fa-solid fa-couch text-amber-500"></i> {{ $apt->living_rooms_count }} صالة
                                    </span>
                                @endif
                                <span class="flex items-center gap-1 font-bold bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                    <i class="fa-solid fa-bath text-amber-500"></i> {{ $apt->bathrooms_count }} حمام
                                </span>
                                @if($apt->view_type)
                                    <span class="text-slate-500 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100 truncate max-w-[150px]" title="{{ $apt->view_type }}">
                                        <i class="fa-solid fa-mountain-sun text-slate-400 ml-1"></i>{{ $apt->view_type }}
                                    </span>
                                @endif
                            </div>

                            <!-- Features List (if available) -->
                            @if($apt->features)
                                <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-slate-600">
                                    @foreach(array_slice(array_map('trim', explode(',', $apt->features)), 0, 3) as $feat)
                                        <span class="px-2 py-0.5 rounded-md bg-amber-50/70 border border-amber-200/60 text-amber-900 font-medium">
                                            ✓ {{ $feat }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Price Row -->
                            <div class="flex items-baseline justify-between pt-1">
                                <div>
                                    <span class="text-xs text-slate-500 block">سعر الإقامة:</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-black text-slate-900">{{ number_format($apt->daily_price, 0) }}</span>
                                        <span class="text-xs font-bold text-amber-700">ريال / ليلة</span>
                                    </div>
                                </div>
                                <div class="text-left text-xs">
                                    <span class="text-slate-400 block text-[10px]">تأمين مسترد</span>
                                    <span class="font-bold text-slate-700 font-mono">{{ number_format($apt->insurance_amount, 0) }} ر.س</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons (Identical & Visible on Mobile and Desktop) -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                            <a href="{{ route('client.apartments.show', $apt->id) }}" class="py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-eye"></i>
                                <span>تفاصيل وحجز</span>
                            </a>
                            <a href="{{ route('client.apartments.show', $apt->id) }}#chatOption" class="py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-comments"></i>
                                <span>محادثة المالك</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $apartments->links() }}
        </div>
    @endif
</div>
@endsection
