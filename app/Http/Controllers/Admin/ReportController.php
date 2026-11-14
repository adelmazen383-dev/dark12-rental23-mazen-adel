<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Subscription, WalletTransaction};
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        // 1. Platform revenue from booking commissions/fees
        $bookingFeesRevenue = WalletTransaction::where('type', 'deduction')
            ->whereNotNull('booking_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // If not tied to booking_id, check description for booking fees
        if ($bookingFeesRevenue == 0) {
            $bookingFeesRevenue = WalletTransaction::where('type', 'deduction')
                ->where('description', 'like', '%حجز%')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');
        }

        // 2. Platform revenue from contract documentation service fees
        $contractFeesRevenue = WalletTransaction::where('type', 'deduction')
            ->where('description', 'like', '%عقد%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        if ($contractFeesRevenue == 0) {
            $contractFeesRevenue = \App\Models\RentalContract::whereBetween('created_at', [$startDate, $endDate])
                ->sum('service_fee');
        }

        // 3. Platform revenue from investor subscriptions
        $subscriptionRevenue = Subscription::whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // 4. Platform revenue from unregistered deal penalties / bypass fines
        $penaltyFeesRevenue = WalletTransaction::where('type', 'deduction')
            ->where(function($q) {
                $q->where('description', 'like', '%مخالفة%')
                  ->orWhere('description', 'like', '%تحايل%')
                  ->orWhere('description', 'like', '%غير مسجلة%');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Other platform deductions if any
        $totalDeductions = WalletTransaction::where('type', 'deduction')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 5. Total Refunds (returned platform fees)
        $totalRefunds = WalletTransaction::where('type', 'refund')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Total Net Platform Profit (what Darik platform actually earned)
        $netPlatformRevenue = ($totalDeductions + $subscriptionRevenue) - $totalRefunds;

        // Count of confirmed bookings managed by platform
        $confirmedBookingsCount = Booking::where('status', 'confirmed')
            ->whereBetween('confirmed_at', [$startDate, $endDate])
            ->count();

        // Count of documented contracts
        $contractsCount = \App\Models\RentalContract::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Daily breakdown
        $dailyTransactions = WalletTransaction::with(['wallet.investor', 'booking'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'bookingFeesRevenue',
            'contractFeesRevenue',
            'subscriptionRevenue',
            'penaltyFeesRevenue',
            'totalRefunds',
            'netPlatformRevenue',
            'confirmedBookingsCount',
            'contractsCount',
            'dailyTransactions'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $transactions = WalletTransaction::with(['wallet.investor', 'booking'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csv .= "التاريخ,المستثمر,النوع,المبلغ,الوصف,الرصيد بعد العملية\n";

        foreach ($transactions as $t) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $t->created_at->format('Y-m-d H:i'),
                $t->wallet->investor->full_name ?? '',
                $t->type_label,
                $t->amount,
                str_replace('"', '""', $t->description),
                $t->balance_after
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="financial_report_' . date('Y-m-d') . '.csv"');
    }
}
