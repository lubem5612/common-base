<?php


namespace Transave\CommonBase\Kuda\Transfer;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;
use Transave\CommonBase\Kuda\Helpers\Validation;

class NameEnquiry
{
    use ManageResponse, Validation, Api;

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
            return $this->fetchBeneficiaryAccount();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function fetchBeneficiaryAccount()
    {
        $this->validateRequest($this->input, $this->rules);
        if ($this->validationFails) {
            return $this->validationErrors;
        }
        $callback = $this->processKuda(config('constants.name_inquiry'), $this->input);
        if ($callback['errors']) {
            return $this->errorResponse('error in getting recipient account', $callback['errors']);
        }
        return $this->successResponse('name enquiry successful', $callback['data']['data']);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "beneficiaryAccountNumber" => "required|string|min:9|max:10",
            "beneficiaryBankCode" => "required|string|min:3",
            "senderTrackingReference" => "nullable",
            "isRequestFromVirtualAccount" => "nullable|in:true,false"
        ];
        return $this;
    }
}