<?php


namespace Transave\CommonBase\Kuda\Transfer;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;
use Transave\CommonBase\Kuda\Helpers\Validation;

class VirtualAccountFundTransfer
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
            return $this->outwardTransfer();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function outwardTransfer()
    {
        $this->validateRequest($this->input, $this->rules);
        if ($this->validationFails) {
            return $this->validationErrors;
        }
        $callback = $this->processKuda(config('constants.virtual_account_fund_transfer'), $this->input);
        if ($callback['errors']) {
            return $this->errorResponse('error in processing transfer', $callback['errors']);
        }
        return $this->successResponse('fund transfer successful', $callback['data']);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "trackingReference" => "required",
            "beneficiaryAccount" => "required",
            "amount" => "required|numeric|gt:0",
            "narration" => "required|string|max:150",
            "beneficiaryBankCode" => "required",
            "beneficiaryName" => "required|string|max:100",
            "senderName" => "required|string|max:100",
            "nameEnquiryId" => "required",
            "clientFeeCharge" => "nullable|numeric|gte:0"
        ];
        return $this;
    }
}