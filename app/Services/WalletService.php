<?php
namespace App\Services;

use App\Models\{Wallet, WalletTransaction, Booking, Setting};
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function deposit(Wallet $wallet, float $amount, string $description, ?int $userId = null): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $amount, $description, $userId) {
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $amount,
                'description' => $description,
                'balance_after' => $wallet->balance,
                'created_by' => $userId,
            ]);
        });
    }

    public function deductBookingFee(Wallet $wallet, Booking $booking, ?int $userId = null): ?WalletTransaction
    {
        $fee = (float) Setting::getValue('booking_fee', 0);
        if ($fee <= 0) return null;

        return DB::transaction(function () use ($wallet, $fee, $booking, $userId) {
            if ($wallet->balance < $fee) {
                throw new \Exception('رصيد المحفظة غير كافي');
            }

            $wallet->decrement('balance', $fee);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deduction',
                'amount' => $fee,
                'description' => "رسوم حجز #{$booking->reference_number}",
                'booking_id' => $booking->id,
                'balance_after' => $wallet->balance,
                'created_by' => $userId,
            ]);
        });
    }

    public function refundBookingFee(Wallet $wallet, Booking $booking, ?int $userId = null): ?WalletTransaction
    {
        $transaction = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('booking_id', $booking->id)
            ->where('type', 'deduction')
            ->first();

        if (!$transaction) return null;

        return DB::transaction(function () use ($wallet, $transaction, $booking, $userId) {
            $wallet->increment('balance', $transaction->amount);
            $wallet->refresh();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $transaction->amount,
                'description' => "استرداد رسوم حجز #{$booking->reference_number}",
                'booking_id' => $booking->id,
                'balance_after' => $wallet->balance,
                'created_by' => $userId,
            ]);
        });
    }
}
