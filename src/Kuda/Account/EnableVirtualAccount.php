<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class EnableVirtualAccount
{
    use ManageResponse, PostRequestHelper;

    private string $trackingReference;

    public function __construct(string $trackingReference)
    {
        $this->trackingReference = $trackingReference;
    }

    public function handle()
    {
        try {
            return $this->enableVirtualAccount();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function enableVirtualAccount()
    {
        $data = [
            "trackingReference" => $this->trackingReference,
        ];
        $callback = $this->processKuda(config('constants.enable_virtual_account'), $data);
        if ($callback["errors"]) {
            return $this->errorResponse('unable to disable account', $callback["errors"]);
        }
        return $this->successResponse('account disabled successfully', $callback["data"]["data"]);
    }
}
