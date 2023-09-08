<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class UpdateWithdrawalLimit extends Action
{
    private $request, $validatedData;
    private User $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setUser()
            ->updateWithdrawalLimit();
    }

    private function updateWithdrawalLimit()
    {
        $this->user->update([
            'withdrawal_limit' => $this->validatedData['withdrawal_limit']
        ]);

        return $this->sendSuccess(null, 'withdrawal limit updated');
    }

    private function setUser()
    {
        if (!array_key_exists('user_id', $this->validatedData)) {
            $this->validatedData['user_id'] = auth()->id();
        }
        $this->user = User::query()->find($this->validatedData['user_id']);
        return $this;
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'withdrawal_limit' => ['required', 'numeric', 'between:0,5000000'],
        ]);
        return  $this;
    }
}