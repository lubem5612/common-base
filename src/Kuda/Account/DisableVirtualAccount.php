<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class DisableVirtualAccount
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
            return $this->disableVirtualAccount();
        }catch (\Exception $e) {
           return $this->serverErrorResponse($e);
        }
    }

    private function disableVirtualAccount()
    {
        $data = [
            "trackingReference" => $this->trackingReference,
        ];
        $callback = $this->processKuda(config('constants.disable_virtual_account'), $data);
        if ($callback["errors"]) {
            return $this->errorResponse('unable to disable account', $callback["errors"]);
        }
        return $this->successResponse('account disabled successfully', $callback["data"]["data"]);
    }
}
