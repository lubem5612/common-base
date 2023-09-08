<?php


namespace Transave\CommonBase\Actions\Wallet;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\Transaction\CreateTransaction;
use Transave\CommonBase\Http\Models\Wallet;

class CreditUserWallet extends Action
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
        $this->setCategory();
        $this->setTransactionStatus();
        $this->setType();
        $this->setDescription();
        $this->createTransaction();
        return $this->creditWallet();
    }

    private function setUserWallet()
    {
        abort_unless(auth()->check(), 401, 'you must be authenticated');

        if (array_key_exists('user_id', $this->validatedData) && $this->validatedData['user_id']) {
            $this->wallet = Wallet::query()->where('user_id', $this->validatedData['user_id'])->first();
        }else {
            $this->validatedData['user_id'] = auth()->id();
            $this->wallet = Wallet::query()->where('user_id', $this->validatedData['user_id'])->first();
        }
        return $this;
    }

    private function checkIfWalletIsActive()
    {
        abort_if($this->wallet->status!='active', 403, 'wallet not active');
        return $this;
    }

    private function createTransaction ()
    {
        $response = (new CreateTransaction($this->validatedData))->execute();
        $array = json_decode($response->getContent(), true);
        abort_unless($array['success'], 403, 'unable to save transaction');
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

    private function setType()
    {
        $this->validatedData['type'] = 'credit';
        return $this;
    }

    private function setDescription()
    {
        if (!array_key_exists('description', $this->validatedData)) {
            $this->validatedData['description'] = "Credit to user wallet";
        }
        return $this;
    }

    private function setCategory()
    {
        if (!array_key_exists('category', $this->validatedData)) {
            $this->validatedData['category'] = "WALLET_DEPOSIT";
        }
        return $this;
    }

    private function setTransactionStatus()
    {
        if (!array_key_exists('status', $this->validatedData)) {
            $this->validatedData['status'] = "successful";
        }
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "user_id" => "nullable|exists:users,id",
            "amount" => "required|numeric|gt:0",
            "commission" => "nullable|numeric|gt:0",
            "charge" => "nullable|numeric|gt:0",
            'description' => 'nullable|string|max:700',
            'category' => 'required|string',
            'status' => 'nullable|string',
            'payload' => 'nullable',
        ]);
    }
}