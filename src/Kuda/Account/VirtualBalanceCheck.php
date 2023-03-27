<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class VirtualBalanceCheck
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
