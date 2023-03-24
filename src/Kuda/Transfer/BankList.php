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
        $callback = $this->processKuda(config('constants.bank_list'));
        if ($callback['errors'])
        {
            return $this->errorResponse('error in fetching banks', $callback['errors']);
        }
        return $this->successResponse('banks fetched successfully', $callback['data']['data']['banks']);
    }
}