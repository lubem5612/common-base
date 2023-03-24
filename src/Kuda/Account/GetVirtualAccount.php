<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class GetVirtualAccount
{
    use ResponseHelper, PostRequestHelper;

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
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function getSingleAccount()
    {
        $data = [
            "trackingReference" => $this->trackingReference,
        ];
        return $this->processKuda(config('constants.get_single_virtual_account'), $data);
    }
}