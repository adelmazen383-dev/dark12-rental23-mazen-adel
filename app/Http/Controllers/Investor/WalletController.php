<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{PlatformBankAccount, Setting, Wallet};
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;
        $wallet = $investor->wallet ?? Wallet::create(['investor_id' => $investor->id, 'balance' => 0]);

        $transactions = $wallet->transactions()->latest()->paginate(15);
        $bankAccounts = PlatformBankAccount::active()->get();
        $bookingFee = Setting::getValue('booking_fee', 150);
        $lowThreshold = Setting::getValue('low_balance_threshold', 100);

        return view('investor.wallet.index', compact('wallet', 'transactions', 'bankAccounts', 'bookingFee', 'lowThreshold'));
    }
}
