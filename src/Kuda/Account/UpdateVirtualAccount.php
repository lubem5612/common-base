<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\ValidationHelper;

class UpdateVirtualAccount
{
    use ManageResponse, ValidationHelper, PostRequestHelper;

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
           return $this->serverErrorResponse($e);
        }
    }

    private function update()
    {
        $this->validateRequest($this->input, $this->rules);
        if ($this->validationFails) {
            return $this->validationErrors;
        }
        $callback = $this->processKuda(config('constants.update_virtual_account'), $this->input);
        if ($callback['errors']) {
            return $this->errorResponse('error in updating account', $callback['errors']);
        }
        return $this->successResponse('account updated successfully', $callback['data']['data']);
    }

    private function makeRules() : self
    {
        $this->rules = [
            "trackingReference" => "required",
            "email" => "nullable|email",
            "lastName" => "nullable|string|max:50",
            "firstName" => "nullable|string|max:50",
        ];
        return $this;
    }
}
