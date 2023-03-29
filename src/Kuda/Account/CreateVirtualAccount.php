<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class CreateVirtualAccount
{
    use PostRequestHelper;
    use ManageResponse;
    use ValidationHelper;

    private array $input;
    private array $rules;

    public function __construct(array $data)
    {
        $this->input = $data;
    }

    public function handle()
    {
        try {
            $this->makeRules();
            return $this->create();
        }catch (\Exception $e) {
            $this->serverErrorResponse($e);
        }
    }

    private function create()
    {
        $this->validateRequest($this->input, $this->rules);
        if ($this->validationFails) {
            return $this->validationErrors;
        }
        $callback = $this->processKuda(config('constants.create_virtual_account'), $this->input);
        if ($callback['errors']) {
            return $this->errorResponse('error in creating account', $callback['errors']);
        }
        return $this->successResponse('account created successfully', $callback['data']['data']);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "trackingReference" => "required",
            "email" => "required|email",
            "phoneNumber" => "required|string|min:9|max:15",
            "lastName" => "required|string|max:50",
            "firstName" => "required|string|max:50",
            "middleName" => "nullable|string|max:50",
            "businessName" => "nullable|string|max:50",
        ];
        return $this;
    }
}
