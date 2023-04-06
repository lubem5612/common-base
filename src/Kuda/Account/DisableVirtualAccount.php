<?php


namespace Transave\CommonBase\Kuda\Account;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;

class DisableVirtualAccount
{
    use ManageResponse, Api;

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