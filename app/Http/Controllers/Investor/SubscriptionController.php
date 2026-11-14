<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{PlatformBankAccount, Setting};
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;
        $subscriptions = $investor->subscriptions()->latest()->paginate(10);
        $activeSubscription = $investor->activeSubscription;
        $bankAccounts = PlatformBankAccount::active()->get();
        $monthlyFee = Setting::getValue('monthly_subscription', 500);

        return view('investor.subscription.index', compact('investor', 'subscriptions', 'activeSubscription', 'bankAccounts', 'monthlyFee'));
    }
}
