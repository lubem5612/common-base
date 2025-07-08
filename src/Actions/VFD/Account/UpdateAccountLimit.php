<?php

namespace Transave\CommonBase\Actions\VFD\Account;


use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;
use Transave\CommonBase\Helpers\VfdApiHelper;
use Illuminate\Support\Facades\Log;

class UpdateAccountLimit
{
    use ResponseHelper, ValidationHelper;

    private $request, $validatedData;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        try {
            return $this
                ->validateRequest()
                ->updateAccountLimit();
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            return $this->sendServerError($e);
        }
    }

    private function updateAccountLimit()
    {
        $data = $this->validatedData;
        $endpoint = '/transaction/limit';
        $response = (new VfdApiHelper($data, $endpoint, 'post'))->execute();

        if ($response['status'] == '00') return $this->sendSuccess($response['message'], 'Successful');

        return $this->sendError('An error occurred', [], 400);
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            'accountNumber'     => 'required|string',
            'transactionLimit'  => 'required|string|gt:0',
            'dailyLimit'        => 'required|string|gt:0'
        ]);

        return $this;
    }
}
