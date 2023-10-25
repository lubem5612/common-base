<?php


namespace Transave\CommonBase\Actions\Auth;



use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\SMS\TermiiService;
use Transave\CommonBase\Helpers\PhoneHelper;
use Transave\CommonBase\Http\Models\User;


class ResendToken extends Action
{
    use PhoneHelper;
    private $request, $validatedInput, $token;
    private User $user;

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
        $this->user = User::query()->where('phone', $this->validatedInput['phone'])->first();
        return $this;
    }

    private function saveToken()
    {
        abort_if($this->user->is_verified=='yes', 403, 'user already verified');

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
            "phone" => 'required|string'
        ]);
        $this->validatedInput['phone'] = $this->getInternationalNumber($this->validatedInput['phone']);
        return $this;
    }
}