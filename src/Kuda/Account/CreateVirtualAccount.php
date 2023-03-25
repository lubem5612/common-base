<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class CreateVirtualAccount
{
    use ValidationHelper;
    use PostRequestHelper;
    use ResponseHelper;

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
            $this->serverErrorResponse($e);
        }
    }

    private function create()
    {
        $this->input["trackingReference"] = $this->generateUniqueId();
        $this->validateRequest($this->input, $this->rules);
        $callback = $this->processKuda(config('constants.create_virtual_account'), $this->input);
        if ($callback['errors']) {
            return $this->errorResponse('error in creating account', $callback['errors']);
        }
        return $this->successResponse('account created successfully', $callback['data']['data']);
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