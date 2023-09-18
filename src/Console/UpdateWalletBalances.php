<?php


namespace Transave\CommonBase\Console;


use Illuminate\Console\Command;
use Transave\CommonBase\Actions\Kuda\Account\VirtualAccountBalance;
use Transave\CommonBase\Http\Models\Wallet;

class UpdateWalletBalances extends Command
{
    protected $signature = 'transave:balance';
    protected $description = 'update wallet balance with kuda balance hourly';

    public function handle()
    {
        $wallets = Wallet::query()->get();
        foreach ($wallets as $wallet) {
            if ($wallet->user->role == 'customer') {
                $response = (new VirtualAccountBalance(['user_id' => $wallet->user->id]))->execute();
                if ($response['success']) {
                    $current_balance = $wallet->balance;
                    $wallet->update([
                        'balance' => $response['data']['withdrawableBalance'],
                        'previous_balance' => $current_balance,
                    ]);
                }
            }
        }
    }
}