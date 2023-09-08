<?php


namespace Transave\CommonBase\Actions\Kuda\Account;


use Transave\CommonBase\Actions\User\UpdateUser;
use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\User;

class UpdateVirtualAccount
{
    use ResponseHelper, ValidationHelper;

    private array $validatedData;
    private array $request;
    private array $kudaData;
    private $response;
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
                ->setEmail()
                ->setFirstName()
                ->setLastName()
                ->updateVirtualAccount()
                ->updateUser();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function updateVirtualAccount()
    {
        $this->kudaData['trackingReference'] = $this->validatedData['user_id'];
        $this->response = (new KudaApiHelper(['serviceType' => 'ADMIN_UPDATE_VIRTUAL_ACCOUNT', 'data' => $this->kudaData]))->execute();
        abort_if($this->response['success']==false, 403, response()->json(['message' => 'kuda account update failed', 'data' => $this->response, 'success' => false]));

        return $this;
    }

    private function setEmail()
    {
        if (array_key_exists('email', $this->validatedData)) {
            $this->kudaData['email'] = $this->validatedData['email'];
        }
        return $this;
    }

    private function setLastName()
    {
        if (array_key_exists('last_name', $this->validatedData)) {
            $this->kudaData['lastName'] = $this->validatedData['last_name'];
        }
        return $this;
    }

    private function setFirstName()
    {
        if (array_key_exists('first_name', $this->validatedData)) {
            $this->kudaData['firstName'] = $this->validatedData['first_name'];
        }
        return $this;
    }

    private function updateUser()
    {
        return (new UpdateUser($this->request))->execute();
    }

    private function setUser()
    {
        $this->user = User::query()->find($this->validatedData['user_id']);
        return $this;
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            'user_id' => ['required', 'exists:users,id'],
            "email" => ["nullable", "email"],
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