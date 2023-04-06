<?php


namespace Transave\CommonBase\Kuda\Transaction;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;

class QueryTransactionStatus
{
    use ManageResponse, Api;

    private string $transactionReference;
    private boolean $isThirdParty;

    public function __construct(string $transactionReference, boolean $isThirdParty)
    {
        $this->transactionReference = $transactionReference;
        $this->isThirdParty = $isThirdParty;
    }

    public function handle()
    {
        try {
            return $this->checkTransactionStatus();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function checkTransactionStatus()
    {
        $data = [
            "isThirdPartyBankTransfer" => $this->isThirdParty,
            "transactionRequestReference" => $this->transactionReference,
        ];
        return $this->processKuda(config('constants.transaction.status'), $data);
    }
}