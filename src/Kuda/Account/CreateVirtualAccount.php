<?php


namespace Transave\CommonBase\Kuda\Account;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;
use Transave\CommonBase\Kuda\Helpers\Validation;

class CreateVirtualAccount
{
    use Api, Validation, ManageResponse;

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