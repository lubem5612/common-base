<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class CreateVirtualAccount
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
            return $this->create();
        }catch (\Exception $e) {
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function create()
    {
        $this->input["trackingReference"] = $this->generateUniqueId();
        $this->validateRequest($this->input, $this->rules);
        return $this->processKuda(config('raadaabase.constants.create_virtual_account'), $this->input);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "email" => "required|email",
            "phoneNumber" => "required|string|min:9|max:15",
            "lastName" => "required|string|max:50",
            "firstName" => "required|string|max:50",
            "middleName" => "sometimes|required|string|max:50",
            "businessName" => "sometimes|required|string|max:50",
        ];
        return $this;
    }
}