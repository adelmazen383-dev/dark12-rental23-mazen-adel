@extends('layouts.admin')

@section('title', 'سجل التدقيق والرقابة المركزية')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل تدقيق ورقابة العمليات (Audit Trail)</h1>
            <p class="text-xs text-slate-500 mt-1">سجل أمني مفصل باللغة العربية يوضح كل إجراء، من نفذه، الطرف المتأثر، والتفاصيل الدقيقة مع التاريخ وعنوان IP</p>
        </div>
        <div class="text-xs bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-xs text-slate-600 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>الرقابة التلقائية مفعلة ومحمية من التعديل</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">الوقت والتاريخ</th>
                        <th class="p-4">المسؤول عن الإجراء</th>
                        <th class="p-4">نوع العملية والإجراء</th>
                        <th class="p-4">السجل / الوحدة المستهدفة</th>
                        <th class="p-4">التفاصيل والبيانات المسجلة</th>
                        <th class="p-4">عنوان IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-amber-50/20 transition">
                            <!-- Timestamp -->
                            <td class="p-4 text-slate-600 whitespace-nowrap">
                                <span class="font-bold text-slate-900 block font-mono text-[11px]">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- User -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs">
                                        <i class="fa-solid {{ $log->user?->role === 'admin' ? 'fa-shield-halved text-amber-600' : 'fa-user text-slate-500' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $log->user?->name ?: 'النظام التلقائي / زائر' }}</p>
                                        <span class="text-[10px] text-slate-400">
                                            @if($log->user?->role === 'admin')
                                                مشرف النظام
                                            @elseif($log->user?->role === 'investor')
                                                مستثمر (مالك شقة)
                                            @else
                                                عملية آمنة
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Action Badge -->
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-bold text-xs border {{ $log->action_badge_color }}">
                                    <span>{{ $log->action_label }}</span>
                                </span>
                            </td>

                            <!-- Affected Model -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold text-xs border border-slate-200/80">
                                    {{ $log->model_label }}
                                </span>
                            </td>

                            <!-- Human Details -->
                            <td class="p-4 text-slate-700 max-w-sm">
                                @if($log->new_values && is_array($log->new_values))
                                    <div class="space-y-1">
                                        @foreach($log->new_values as $key => $val)
                                            @if(!is_array($val) && !is_null($val) && $val !== '')
                                                <div class="text-[11px] flex items-baseline gap-1.5">
                                                    <span class="text-slate-400 font-semibold">{{ $key }}:</span>
                                                    <span class="font-bold text-slate-900 break-all">{{ is_bool($val) ? ($val ? 'نعم' : 'لا') : $val }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($log->old_values && is_array($log->old_values))
                                    <span class="text-slate-500 text-[11px]">تم تعديل بيانات السجل</span>
                                @else
                                    <span class="text-slate-400 text-xs">تم التوثيق بنجاح بدون تعديل قيم</span>
                                @endif
                            </td>

                            <!-- IP -->
                            <td class="p-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                <span class="bg-slate-50 px-2 py-1 rounded border border-slate-200">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-check text-4xl mb-2 text-slate-300"></i>
                                <p class="text-sm">لا توجد سجلات تدقيق حتى الآن.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
