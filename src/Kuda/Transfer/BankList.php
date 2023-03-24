<?php


namespace Raadaapartners\Raadaabase\Kuda\Transfer;


use Raadaapartners\Raadaabase\Helpers\ResponseTrait;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class BankList
{
    use PostRequestHelper, ResponseTrait;

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
        return $this->processKuda(config('raadaabase.constants.bank_list'));
    }
}