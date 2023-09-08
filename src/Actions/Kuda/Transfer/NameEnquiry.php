<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class NameEnquiry
{
    use ResponseHelper, ValidationHelper;

    private array $request;
    private array $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try {
            return $this
                ->validateRequest()
                ->fetchBeneficiaryAccount();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function fetchBeneficiaryAccount()
    {
        return (new KudaApiHelper(['serviceType' => 'NAME_ENQUIRY', 'data' => $this->validatedData]))->execute();
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "beneficiaryAccountNumber" => "required|string|min:9|max:10",
            "beneficiaryBankCode" => "required|string|min:3",
            "senderTrackingReference" => "nullable",
            "isRequestFromVirtualAccount" => "nullable|in:true,false"
        ]);

        return $this;
    }
}