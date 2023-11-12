<?php


namespace Transave\CommonBase\Actions\Flutterwave;


use Illuminate\Support\Arr;
use Transave\CommonBase\Actions\Action;
use Transave\CommonBase\Helpers\FlutterwaveApiHelper;

class ValidateCharge extends Action
{
    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->validateRequest()->validateCharge();
    }

    private function validateCharge()
    {
        return (new FlutterwaveApiHelper([
            'method' => 'POST',
            'url' => '/validate-charge',
            'data' => $this->validatedData
        ]))->execute();
    }

    private function validateRequest() : self
    {
        $this->validatedData = $this->validate($this->request, [
            "otp" => "required|size:6",
            "merchant_ref" => "required|string",
            "type" => "nullable|in:card,account"
        ]);
        $this->validatedData['flw_ref'] = $this->validatedData['merchant_ref'];
        $this->validatedData = Arr::except($this->validatedData, ['merchant_ref']);
        return $this;
    }
}