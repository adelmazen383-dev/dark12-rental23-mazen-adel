@extends('layouts.investor')

@section('title', 'محادثات ورسائل النزلاء')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">محادثات ورسائل النزلاء المباشرة</h1>
            <p class="text-xs text-slate-500 mt-1">التواصل المباشر والرد على استفسارات العملاء والنزلاء المهتمين بشققك</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('investor.chats.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-600 mb-1">بحث باسم النزيل أو رقم الجوال</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم النزيل، الهاتف..." class="w-full pl-3 pr-9 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-emerald-500">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">تصفية حسب الشقة</label>
                <select name="apartment_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-emerald-500">
                    <option value="">جميع الشقق</option>
                    @foreach($apartments as $apt)
                        <option value="{{ $apt->id }}" {{ request('apartment_id') == $apt->id ? 'selected' : '' }}>
                            شقة {{ $apt->apartment_number }} (الدور {{ $apt->floor_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition text-center">
                    تصفية
                </button>
                <a href="{{ route('investor.chats.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Conversations List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($conversations as $c)
                @php
                    $unread = $c->unreadMessagesForInvestorCount();
                    $lastMsg = $c->latestMessage;
                    $apt = $c->apartment;
                    $cover = $apt?->media?->first()?->file_path;
                @endphp
                <a href="{{ route('investor.chats.show', $c->id) }}" class="p-4 hover:bg-slate-50/80 transition flex items-start justify-between gap-4 block">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 shadow-xs">
                            @if($cover)
                                <img src="{{ asset('storage/' . $cover) }}" alt="شقة {{ $apt?->apartment_number }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-50">
                                    <i class="fa-solid fa-hotel text-lg text-emerald-600"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h3 class="font-bold text-sm text-slate-900 truncate">{{ $c->customer_name }}</h3>
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    شقة {{ $apt?->apartment_number }} (الدور {{ $apt?->floor_number }})
                                </span>
                                <span class="text-[10px] font-semibold text-slate-500">
                                    {{ number_format($apt?->daily_price ?? 0) }} ريال/اليوم
                                </span>
                                @if($unread > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse">
                                        {{ $unread }} جديدة
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-1">
                                @if($lastMsg)
                                    <strong class="text-slate-700">{{ $lastMsg->sender_name }}:</strong> 
                                    {{ $lastMsg->is_deleted ? 'تم حذف هذه الرسالة' : $lastMsg->message }}
                                @else
                                    <em>بدء المحادثة</em>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-left flex-shrink-0 text-xs">
                        <span class="text-[11px] text-slate-400 block">{{ $c->last_message_at ? $c->last_message_at->diffForHumans() : '-' }}</span>
                        <span class="inline-block mt-1 text-emerald-600 font-bold hover:underline">
                            فتح الدردشة <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center text-slate-400">
                    <i class="fa-solid fa-comments text-4xl mb-2 text-slate-300"></i>
                    <p class="text-sm">لا توجد محادثات أو رسائل من النزلاء حتى الآن</p>
                </div>
            @endforelse
        </div>

        @if($conversations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $conversations->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
