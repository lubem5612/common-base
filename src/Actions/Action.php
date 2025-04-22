<?php


namespace Transave\CommonBase\Actions;


use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class Action
{
    use ValidationHelper, ResponseHelper;

    public function execute()
    {
        try {
            return $this->handle();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    public function handle()
    {
        //override with content here
        return $this;
    }
}
