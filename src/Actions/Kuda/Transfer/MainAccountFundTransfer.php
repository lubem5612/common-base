<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class MainAccountFundTransfer
{
    use ResponseHelper, ValidationHelper;

    private array $request;
    private array $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
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
        $this->validatedData["clientAccountNumber"] = config('transave.kuda.acc_number');
        $this->validatedData["senderName"] = config('transave.kuda.acc_name');

        return (new KudaApiHelper(['serviceType' => 'SINGLE_FUND_TRANSFER', 'data' => $this->validatedData]))->execute();
    }


    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "beneficiaryBankCode" => "required",
            "beneficiaryAccount" => "required",
            "beneficiaryName" => "required|string|max:100",
            "amount" => "required|numeric|gt:0",
            "narration" => "required|string|max:150",
            "nameEnquirySessionID" => "required",
            "trackingReference" => "required",
            "clientFeeCharge" => "nullable|numeric|gte:0"
        ]);

        return $this;
    }
}