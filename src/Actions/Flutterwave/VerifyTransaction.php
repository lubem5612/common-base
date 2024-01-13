<?php


namespace Transave\CommonBase\Actions\Flutterwave;


use Transave\CommonBase\Helpers\FlutterwaveApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class VerifyTransaction
{
    use ValidationHelper, ResponseHelper;
    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $this->validateRequest();
            return $this->queryTransactionStatus();
        }catch (\Exception $exception) {
            return $this->sendServerError($exception);
        }
    }

    private function queryTransactionStatus()
    {
        return (new FlutterwaveApiHelper([
            'method' => 'GET',
            'url' => '/transactions/verify_by_reference?tx_ref='.$this->validatedData['tx_ref']
        ]))->execute();
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'tx_ref' => 'required|string|min:15'
        ]);
    }
}