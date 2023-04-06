<?php


namespace Transave\CommonBase\Kuda\Account;




use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;

class GetVirtualAccount
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
            return $this->getSingleAccount();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function getSingleAccount()
    {
        $data = [
            "trackingReference" => $this->trackingReference,
        ];
        $callback = $this->processKuda(config('constants.get_single_virtual_account'), $data);
        if ($callback["errors"]) {
            return $this->errorResponse('unable to retrieve virtusl account', $callback['errors']);
        }
        return $this->successResponse('account retrieved successfully', $callback["data"]["data"]["account"]);
    }
}