<?php


namespace Transave\CommonBase\Helpers;


use Transave\CommonBase\Actions\Kuda\Account\VirtualAccountBalance;
use Transave\CommonBase\Http\Models\Transaction;
use Transave\CommonBase\Http\Models\User;

class WithdrawalLimitHelper
{
    private $userId;
    private User $user;

    public function __construct(string $user_id)
    {
        $this->userId = $user_id;
        $this->setUser();
    }
    public function currentLimit() {
        $amount = Transaction::query()->where('user_id', $this->user->id)->where('type', 'debit')->sum('amount');
        $charges = Transaction::query()->where('user_id', $this->user->id)->where('type', 'debit')->sum('charges');
        $commission = Transaction::query()->where('user_id', $this->user->id)->where('type', 'debit')->sum('commission');
        return (float)$this->user->withdrawal_limit - ((float)$amount + (float)$charges - (float)$commission);
    }

    public function currentWalletBalance()
    {
        $response = (new VirtualAccountBalance(['user_id' => $this->user->id]))->execute();
        if ($response['success']) {
           return $response['data']['withdrawableBalance'];
        }
        return 0.00;
    }

    private function setUser()
    {
        if ($this->userId == '' || $this->userId == null) {
            $this->userId = auth()->id();
        }
        $this->user = User::query()->find($this->userId);
        return $this;
    }
}