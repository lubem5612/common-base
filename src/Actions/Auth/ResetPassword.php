<?php


namespace Transave\CommonBase\Actions\Auth;



use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class ResetPassword extends Action
{
    private $validatedInput, $request;
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
            ->setPassword()
            ->deletePasswordReset();
    }

    private function deletePasswordReset()
    {
        DB::table('password_resets')->where("token", $this->validatedInput['token'])->delete();
        return $this->sendSuccess(null, "password reset successful");
    }

    private function setUser()
    {
        $reset = DB::table('password_resets')->where("token", $this->validatedInput['token'])->first();
        $this->user = User::query()->where("email", $reset->email)->first();

        $isExpired = Carbon::now()->gt(Carbon::parse($reset->created_at)->addMinutes(10));
        abort_if($isExpired, 403, response()->json(['message' => 'token expired', 'success' => false, 'data' => null]));
        return $this;
    }

    private function setPassword()
    {
        $this->user->password = bcrypt($this->validatedInput["password"]);
        $this->user->save();
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedInput = $this->validate($this->request, [
            'token' => 'required|integer|digits:4',
            'password' => 'required|string|min:6'
        ]);
        return $this;
    }
}