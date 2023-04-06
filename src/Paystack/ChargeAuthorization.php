<?php


namespace Transave\CommonBase\Paystack;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Validation;
use Transave\CommonBase\Paystack\Helper\ApiCall;

class ChargeAuthorization
{
    use ManageResponse, Validation, ApiCall;

    private array $request;
    private array $rules;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try{
            return $this->makeRules()->chargeReturningCustomer();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function chargeReturningCustomer()
    {
        $this->validateRequest($this->request, $this->rules);
        if ($this->validationFails) {
            return $this->validationErrors;
        }
        $url = config('commonbase.paystack.base_url')."/transaction/charge_authorization";
        $callback = $this->processPaystack($url, $this->request);
        if ($callback['errors']) {
            return $this->errorResponse('error in charging customer', $callback['errors']);
        }
        return $this->successResponse('customer charge successful', $callback['data']['data']);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "email" => "required|email",
            "amount" => "required|numeric|gt:100|lt:1000000",
            "authorization_code" => "required|string",
        ];
        return $this;
    }
}