@extends('layouts.admin')

@section('title', 'إدارة طلبات المعاينة')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">طلبات معاينة الشقق</h1>
            <p class="text-xs text-slate-500 mt-1">الطلبات المرسلة من النزلاء عبر الباركود لرؤية الشقة قبل تأكيد الحجز</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.inspections.index') }}" method="GET" class="flex gap-3 text-xs">
            <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                <option value="">جميع الحالات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>بانتظار التنسيق</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>تم تحديد موعد</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>تمت المعاينة</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl">تصفية</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">العميل</th>
                        <th class="p-3.5">الشقة والدور</th>
                        <th class="p-3.5">المستثمر</th>
                        <th class="p-3.5">ملاحظات العميل</th>
                        <th class="p-3.5">الموعد المحدد</th>
                        <th class="p-3.5">الحالة</th>
                        <th class="p-3.5 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($inspections as $ins)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900">{{ $ins->customer_name }}</div>
                                <div class="text-[10px] text-slate-500" dir="ltr">{{ $ins->customer_phone }}</div>
                            </td>
                            <td class="p-3.5">شقة {{ $ins->apartment->apartment_number }} (الدور {{ $ins->apartment->floor_number }})</td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-800">{{ $ins->apartment->investor->full_name }}</span>
                                <span class="text-[10px] text-slate-400 block" dir="ltr">{{ $ins->apartment->investor->whatsapp }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs">{{ $ins->notes ?: '—' }}</td>
                            <td class="p-3.5">
                                @if($ins->scheduled_at)
                                    <span class="font-bold text-blue-700">{{ $ins->scheduled_at->format('Y-m-d h:i A') }}</span>
                                @else
                                    <span class="text-slate-400">لم يُحدد</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($ins->status === 'completed')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">مكتملة</span>
                                @elseif($ins->status === 'scheduled')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">مجدولة</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">بانتظار التنسيق</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openScheduleModal({{ $ins->id }})" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg font-bold">
                                        تحديد موعد
                                    </button>
                                    @if($ins->status !== 'completed')
                                        <form action="{{ route('admin.inspections.complete', $ins->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg font-bold">
                                                تمت
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">لا توجد طلبات معاينة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $inspections->links() }}
        </div>
    </div>

</div>

<!-- Modal: Schedule Inspection -->
<div id="scheduleModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-slate-900">تحديد موعد المعاينة</h3>
            <button onclick="document.getElementById('scheduleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="scheduleForm" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ ووقت المعاينة *</label>
                <input type="datetime-local" name="scheduled_at" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات التنسيق</label>
                <input type="text" name="notes" placeholder="تم التواصل مع النزيل والاستقبال..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('scheduleModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold">حفظ الموعد</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openScheduleModal(id) {
        document.getElementById('scheduleForm').action = `/admin/inspections/${id}/schedule`;
        document.getElementById('scheduleModal').classList.remove('hidden');
    }
</script>
@endsection
