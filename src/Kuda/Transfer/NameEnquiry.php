<?php


namespace Raadaapartners\Raadaabase\Kuda\Transfer;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class NameEnquiry
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
            return $this->fetchBeneficiaryAccount();
        }catch (\Exception $e) {
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function fetchBeneficiaryAccount()
    {
        $this->validateRequest($this->input, $this->rules);
        return $this->processKuda(config('constants.name_inquiry'), $this->input);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "beneficiaryAccountNumber" => "required|string|min:9|max:10",
            "beneficiaryBankCode" => "required|string|min:3",
            "senderTrackingReference" => "sometimes|required",
            "isRequestFromVirtualAccount" => "sometimes|required|in:true,false"
        ];
        return $this;
    }
}