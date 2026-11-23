<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Investor, Setting, Subscription, Wallet};
use App\Services\{AuditService, NotificationService, WalletService};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $query = Investor::with(['user', 'apartments', 'wallet']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $investors = $query->latest()->paginate(15);
        $statusCounts = [
            'all' => Investor::count(),
            'pending' => Investor::where('status', 'pending')->count(),
            'approved' => Investor::where('status', 'approved')->count(),
            'suspended' => Investor::where('status', 'suspended')->count(),
            'rejected' => Investor::where('status', 'rejected')->count(),
        ];

        return view('admin.investors.index', compact('investors', 'statusCounts'));
    }

    public function show(Investor $investor)
    {
        $investor->load([
            'user',
            'apartments.media',
            'wallet.transactions' => fn($q) => $q->latest()->take(10),
            'subscriptions' => fn($q) => $q->latest(),
        ]);

        // Get confirmed bookings for this investor's apartments (critical during suspension review)
        $apartmentIds = $investor->apartments->pluck('id');
        $confirmedBookings = \App\Models\Booking::whereIn('apartment_id', $apartmentIds)
            ->where('status', 'confirmed')
            ->where('checkout_date', '>=', now())
            ->get();

        return view('admin.investors.show', compact('investor', 'confirmedBookings'));
    }

    public function approve(Investor $investor)
    {
        $old = $investor->toArray();
        $investor->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
            'suspension_reason' => null,
        ]);

        // Ensure wallet exists
        if (!$investor->wallet) {
            Wallet::create(['investor_id' => $investor->id, 'balance' => 0]);
        }

        // Give initial 30 days trial/active subscription if none exists
        if (!$investor->activeSubscription) {
            $monthlyFee = (float) Setting::getValue('monthly_subscription', 500);
            Subscription::create([
                'investor_id' => $investor->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'amount' => $monthlyFee,
                'status' => 'active',
                'paid_at' => now(),
                'payment_reference' => 'INITIAL_APPROVAL',
            ]);
        }

        // Activate investor's apartments
        $investor->apartments()->update(['is_active' => true]);

        AuditService::log('investor_approved', $investor, $old, $investor->toArray());

        NotificationService::sendWhatsApp(
            $investor->whatsapp,
            "🎉 تهانينا! تم اعتماد حسابك كمستثمر في منصة دارك.\nيرجى تسجيل الدخول وشحن محفظتك المالية في الموقع حتى تظهر شققك للعملاء وتبدأ في استقبال الحجوزات.\n(رسوم المنصة: 150 ريال لكل تأكيد حجز، و 100 ريال لكل عقد إيجار).",
            'investor',
            'investor',
            $investor->id
        );

        return back()->with('success', 'تم اعتماد المستثمر وتفعيل شققه بنجاح.');
    }

    public function reject(Request $request, Investor $investor)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'يرجى ذكر سبب الرفض لتوضيحه للمستثمر',
        ]);

        $old = $investor->toArray();
        $investor->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Deactivate apartments
        $investor->apartments()->update(['is_active' => false]);

        AuditService::log('investor_rejected', $investor, $old, $investor->toArray());

        NotificationService::sendWhatsApp(
            $investor->whatsapp,
            "⚠️ نود إفادتك بأنه تعذر اعتماد طلبك كمستثمر للأسباب التالية:\n{$request->rejection_reason}\nيمكنك استكمال النواقص وإعادة التقديم.",
            'investor',
            'investor',
            $investor->id
        );

        return back()->with('success', 'تم رفض طلب المستثمر وتسجيل سبب الرفض.');
    }

    public function suspend(Request $request, Investor $investor)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:1000',
        ], [
            'suspension_reason.required' => 'يرجى تسجيل سبب الإيقاف',
        ]);

        $old = $investor->toArray();
        $investor->update([
            'status' => 'suspended',
            'suspension_reason' => $request->suspension_reason,
            'suspension_date' => now(),
        ]);

        // Hide apartments from receiving NEW bookings
        $investor->apartments()->update(['is_active' => false]);

        // Note: As required by specification, confirmed bookings are NOT automatically cancelled!
        AuditService::log('investor_suspended', $investor, $old, $investor->toArray());

        NotificationService::sendWhatsApp(
            $investor->whatsapp,
            "⚠️ تم إيقاف استقبال الحجوزات الجديدة لحسابك للأسباب التالية:\n{$request->suspension_reason}\nيرجى التواصل مع إدارة البرج لمراجعة الحساب.",
            'investor',
            'investor',
            $investor->id
        );

        return back()->with('success', 'تم إيقاف المستثمر وإخفاء شققه عن الحجوزات الجديدة. الحجوزات المؤكدة السابقة ما زالت قائمة للمعالجة الإدارية.');
    }

    public function reactivate(Investor $investor)
    {
        $old = $investor->toArray();
        $investor->update([
            'status' => 'approved',
            'suspension_reason' => null,
            'suspension_date' => null,
        ]);

        $investor->apartments()->update(['is_active' => true]);

        AuditService::log('investor_reactivated', $investor, $old, $investor->toArray());

        return back()->with('success', 'تم إعادة تفعيل المستثمر وتنشيط شققه.');
    }

    public function addBalance(Request $request, Investor $investor, WalletService $walletService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'required|string|max:255',
        ], [
            'amount.required' => 'المبلغ مطلوب',
            'notes.required' => 'يرجى توضيح سبب الإيداع أو المرجع',
        ]);

        $wallet = $investor->wallet ?? Wallet::create(['investor_id' => $investor->id, 'balance' => 0]);
        $walletService->deposit($wallet, $request->amount, $request->notes, auth()->id());

        AuditService::log('wallet_balance_added', $investor, null, [
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);

        NotificationService::sendWhatsApp(
            $investor->whatsapp,
            "💳 تم شحن محفظتك بمبلغ {$request->amount} ريال بنجاح.\nالرصيد الحالي: {$wallet->balance} ريال.",
            'investor',
            'wallet',
            $wallet->id
        );

        return back()->with('success', 'تم شحن رصيد المحفظة بنجاح.');
    }

    /**
     * Toggle admin approval override for an investor:
     * Allows investor apartments to show even if suspended or subscription is expired.
     */
    public function toggleAdminApproval(Investor $investor)
    {
        $newVal = !$investor->admin_approved_override;
        $investor->update(['admin_approved_override' => $newVal]);

        AuditService::log('investor_admin_approval_toggled', $investor, null, ['admin_approved_override' => $newVal]);

        $msg = $newVal 
            ? "تم منح موافقة الإدارة الاستثنائية؛ ستظهر شقق المستثمر للنزلاء حتى في حال تعليق الحساب أو انتهاء الاشتراك." 
            : "تم إلغاء موافقة الإدارة الاستثنائية؛ لن تظهر شققه إلا إذا كان حسابه سارياً واشتراكه نشطاً.";

        return back()->with('success', $msg);
    }

    /**
     * Admin password reset for investor
     */
    public function resetPassword(Request $request, Investor $investor)
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ], [
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'new_password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات.',
        ]);

        $user = $investor->user;
        $user->password = Hash::make($request->new_password);
        $user->save();

        AuditService::log('investor_password_reset_by_admin', $investor, null, [
            'admin_id' => auth()->id(),
            'investor_id' => $investor->id,
            'investor_name' => $investor->full_name,
        ]);

        // Send WhatsApp notification with new credentials
        NotificationService::sendWhatsApp(
            $investor->whatsapp,
            "🔐 تم إعادة تعيين كلمة المرور لحسابك في منصة دارك بناءً على طلبك.\nالبريد المسجل: {$user->email}\nكلمة المرور الجديدة: {$request->new_password}\nيرجى تسجيل الدخول وتغييرها من ملفك الشخصي إذا أردت.",
            'investor',
            'investor',
            $investor->id
        );

        return back()->with('success', "تم تعيين كلمة المرور الجديدة للمستثمر ({$investor->full_name}) بنجاح وإشعار المستثمر فورياً عبر واتساب.");
    }
}
