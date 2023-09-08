<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class VirtualAccountFundTransfer
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
                ->outwardTransfer();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function outwardTransfer()
    {
        return (new KudaApiHelper(['serviceType' => 'VIRTUAL_ACCOUNT_FUND_TRANSFER', 'data' => $this->validatedData]))->execute();
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "trackingReference" => "required",
            "beneficiaryAccount" => "required",
            "amount" => "required|numeric|gt:0",
            "narration" => "required|string|max:150",
            "beneficiaryBankCode" => "required",
            "beneficiaryName" => "required|string|max:100",
            "senderName" => "required|string|max:100",
            "nameEnquiryId" => "required",
            "clientFeeCharge" => "nullable|numeric|gte:0"
        ]);

        return $this;
    }
}