<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class SetTransactionPin extends Action
{
    private $request, $validatedData;
    private $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()->setUser()->setTransactionPin();
    }

    private function setUser()
    {
        $this->user = auth()->user();
        return $this;
    }

    private function setTransactionPin()
    {
        $this->user->transaction_pin = bcrypt($this->validatedData['transaction_pin']);
        $this->user->save();

        return $this->sendSuccess(null, 'transaction pin updated');
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'transaction_pin' => ['required', 'integer', 'between:1000,9999'],
        ]);
        return  $this;
    }
}