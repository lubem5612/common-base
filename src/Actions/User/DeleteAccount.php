<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\Kuda\Account\DisableVirtualAccount;
use Transave\CommonBase\Http\Models\User;

class DeleteAccount extends Action
{
    private User $user;
    private $request, $validatedInput;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this
            ->validateRequest()
            ->setUser()
            ->deactivateAccount()
            ->deleteUser();
    }

    private function setUser()
    {
        $this->user = User::query()->find($this->validatedInput['user_id']);
        return $this;
    }

    private function deactivateAccount()
    {
        $response = (new DisableVirtualAccount(['user_id' => $this->validatedInput['user_id']]))->execute();
        abort_unless($response['success'], 403, 'unable to deactivate account');
        return $this;
    }

    private function deleteUser()
    {
        if ($this->user->role == 'admin') abort(403, 'you can not delete admin account');
        $this->user->delete();
        return $this->sendSuccess(null, 'user deleted successfully');
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id'
        ]);
        return $this;
    }
}