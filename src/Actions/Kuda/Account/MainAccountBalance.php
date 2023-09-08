<?php


namespace Transave\CommonBase\Actions\Kuda\Account;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;

class MainAccountBalance
{
    use ResponseHelper;

    public function execute()
    {
        try {
            return (new KudaApiHelper(['serviceType' => 'ADMIN_RETRIEVE_MAIN_ACCOUNT_BALANCE']))->execute();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }
}