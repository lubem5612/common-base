<?php


namespace Transave\CommonBase\Actions\Auth;


use Carbon\Carbon;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Http\Models\User;

class VerifyAccount extends Action
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
            ->checkTokenExpiry()
            ->verifyUser();
    }

    private function setUser()
    {
        $this->user = User::query()->where('verification_token', $this->validatedData['verification_token'])->first();
        abort_if(empty($this->user), 404, response()->json(['message' => 'user not found', 'success' => false, 'data' => null]));
        abort_if($this->user->is_verified == 'yes', 404, response()->json(['message' => 'account already verified', 'success' => false, 'data' => null]));
        return $this;
    }

    private function checkTokenExpiry()
    {
        $isExpired = Carbon::now()->gt(Carbon::parse($this->user->account_verified_at)->addMinutes(10));
        abort_if($isExpired, 404, response()->json(['message' => 'token has expired', 'success' => false, 'data' => null]));
        return $this;
    }

    private function verifyUser()
    {
        $this->user->update([
            'is_verified' => 'yes',
            'verification_token' => null,
            'account_verified_at' => null
        ]);

        return $this->sendSuccess(null, 'account verified successfully');
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'verification_token' => ['required', 'digits:6', 'exists:users,verification_token'],
        ]);
        return  $this;
    }
}