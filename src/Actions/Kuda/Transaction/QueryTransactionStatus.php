<?php


namespace Transave\CommonBase\Actions\Kuda\Transaction;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class QueryTransactionStatus
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
            return $this->validateRequest()->checkTransactionStatus();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function checkTransactionStatus()
    {
        return (new KudaApiHelper(['serviceType' => 'TRANSACTION_STATUS_QUERY', 'data' => $this->validatedData]))->execute();
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "isThirdPartyBankTransfer" => "required",
            "transactionRequestReference" => "required|string"
        ]);

        return $this;
    }
}