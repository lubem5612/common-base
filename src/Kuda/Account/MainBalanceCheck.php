<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class MainBalanceCheck
{
    use PostRequestHelper, ManageResponse;

    public function handle()
    {
        try {
            return $this->chekMainAccountBalance();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function chekMainAccountBalance()
    {
        $callback = $this->processKuda(config('constants.get_main_account_balance'));
        if ($callback['errors'])
        {
            return $this->errorResponse('error in fetching main account balance', $callback['errors']);
        }
        return $this->successResponse('main account balance successfully', $callback['data']['data']);
    }
}
