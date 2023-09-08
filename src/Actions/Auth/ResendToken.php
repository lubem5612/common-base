<?php


namespace Transave\CommonBase\Actions\Auth;



use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\SMS\TermiiService;
use Transave\CommonBase\Http\Models\User;


class ResendToken extends Action
{
    private $request, $validatedInput, $user, $token;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this
                ->validateRequest()
                ->setUser()
                ->setToken()
                ->saveToken()
                ->sendNotification()
                ->sendSuccess(null, 'token resend successfully');
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function setToken()
    {
        $this->token = rand(100000, 999999);
        return $this;
    }

    private function setUser()
    {
        $this->user = User::query()->find($this->validatedInput['user_id']);
        return $this;
    }

    private function saveToken()
    {
        abort_if($this->user->is_verified, 403, response()->json(['message' => 'user already exist', 'success' => false, 'data' => null]));

        $this->user->update([
            "token" => $this->token,
            "email_verified_at" => Carbon::now()
        ]);
        return $this;
    }

    private function sendNotification()
    {
        try {
            $message = "Hello {$this->user->name}, Use code {$this->token} to activate your account. From Transave";
            (new TermiiService(['to' => $this->user->phone, 'sms' => $message]))->execute();
        } catch (\Exception $exception) {
            Log::error($exception->getTraceAsString());
        }

        return $this;
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            "user_id" => 'required|exists:users,id'
        ]);
        return $this;
    }
}