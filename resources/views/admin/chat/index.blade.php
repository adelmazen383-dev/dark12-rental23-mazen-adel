@extends('layouts.admin')

@section('title', 'مراقبة وإشراف المحادثات')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">مراقبة محادثات النزلاء والملاك</h1>
            <p class="text-xs text-slate-500 mt-1">اطلاع الإدارة المركزي على كافة المحادثات الجارية بين المستأجرين والملاك لضمان جودة التعامل</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إجمالي قنوات المحادثة</span>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalConversations }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إجمالي الرسائل المتبادلة</span>
                <p class="text-2xl font-black text-indigo-600 mt-0.5">{{ $totalMessages }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">حالة الرقابة والإشراف</span>
                <p class="text-lg font-black text-emerald-600 mt-0.5">مفعلة ونشطة 100%</p>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.chats.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">بحث باسم النزيل، رقم الهاتف أو الموضوع</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم النزيل، رقم الهاتف..." class="w-full pl-3 pr-9 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">المستثمر (المالك)</label>
                <select name="investor_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">جميع المستثمرين</option>
                    @foreach($investors as $inv)
                        <option value="{{ $inv->id }}" {{ request('investor_id') == $inv->id ? 'selected' : '' }}>{{ $inv->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition text-center">
                    تصفية
                </button>
                <a href="{{ route('admin.chats.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Conversations Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">النزيل (المستأجر)</th>
                        <th class="py-3.5 px-4">المالك (المستثمر)</th>
                        <th class="py-3.5 px-4">الشقة</th>
                        <th class="py-3.5 px-4">آخر رسالة</th>
                        <th class="py-3.5 px-4">آخر نشاط</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الاطلاع والمراقبة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($conversations as $c)
                        @php
                            $lastMsg = $c->latestMessage;
                            $unreadAdmin = $c->unreadMessagesForAdminCount();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition {{ $c->has_bypass_attempt ? 'bg-rose-50/50' : '' }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="font-bold text-slate-900">{{ $c->customer_name }}</p>
                                    @if($c->has_bypass_attempt)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-rose-600 text-white animate-pulse" title="{{ $c->bypass_attempt_reason }}">🚨 تحايل</span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $c->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $c->investor?->full_name ?? 'إدارة البرج' }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $c->investor?->phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-700">
                                شقة {{ $c->apartment->apartment_number }} (الدور {{ $c->apartment->floor_number }})
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                @if($lastMsg)
                                    <strong class="text-slate-800">{{ $lastMsg->sender_name }}:</strong> {{ $lastMsg->message }}
                                @else
                                    <span class="text-slate-400">لا توجد رسائل بعد</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $c->last_message_at ? $c->last_message_at->diffForHumans() : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($c->status === 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">مفتوحة</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">مغلقة</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.chats.show', $c->id) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>متابعة المحادثة</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-comments text-4xl mb-2 text-slate-300"></i>
                                <p class="text-sm">لا توجد أي محادثات مسجلة بالبرج حتى الآن</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($conversations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $conversations->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
