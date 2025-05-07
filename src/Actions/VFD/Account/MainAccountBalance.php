<?php


namespace Transave\CommonBase\Actions\VFD\Account;


use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;

class MainAccountBalance
{
    use ResponseHelper;

    public function execute()
    {
        try {
            return (new VfdApiHelper([], '/account/enquiry', 'get'))->execute();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }
}
