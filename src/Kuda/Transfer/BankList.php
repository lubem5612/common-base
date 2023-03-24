<?php


namespace Raadaapartners\Raadaabase\Kuda\Transfer;


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
           return $this->serverErrorResponse($e);
        }
    }

    private function getBankList()
    {
        return $this->processKuda(config('constants.bank_list'));
    }
}