<?php


namespace Transave\CommonBase\Actions\Paystack;


use Transave\CommonBase\Helpers\PaystackApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class ChargeAuthorization
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
        try{
            return $this
                ->validateRequest()
                ->chargeReturningCustomer();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function chargeReturningCustomer()
    {
        return (new PaystackApiHelper([
            'url' => '/transaction/charge_authorization',
            'data' => $this->validatedData
        ]))->execute();
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "email" => "required|email",
            "amount" => "required|numeric|gt:100|lt:1000000",
            "authorization_code" => "required|string",
        ]);
        return $this;
    }
}