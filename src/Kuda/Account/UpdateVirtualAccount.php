<?php


namespace Transave\CommonBase\Kuda\Account;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;
use Transave\CommonBase\Kuda\Helpers\Validation;

class UpdateVirtualAccount
{
    use ManageResponse, Validation, Api;

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