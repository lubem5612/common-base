<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class UpdateAccountStatus extends Action
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
            ->updateAccountStatus();
    }

    private function updateAccountStatus()
    {
        $this->user->update([
            'account_status' => $this->validatedData['account_status']
        ]);

        return $this->sendSuccess(null, 'account status updated');
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
            'user_id' => ['required', 'exists:users,id'],
            'account_status' => ['required', 'in:unverified,verified,suspended,banned'],
        ]);
        return  $this;
    }
}