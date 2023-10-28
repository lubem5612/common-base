<?php


namespace Transave\CommonBase\Actions\User;


use Illuminate\Support\Facades\Hash;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\SessionHelper;
use Transave\CommonBase\Http\Models\User;

class VerifyTransactionPin extends Action
{
    use SessionHelper;
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
            ->verifyTransactionPin();
    }

    private function verifyTransactionPin()
    {
        $status = Hash::check($this->validatedData['transaction_pin'], $this->user->transaction_pin);
        $this->incrementSession($this->user->id);

        if ($status) return $this->sendSuccess(null, 'transaction pin verified successfully');
        return $this->sendError('failed in verifying transaction pin');
    }

    private function setUser()
    {
        if (!array_key_exists('user_id', $this->validatedData)) {
            $this->validatedData['user_id'] = auth()->id();
        }
        $this->user = User::query()->find($this->validatedData['user_id']);

        abort_if($this->getSession($this->user->id) > 4, 403, response()->json(['message' => 'maximum attempts exceeded', 'data' => null, 'success' => false]));

        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => 'required|exists:users,id',
            'transaction_pin' => 'required|integer|digits:4',
        ]);

        return $this;
    }
}