<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\Kuda\Account\EnableVirtualAccount;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;

class RestoreAccount extends Action
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
            ->createKycIfMissing()
            ->activateAccount()
            ->restoreAccount();
    }

    private function setUser()
    {
        $this->user = User::withTrashed()->find($this->validatedInput['user_id']);
        return $this;
    }

    private function restoreAccount()
    {
        $this->user->restore();
        return $this->sendSuccess(null, 'account restore successfully');
    }

    private function activateAccount()
    {
        $response = (new EnableVirtualAccount(['user_id' => $this->validatedInput['user_id']]))->execute();
        abort_unless($response['success'], 403, 'unable to reactivate account');
        return $this;
    }

    private function createKycIfMissing()
    {
        $kyc = Kyc::query()->where('user_id', $this->validatedInput['user_id'])->doesntExist();
        if ($kyc) {
            Kyc::query()->create([
                'user_id' => $this->validatedInput['user_id']
            ]);
        }
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id'
        ]);
        return $this;
    }
}