<?php


namespace Transave\CommonBase\Actions\User;


use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\KycHelper;
use Transave\CommonBase\Http\Models\User;

class GetUserKyc extends Action
{
    private $request, $validatedData;
    private ?User $user;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $this->validateRequest();
        $this->getUser();
        return $this->getKycDetails();
    }

    private function getUser()
    {
        $this->user = User::query()->find($this->validatedData['user_id']);
    }

    private function getKycDetails()
    {
        if (!in_array($this->user->role, ['superadmin', 'admin', 'support'])) {
            $response = (new KycHelper(['user_id' => $this->user->id]))->execute();
            if(!$response['success']) {
                abort(404, 'unable to retrieve kyc');
            }
            return $this->sendSuccess($response['data'], 'kyc data retrieved');
        }
        return $this->sendError('kyc not available for user type');
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "user_id" => "required|exists:users,id"
        ]);
    }
}