<?php


namespace Transave\CommonBase\Actions\Auth;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Actions\SMS\TermiiService;
use Transave\CommonBase\Helpers\PhoneHelper;
use Transave\CommonBase\Http\Models\User;

class ForgotPassword extends Action
{
    use PhoneHelper;
    private $validatedData, $request, $token, $message;
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
            ->generateCode()
            ->deleteResetIfExists()
            ->createPasswordReset()
            ->sendNotification();
    }

    private function setUser()
    {
        $this->user = User::query()->where("email", $this->validatedData['email'])->first();
        return $this;
    }

    private function createPasswordReset()
    {
        DB::table('password_resets')->insert([
            "email" => $this->user->email,
            "token" => $this->token,
            "created_at" => Carbon::now()
        ]);

        $this->message = "A password reset token has been sent to your phone";
        return $this;
    }

    private function generateCode()
    {
        $this->token = rand(100000, 999999);
        return $this;
    }

    private function deleteResetIfExists()
    {
        if (DB::table('password_resets')->where('email', $this->user->email)->exists()) {
            DB::table('password_resets')->where('email', $this->user->email)->delete();
        }
        return $this;
    }

    private function sendNotification()
    {
        try {
            $message = "Hello {$this->user->name}, Use code {$this->token} to reset password. From Transave";
            (new TermiiService(['to' => $this->user->phone, 'sms' => $message]))->execute();
        } catch (\Exception $exception) {
            Log::error($exception->getTraceAsString());
        }

        return $this->sendSuccess(null, $this->message);
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'email' => ['required', 'email'],
        ]);
        return  $this;
    }
}