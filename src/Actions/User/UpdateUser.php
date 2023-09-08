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
        $this->request = $request;
        $this->uploader = new UploadHelper();
    }

    public function execute()
    {
        try {
            return $this
                ->validateRequest()
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
        $this->kyc = Kyc::query()->where('user_id', $this->validatedData['user_id']);
        return $this;
    }

    private function updateKycRecord()
    {
        $this->kyc->fill($this->kycData)->save();
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => ['required', 'exists:users,id'],
            'first_name' => ['sometimes', 'required', 'string', 'max:50'],
            'last_name' => ['sometimes', 'required', 'string', 'max:50'],
            'middle_name' => ['sometimes', 'required', 'string', 'max:50'],
            'business_name' => ['sometimes', 'required', 'string', 'max:150'],
            'bvn' => ['sometimes', 'required', 'numeric', 'between:10000000000,99999999999'],

            'image' => ['nullable', 'file', 'max:3000', 'mimes:gif,jpg,jpeg,png,webp'],
            'identity_card' => ['nullable', 'file', 'max:3000', 'mimes:gif,jpg,jpeg,png,webp,pdf'],
            'address_proof' => ['nullable', 'file', 'max:3000', 'mimes:gif,jpg,jpeg,png,webp,pdf'],
            'identity_type' => ['nullable', 'string','max:150'],
            'identity_card_number' => ['nullable', 'string','max:150'],
            'country_of_origin_id' => ['nullable', 'exists:countries,id'],
            'country_of_residence_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'lga_id' => ['nullable', 'exists:lgas,id'],
            'city' => ['nullable', 'string', 'max:150'],
            'next_of_kin' => ['nullable', 'string', 'max:100'],
            'next_of_kin_contact' => ['nullable', 'string', 'max:15'],
            'mother_maiden_name' => ['nullable', 'string', 'max:50'],
            'residential_status' => ['nullable', 'string', 'max:50'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'employer' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'educational_qualification' => ['nullable', 'string', 'max:80'],
            'date_of_employment' => ['nullable', 'string'],
            'number_of_children' => ['nullable', 'string'],
            'income_range' => ['nullable', 'array'],
            'income_range.*' => ['nullable', 'numeric'],
            'verification_status' => ['nullable', 'string', 'in:verified,incomplete,unverified'],
            'is_loan_compliant' => ['nullable', 'string', 'in:yes,no'],
        ]);
        return $this;
    }
}