<?php


namespace Transave\CommonBase\Actions\User;


use Illuminate\Support\Arr;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\UploadHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;

class UpdateUser
{
    use ValidationHelper, ResponseHelper;
    private $request, $validatedData, $kycData, $walletData;
    private User $user;
    private Kyc $kyc;
    private $uploader;

    public function __construct(array $request)
    {
        $this->validatedData = $request;
        $this->uploader = new UploadHelper();
    }

    public function execute()
    {
        try {
            return $this
                ->setKyCData()
                ->setUser()
                ->setKyc()
                ->uploadIdentityCard()
                ->uploadPoofOfAddress()
                ->uploadProfileImage()
                ->setIncomeRange()
                ->updateKycRecord()
                ->updateUserAccount();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function setUser()
    {
        $this->user = User::query()->find($this->validatedData['user_id']);
        return $this;
    }

    private function updateUserAccount()
    {
        $data = Arr::only($this->validatedData, ['first_name', 'last_name', 'middle_name', 'business_name', 'bvn']);
        $this->user->fill($data)->save();

        return $this->sendSuccess($this->user->refresh()->load('kyc'), 'user account updated');
    }

    private function setKyCData()
    {
        $this->kycData = Arr::only($this->validatedData, [
            'identity_type',
            'identity_card_number',
            'country_of_origin_id',
            'country_of_residence_id',
            'state_id', 'lga_id',
            'city', 'next_of_kin',
            'next_of_kin_contact',
            'mother_maiden_name',
            'residential_status',
            'employment_status',
            'employer', 'job_title',
            'educational_qualification',
            'date_of_employment',
            'number_of_children',
            'income_range',
            'verification_status',
            'is_loan_compliant',
        ]);
        return $this;
    }

    private function uploadProfileImage()
    {
        if (array_key_exists('image', $this->validatedData)) {
            $response = $this->uploader->uploadOrReplaceFile($this->validatedData['image'], 'profiles', $this->kyc, 'image_url');
            if ($response['success']) {
                $this->kycData['image_url'] = $response['upload_url'];
            }
        }
        return $this;
    }

    private function uploadIdentityCard()
    {
        if (array_key_exists('identity_card', $this->validatedData)) {
            $response = $this->uploader->uploadOrReplaceFile($this->validatedData['identity_card'], 'identity-cards', $this->kyc, 'identity_card_url');
            if ($response['success']) {
                $this->kycData['identity_card_url'] = $response['upload_url'];
            }
        }
        return $this;
    }

    private function uploadPoofOfAddress()
    {
        if (array_key_exists('address_proof', $this->validatedData)) {
            $response = $this->uploader->uploadOrReplaceFile($this->validatedData['address_proof'], 'addresses', $this->kyc, 'address_proof_url');
            if ($response['success']) {
                $this->kycData['address_proof_url'] = $response['upload_url'];
            }
        }
        return $this;
    }

    private function setIncomeRange()
    {
        if (array_key_exists('income_range', $this->validatedData) && is_array($this->validatedData['income_range'])) {
            $this->validatedData['income_range'] = json_encode($this->validatedData['income_range']);
        }
        return $this;
    }

    private function setKyc()
    {
        $this->kyc = Kyc::query()->where('user_id', $this->validatedData['user_id'])->first();
        return $this;
    }

    private function updateKycRecord()
    {
        $this->kyc->fill($this->kycData)->save();
        return $this;
    }
}