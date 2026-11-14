<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, InspectionRequest};
use App\Services\{AuditService, NotificationService};
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function store(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ], [
            'customer_name.required' => 'اسم العميل مطلوب',
            'customer_phone.required' => 'رقم الجوال مطلوب',
        ]);

        $inspection = InspectionRequest::create([
            'apartment_id' => $apartment->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditService::log('inspection_requested', $apartment, null, [
            'customer' => $inspection->customer_name,
            'phone' => $inspection->customer_phone,
        ]);

        // WhatsApp notification to investor & admin
        NotificationService::notifyInspectionRequest($inspection);

        return back()->with('success', 'تم إرسال طلب المعاينة بنجاح! سيتم التواصل معك من قِبل إدارة البرج وصاحب الشقة لتنسيق الموعد المناسب.');
    }
}
