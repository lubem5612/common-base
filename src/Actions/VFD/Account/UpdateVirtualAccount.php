<?php


namespace Transave\CommonBase\Actions\VFD\Account;


use Transave\CommonBase\Actions\User\UpdateUser;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Http\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateVirtualAccount
{
    use ResponseHelper, ValidationHelper;

    private array $validatedData;
    private array $request;
    private ?User $user;

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
                ->updateUser();
        }catch (HttpException $e) {
            return $this->sendServerError($e, $e->getStatusCode());
        }
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
        $max50 = 'max:50';
        $max150 = 'max:150';
        $max3000 = 'max:3000';
        $idCardMimes = 'mimes:gif,jpg,jpeg,png,webp,pdf';
        $this->validatedData = $this->validate($this->request, [
            'user_id' => ['required', 'exists:users,id'],
            'first_name' => ['sometimes', 'required', 'string', $max50],
            'last_name' => ['sometimes', 'required', 'string', $max50],
            'middle_name' => ['sometimes', 'required', 'string', $max50],
            'business_name' => ['sometimes', 'required', 'string', $max150],
            'bvn' => ['sometimes', 'required', 'numeric', 'between:10000000000,99999999999'],
            'image' => ['nullable', 'file', $max3000, 'mimes:gif,jpg,jpeg,png,webp'],
            'identity_card' => ['nullable', 'file', $max3000, $idCardMimes],
            'identity_card_back' => ['nullable', 'file', $max3000, $idCardMimes],
            'address_proof' => ['nullable', 'file', $max3000, $idCardMimes],
            'identity_type' => ['nullable', 'string',$max150],
            'identity_card_number' => ['nullable', 'string',$max150],
            'country_of_origin_id' => ['nullable', 'exists:countries,id'],
            'country_of_residence_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'lga_id' => ['nullable', 'exists:lgas,id'],
            'city' => ['nullable', 'string', $max150],
            'next_of_kin' => ['nullable', 'string', 'max:100'],
            'next_of_kin_contact' => ['nullable', 'string', 'max:15'],
            'mother_maiden_name' => ['nullable', 'string', $max50],
            'residential_status' => ['nullable', 'string', $max50],
            'employment_status' => ['nullable', 'string', $max50],
            'employer' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', $max150],
            'educational_qualification' => ['nullable', 'string', 'max:80'],
            'date_of_employment' => ['nullable', 'date'],
            'number_of_children' => ['nullable', 'integer'],
            'income_range' => ['nullable'],
            'idBack' => ['nullable'],
            'isFinalSubmission' => ['nullable']
        ]);
        return $this;
    }
}
