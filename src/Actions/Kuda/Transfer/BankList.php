<?php


namespace Transave\CommonBase\Actions\Kuda\Transfer;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;

class BankList
{
    use ResponseHelper;

    public function execute()
    {
        try {
            return (new KudaApiHelper(['serviceType' => 'BANK_LIST']))->execute();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }
}