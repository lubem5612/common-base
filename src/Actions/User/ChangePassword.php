<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class ChangePassword extends Action
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
            ->checkOldPassword()
            ->changePassword();
    }

    private function changePassword()
    {
        $this->user->password = bcrypt($this->validatedData['new_password']);
        $this->user->save();

        return $this->sendSuccess(null, 'withdrawal limit updated');
    }

    private function checkOldPassword()
    {
        $response = (new VerifyPassword([
            'user_id' => $this->validatedData['user_id'],
            'password' => $this->validatedData['old_password']
        ]))->execute();
        $array_content = json_decode($response->getContent(), true);
        abort_unless($array_content['success'], 401, 'password does not match');
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
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'new_password' => ['required'],
            'old_password' => ['required'],
        ]);
        return  $this;
    }
}