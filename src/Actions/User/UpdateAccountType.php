<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class UpdateAccountType extends Action
{
    private $request, $validatedData;

    private User $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()->setUser()->updateAccountType();
    }

    private function setUser()
    {
        if (!array_key_exists('user_id', $this->validatedData)) {
            $this->validatedData['user_id'] = auth()->id();
        }
        $this->user = User::query()->find($this->validatedData['user_id']);
        return $this;
    }

    private function updateAccountType()
    {
        $this->user->update([
            'account_type' => $this->validatedData['account_type'],
            'withdrawal_limit' => config('commonbase.withdrawal_limits')[$this->validatedData['account_type']]
        ]);

        return $this->sendSuccess(null, 'account type updated');
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => ['required', 'exists:users,id'],
            'account_type' => ['required', 'in:ordinary,classic,premium,super'],
        ]);
        return  $this;
    }
}