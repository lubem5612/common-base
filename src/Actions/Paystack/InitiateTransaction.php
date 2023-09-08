<?php


namespace Transave\CommonBase\Actions\Paystack;


use Transave\CommonBase\Helpers\PaystackApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class InitiateTransaction
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
            return $this
                ->validateRequest()
                ->initializeTransfer();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }


    private function initializeTransfer()
    {
        return (new PaystackApiHelper([
            'url' => '/transaction/initialize',
            'data' => $this->validatedData
        ]))->execute();
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "email" => "required|email",
            "amount" => "required|numeric|gt:100|lt:1000000"
        ]);
        return $this;
    }
}