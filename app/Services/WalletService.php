<?php
namespace App\Services;

use App\Models\{Wallet, WalletTransaction, Booking, Setting};
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function deposit(Wallet $wallet, float $amount, string $description, ?int $userId = null): WalletTransaction
    {
        if ($amount == 0.0) {
            throw new \InvalidArgumentException('مبلغ العملية يجب أن يكون أكبر من صفر.');
        }

        return DB::transaction(function () use ($wallet, $amount, $description, $userId) {
            // Lock wallet row for update to prevent concurrent race conditions
            $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            // If deducting via negative amount, prevent negative balance
            if ($amount < 0 && $lockedWallet->balance < abs($amount)) {
                throw new \Exception('رصيد المحفظة غير كافٍ لإتمام الخصم.');
            }

            $lockedWallet->increment('balance', $amount);
            $lockedWallet->refresh();
            $wallet->balance = $lockedWallet->balance;

            return WalletTransaction::create([
                'wallet_id' => $lockedWallet->id,
                'type' => $amount >= 0 ? 'deposit' : 'deduction',
                'amount' => abs($amount),
                'description' => $description,
                'balance_after' => $lockedWallet->balance,
                'created_by' => $userId,
            ]);
        });
    }

    public function deductBookingFee(Wallet $wallet, Booking $booking, ?int $userId = null): ?WalletTransaction
    {
        $fee = (float) Setting::getValue('booking_fee', 150);
        if ($fee <= 0) return null;

        return DB::transaction(function () use ($wallet, $fee, $booking, $userId) {
            // Lock wallet row to prevent race conditions
            $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if ($lockedWallet->balance < $fee) {
                throw new \Exception('رصيد المحفظة غير كافي لسداد رسوم الحجز.');
            }

            $lockedWallet->decrement('balance', $fee);
            $lockedWallet->refresh();
            $wallet->balance = $lockedWallet->balance;

            return WalletTransaction::create([
                'wallet_id' => $lockedWallet->id,
                'type' => 'deduction',
                'amount' => $fee,
                'description' => "رسوم حجز #{$booking->reference_number}",
                'booking_id' => $booking->id,
                'balance_after' => $lockedWallet->balance,
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

        // Prevent double refunds
        $alreadyRefunded = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('booking_id', $booking->id)
            ->where('type', 'refund')
            ->exists();

        if ($alreadyRefunded) {
            throw new \Exception('تم استرداد رسوم هذا الحجز مسبقاً.');
        }

        return DB::transaction(function () use ($wallet, $transaction, $booking, $userId) {
            $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
            $lockedWallet->increment('balance', $transaction->amount);
            $lockedWallet->refresh();
            $wallet->balance = $lockedWallet->balance;

            return WalletTransaction::create([
                'wallet_id' => $lockedWallet->id,
                'type' => 'refund',
                'amount' => $transaction->amount,
                'description' => "استرداد رسوم حجز #{$booking->reference_number}",
                'booking_id' => $booking->id,
                'balance_after' => $lockedWallet->balance,
                'created_by' => $userId,
            ]);
        });
    }
}
