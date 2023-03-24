<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class UpdateVirtualAccount
{
    use ResponseHelper, ValidationHelper, PostRequestHelper;

    private array $input;
    private array $rules;

    public function __construct(array $data)
    {
        $this->input = $data;
    }

    public function handle()
    {
        try {
            $this->makeRules();
            return $this->update();
        }catch (\Exception $e) {
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function update()
    {
        $this->validateRequest($this->input, $this->rules);
        return $this->processKuda(config('constants.update_virtual_account'), $this->input);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "trackingReference" => "required",
            "email" => "sometimes|required|email",
            "lastName" => "sometimes|required|string|max:50",
            "firstName" => "sometimes|required|string|max:50",
        ];
        return $this;
    }
}