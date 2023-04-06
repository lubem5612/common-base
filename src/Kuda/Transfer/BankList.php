<?php


namespace Transave\CommonBase\Kuda\Transfer;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;

class BankList
{
    use Api, ManageResponse;

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
        $callback = $this->processKuda(config('constants.bank_list'));
        if ($callback['errors'])
        {
            return $this->errorResponse('error in fetching banks', $callback['errors']);
        }
        return $this->successResponse('banks fetched successfully', $callback['data']['data']['banks']);
    }
}