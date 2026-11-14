<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رسمية - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-border { border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 antialiased text-slate-800">

    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="javascript:history.back()" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold transition hover:bg-slate-800 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة</span>
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md">
            <i class="fa-solid fa-print"></i>
            <span>طباعة الفاتورة</span>
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-slate-200 print-border">
        <!-- Header -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center text-2xl font-bold shadow-md">
                    <i class="fa-solid fa-hotel"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black text-slate-900">{{ $siteName }}</h1>
                    <p class="text-xs text-slate-500 font-semibold">فاتورة ضريبية / معتمدة</p>
                </div>
            </div>

            <div class="text-left">
                <div class="text-[10px] text-slate-400 font-bold">رقم الفاتورة</div>
                <div class="text-base font-black text-slate-900 font-mono">{{ $invoice->invoice_number }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">تاريخ الإصدار: {{ $invoice->issue_date->format('Y-m-d') }}</div>
                @if($invoice->due_date)
                    <div class="text-[10px] text-slate-500">تاريخ الاستحقاق: {{ $invoice->due_date->format('Y-m-d') }}</div>
                @endif
            </div>
        </div>

        <!-- Billed to & Billed by -->
        <div class="grid grid-cols-2 gap-6 my-6 text-xs">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">فاتورة إلى (العميل / المستأجر):</span>
                <div class="font-black text-sm text-slate-900">{{ $invoice->client_name }}</div>
                @if($invoice->client_phone)
                    <div class="text-slate-600" dir="ltr">جوال: {{ $invoice->client_phone }}</div>
                @endif
                @if($invoice->client_tax_number)
                    <div class="text-slate-600 font-mono">الرقم الضريبي: {{ $invoice->client_tax_number }}</div>
                @endif
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">الجهة المصدرة:</span>
                <div class="font-black text-sm text-slate-900">{{ $invoice->investor ? $invoice->investor->full_name : $siteName }}</div>
                @if($invoice->investor)
                    <div class="text-slate-600">ترخيص سياحي: {{ $invoice->investor->license_number ?: 'معتمد' }}</div>
                    <div class="text-slate-600" dir="ltr">جوال: {{ $invoice->investor->phone }}</div>
                @else
                    <div class="text-slate-600">إدارة منصة البرج المعتمدة</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="my-6 border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-900 text-white font-bold">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">بيان الخدمة / الوحدة</th>
                        <th class="p-3 text-center">الكمية</th>
                        <th class="p-3 text-left">سعر الوحدة</th>
                        <th class="p-3 text-left">الإجمالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($invoice->items ?? [] as $idx => $item)
                        <tr>
                            <td class="p-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ $item['description'] ?? 'خدمة' }}</td>
                            <td class="p-3 text-center font-mono">{{ $item['quantity'] ?? 1 }}</td>
                            <td class="p-3 text-left font-mono">{{ number_format($item['unit_price'] ?? 0, 2) }} ر.س</td>
                            <td class="p-3 text-left font-mono font-bold text-slate-900">{{ number_format($item['total'] ?? 0, 2) }} ر.س</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end my-6">
            <div class="w-full sm:w-72 space-y-2 text-xs border border-slate-200 rounded-xl p-4 bg-slate-50">
                <div class="flex justify-between text-slate-600">
                    <span>المجموع الفرعي:</span>
                    <span class="font-mono font-bold">{{ number_format($invoice->subtotal, 2) }} ر.س</span>
                </div>
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>ضريبة القيمة المضافة:</span>
                        <span class="font-mono font-bold">{{ number_format($invoice->tax_amount, 2) }} ر.س</span>
                    </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-slate-300 font-black text-sm text-slate-900">
                    <span>المجموع الكلي:</span>
                    <span class="text-emerald-700 font-mono">{{ number_format($invoice->total_amount, 2) }} ر.س</span>
                </div>
                <div class="pt-2 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black 
                        @if($invoice->status === 'paid') bg-emerald-100 text-emerald-800
                        @elseif($invoice->status === 'cancelled') bg-rose-100 text-rose-800
                        @else bg-blue-100 text-blue-800 @endif">
                        حالة الفاتورة: {{ $invoice->status_label }}
                    </span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                <strong>ملاحظات:</strong> {{ $invoice->notes }}
            </div>
        @endif

        <div class="mt-8 pt-4 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-400">
            <span>تم إصدار هذه الفاتورة إلكترونياً وهي معتمدة وموثقة.</span>
            <span>المرجع: {{ substr(md5($invoice->invoice_number), 0, 8) }}</span>
        </div>
    </div>

</body>
</html>
