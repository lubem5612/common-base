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
            $response = (new KudaApiHelper(['serviceType' => 'BANK_LIST']))->execute();
            abort_unless($response['success'], 404, response()->json(['message' => 'failed in fetching banks', 'data' => $response, 'success' => false]));
            return $this->sendSuccess($response['data']['banks'], 'banks retrieved successfully');
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }
}