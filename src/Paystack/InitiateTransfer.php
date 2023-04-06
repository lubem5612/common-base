<?php


namespace Transave\CommonBase\Paystack;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Validation;
use Transave\CommonBase\Paystack\Helper\ApiCall;

class InitiateTransfer
{
    use Validation, ManageResponse, ApiCall;

    private array $request;
    private array $rules;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try {
            $this->makeRules();
            return $this->initializeTransfer();
        }catch (\Exception $exception) {
           return $this->serverErrorResponse($exception);
        }
    }

    private function initializeTransfer()
    {
        $this->validateRequest($this->request, $this->rules);
        if ($this->validationFails) {
            return $this->validationErrors;
        }
        $this->request['callback_url'] = url(config('commonbase.paystack.callback_url'));
        $url = config('commonbase.paystack.base_url')."/transaction/initialize";
        $callback = $this->processPaystack($url, $this->request);
        if ($callback['errors']) {
            return $this->errorResponse('error in initiating transfer', $callback['errors']);
        }
        return $this->successResponse('transfer initiated successfully', $callback['data']['data']);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "email" => "required|email",
            "amount" => "required|numeric|gt:100|lt:1000000"
        ];
        return $this;
    }
}