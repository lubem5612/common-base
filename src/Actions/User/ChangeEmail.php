<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class ChangeEmail extends Action
{
    private $request, $validatedData;
    private User $user;

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

        return $this->sendSuccess(null, 'user email changed');
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'email' => ['required', 'email'],
        ]);
        return  $this;
    }
}