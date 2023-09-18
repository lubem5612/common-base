<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\Kuda\Account\UpdateVirtualAccount;
use Transave\CommonBase\Http\Models\User;

class ChangeEmail extends Action
{
    private $request, $validatedData;
    private $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()->setUser()->changeUserEmail();
    }

    private function setUser()
    {
        $this->user = auth()->user();
        return $this;
    }

    private function changeUserEmail()
    {
        $this->user->email = $this->validatedData['email'];
        $this->user->save();
        $response = (new UpdateVirtualAccount([
            'email' => $this->user->refresh()->email,
            'user_id' => $this->user->id,
        ]))->execute();

        if ($response['success']) {
            return $this->sendSuccess(null, 'user email changed');
        }
        return $this->sendError('failed in updating kuda account', $response);
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'email' => ['required', 'email'],
        ]);
        return  $this;
    }
}