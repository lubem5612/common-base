<?php


namespace Transave\CommonBase\Actions\VFD\Account;


use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Helpers\Constants;
use Transave\CommonBase\Helpers\UtilsHelper;

class NewAccountValidation
{
    use ResponseHelper, UtilsHelper;

    public function execute()
    {
        try {
            $this->validateAccount();
        }catch (\Exception $e) {
            $this->sendServerError($e);
        }
    }

    private function validateAccount()
    {
        $users = User::where([
            'account_status' => Constants::ACCOUNT_STATUS['unverified'],
            'role' => Constants::USER_ROLES['customer']
        ])->with(['kyc'])->get();

        foreach($users as $user) {
            if ($user->kyc && isset($user->kyc->verification_payload)) {
                $verifcation_payload = json_decode($user->kyc->verification_payload);
                if (isset($verifcation_payload->data)) {
                    $kyc = $verifcation_payload->data;
                    $regName = $user->first_name.$user->middle_name.$user->last_name;
                    $regName = $this->removeStrings(strtolower($regName), [strtolower(Constants::WALLET_PREFIX)]);
                    $kycName = $kyc->firstname.$kyc->middlename.$kyc->lastname;
                    $kycName = $this->removeStrings(strtolower($kycName), [strtolower(Constants::WALLET_PREFIX)]);
    
                    if ($kycName != $regName) {
                        $user->account_status = Constants::ACCOUNT_STATUS['suspended'];
                        $user->Save();
                        // Send email to notify user
                    } else {
                        $user->first_name = Constants::WALLET_PREFIX.$user->first_name;
                        $user->account_status = Constants::ACCOUNT_STATUS['verified'];
                        $user->account_type = Constants::ACCOUNT_TYPE['classic'];
                        $user->is_verified = Constants::IS_VERIFIED['yes'];
                        $user->kyc->verification_status = Constants::ACCOUNT_STATUS['incomplete'];
                        $user->kyc->save();
                        $user->save();
                    }
                }
            } else {
                $user->account_status = Constants::ACCOUNT_STATUS['suspended'];
                $user->Save();
                // Send email
            }
        }
    }
}
