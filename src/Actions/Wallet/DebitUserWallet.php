<?php


namespace Transave\CommonBase\Actions\Wallet;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\Wallet;

class DebitUserWallet extends Action
{
    private $request, $validatedData;
    private Wallet $wallet;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $this->validateRequest();
        $this->setUserWallet();
        $this->checkIfWalletIsActive();
        return $this->creditWallet();
    }

    private function setUserWallet()
    {
        if (array_key_exists('user_id', $this->validatedData) && $this->validatedData['user_id']) {
            $this->wallet = Wallet::query()->where('user_id', $this->validatedData['user_id'])->first();
        }else {
            $this->wallet = Wallet::query()->where('user_id',auth()->id())->first();
        }
        return $this;
    }

    private function checkIfWalletIsActive()
    {
        abort_if($this->wallet->status!='active', 403, response()->json(['message' => 'wallet not active', 'success' => false, 'data' => null]));
        return $this;
    }

    private function creditWallet()
    {
        $current_balance = $this->wallet->balance;
        $this->wallet->update([
            'balance' => abs((float)$this->validatedData['amount']) + (float)$current_balance,
            'previous_balance' => $current_balance
        ]);
        return $this->sendSuccess($this->wallet->refresh()->load('user'), 'wallet credited successfully');
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "user_id" => "nullable|exists:users,id",
            "amount" => "required|numeric|gt:0"
        ]);
    }
}