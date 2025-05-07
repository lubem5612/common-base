<?php

namespace Transave\CommonBase\Helpers;

use Transave\CommonBase\Http\Models\Wallet;

trait BalanceHelper
{
    public function checkBalance($user_id): float
    {
        $wallet = Wallet::where(['user_id' => $user_id])->first();
        if ($wallet) return $wallet->balance;
        return 0;
    }

    public function debitWallet($user_id, $amount) : bool
    {
        $wallet = Wallet::where(['user_id' => $user_id])->lockForUpdate()->first();
        // make sure amount is in positive
        // Log balance updates
        if ($wallet && $wallet->balance >= $amount) {
            $wallet->balance -= $amount;
            $wallet->Save();

            return true;
        }

        return false;
    }

    public function creditWallet($user_id, $amount) : bool
    {
        $wallet = Wallet::where(['user_id' => $user_id])->lockForUpdate()->first();
        // make sure amount is in positive
        // Log balance updates
        if ($wallet) {
            $wallet->balance += $amount;
            $wallet->Save();

            return true;
        }

        return false;
    }
}
