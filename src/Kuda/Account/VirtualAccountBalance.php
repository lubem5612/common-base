<?php


namespace Transave\CommonBase\Kuda\Account;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;

class VirtualAccountBalance
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
            return $this->getVirtualAccountBalance();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function getVirtualAccountBalance()
    {
        $data = [
            "trackingReference" => $this->trackingReference,
        ];
        $callback = $this->processKuda(config('constants.enable_virtual_account'), $data);
        if ($callback["errors"]) {
            return $this->errorResponse('unable to get virtual account balance', $callback["errors"]);
        }
        return $this->successResponse('virtual account balance retrieved successfully', $callback["data"]["data"]);
    }
}