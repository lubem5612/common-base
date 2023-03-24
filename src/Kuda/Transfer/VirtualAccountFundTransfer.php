<?php


namespace Raadaapartners\Raadaabase\Kuda\Transfer;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class VirtualAccountFundTransfer
{
    use ResponseHelper, ValidationHelper, PostRequestHelper;

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
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function outwardTransfer()
    {
        $this->validateRequest($this->input, $this->rules);
        return $this->processKuda(config('constants.virtual_account_fund_transfer'), $this->input);
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
        ];
        return $this;
    }
}