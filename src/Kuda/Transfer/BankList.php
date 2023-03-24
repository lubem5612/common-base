<?php


namespace Raadaapartners\Raadaabase\Kuda\Transfer;


use Illuminate\Support\Facades\Log;
use Raadaapartners\Raadaabase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class BankList
{
    use PostRequestHelper, ResponseHelper;

    public function handle()
    {
        try {
            return $this->getBankList();
        }catch (\Exception $e) {
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function getBankList()
    {
        return $this->processKuda(config('constants.bank_list'));
    }
}