<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\Kuda\Account\DisableVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\EnableVirtualAccount;
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
            ->updateKudaStatus()
            ->updateAccountStatus();
    }

    private function updateAccountStatus()
    {
        $this->user->update([
            'account_status' => $this->validatedData['account_status']
        ]);
        return $this->sendSuccess(null, 'account status updated');
    }

    private function updateKudaStatus()
    {
        if ($this->validatedData['account_status'] == 'verified') {
            $response = (new EnableVirtualAccount([
                'user_id' => $this->user->id
            ]))->execute();
            abort_unless($response['success'], 403, 'unable to enable virtual account');
        }else {
            $response = (new DisableVirtualAccount([
                'user_id' => $this->user->id
            ]))->execute();
            $this->user->update([
                'is_verified' => 'no',
                'withdrawal_limit' => 0,
            ]);
            abort_unless($response['success'], 403, 'unable to disable virtual account');
        }
        return $this;
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