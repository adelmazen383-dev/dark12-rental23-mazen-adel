@extends('layouts.admin')

@section('title', 'إدارة الشقق والأدوار')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">شقق وأجنحة البرج</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة جميع الوحدات السكنية والفندقية وتصنيفاتها وصورها وفيديوهاتها</p>
        </div>
        <a href="{{ route('admin.apartments.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>إضافة شقة جديدة</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.apartments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-600 mb-1">الدور</label>
                <select name="floor" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">جميع الأدوار</option>
                    @foreach($floors as $fl)
                        <option value="{{ $fl }}" {{ request('floor') == $fl ? 'selected' : '' }}>الدور {{ $fl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">المستثمر</label>
                <select name="investor_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">جميع المستثمرين</option>
                    @foreach($investors as $inv)
                        <option value="{{ $inv->id }}" {{ request('investor_id') == $inv->id ? 'selected' : '' }}>{{ $inv->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">حالة الظهور</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">الكل</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة وتظهر للنزلاء</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطلة / مخفية</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">الصفحة الأولى (سري) ⭐</label>
                <select name="featured" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">الكل</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>مثبتة بالصفحة الأولى</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>غير مثبتة</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition">
                    تصفية
                </button>
                @if(request()->hasAny(['floor', 'investor_id', 'status', 'featured']))
                    <a href="{{ route('admin.apartments.index') }}" class="p-2 bg-slate-100 text-slate-600 rounded-xl">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">الشقة / الدور</th>
                        <th class="p-3.5">المستثمر</th>
                        <th class="p-3.5">المواصفات</th>
                        <th class="p-3.5">سعر الليلة والتأمين</th>
                        <th class="p-3.5">الوسائط</th>
                        <th class="p-3.5">الصفحة الأولى (سري)</th>
                        <th class="p-3.5">حالة التفعيل</th>
                        <th class="p-3.5">الظهور للنزلاء (الإتاحة)</th>
                        <th class="p-3.5 text-center">موافقة الإدارة</th>
                        <th class="p-3.5 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($apartments as $apt)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3.5">
                                <div class="font-black text-slate-900 text-sm">شقة {{ $apt->apartment_number }}</div>
                                <div class="text-[11px] text-slate-500">الدور {{ $apt->floor_number }}</div>
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('admin.investors.show', $apt->investor_id) }}" class="font-bold text-amber-700 hover:underline">
                                    {{ $apt->investor->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400" dir="ltr">{{ $apt->investor->phone }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-800">{{ $apt->rooms_count }} غرف • {{ $apt->bathrooms_count }} حمام</span>
                                <div class="text-[10px] text-slate-500">{{ $apt->view_type ?: 'إطلالة عادية' }}</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-black text-emerald-700 text-sm">{{ number_format($apt->daily_price, 2) }} ر.س</div>
                                <div class="text-[10px] text-slate-500">تأمين: {{ number_format($apt->insurance_amount, 2) }} ر.س</div>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[11px]">
                                        <i class="fa-solid fa-image text-slate-400"></i> {{ $apt->images->count() }}
                                    </span>
                                    @if($apt->videos->isNotEmpty())
                                        <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-bold text-[11px]">
                                            <i class="fa-solid fa-video text-rose-500"></i> فيديو
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <form action="{{ route('admin.apartments.toggle-featured', $apt->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition flex items-center gap-1 {{ $apt->is_featured_on_homepage ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}" title="تحديد ظهور الشقة في واجهة الصفحة الأولى دون علم صاحب الشقة">
                                        <i class="fa-solid fa-star {{ $apt->is_featured_on_homepage ? 'text-amber-500' : 'text-slate-300' }}"></i>
                                        <span>{{ $apt->is_featured_on_homepage ? 'مثبتة بالواجهة' : 'عادي' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="p-3.5">
                                <form action="{{ route('admin.apartments.toggle-active', $apt->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $apt->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $apt->is_active ? 'نشطة (مفعلة)' : 'مخفية (معطلة)' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-3.5">
                                @if($apt->isCurrentlyRented())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-key text-[9px]"></i> مؤجرة حالياً
                                    </span>
                                @elseif(!$apt->is_available)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> غير متاحة
                                    </span>
                                @elseif(!$apt->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye-slash text-[9px]"></i> معطلة إدارياً
                                    </span>
                                @elseif($apt->investor && !$apt->investor->isAllowedToDisplayApartments() && !$apt->admin_approved_override)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> مخفية (إيقاف/اشتراك)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> تظهر للنزلاء
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <form action="{{ route('admin.apartments.toggle-admin-approval', $apt->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold transition {{ $apt->admin_approved_override ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}" title="منح أو إلغاء موافقة الإدارة الاستثنائية للظهور">
                                        {{ $apt->admin_approved_override ? 'موافقة مفعلة' : 'عادي' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.apartments.show', $apt->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg" title="عرض">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.apartments.edit', $apt->id) }}" class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.apartments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشقة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg" title="حذف">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500">لا توجد شقق مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $apartments->links() }}
        </div>
    </div>

</div>
@endsection
