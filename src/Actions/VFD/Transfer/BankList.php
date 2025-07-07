<?php


namespace Transave\CommonBase\Actions\VFD\Transfer;


use Transave\CommonBase\Helpers\VfdApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;

class BankList
{
    use ResponseHelper;

    public function execute()
    {
        try {
            return (new VfdApiHelper([], '/bank', 'get'))->execute();
        } catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }
}