<?php


namespace Raadaapartners\Raadaabase\Kuda\Transaction;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class TransactionStatusQuery
{
    use ResponseHelper, PostRequestHelper;

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
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
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