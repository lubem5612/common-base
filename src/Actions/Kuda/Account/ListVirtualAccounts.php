<?php


namespace Transave\CommonBase\Actions\Kuda\Account;


use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class ListVirtualAccounts
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
            return $this->validateRequest()->listVirtualAccounts();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function listVirtualAccounts()
    {
        return (new KudaApiHelper(['serviceType' => 'ADMIN_VIRTUAL_ACCOUNTS', 'data' => $this->validatedData]))->execute();
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "PageSize" => 'nullable|integer',
            "PageNumber" => 'nullable|integer',
        ]);
        return $this;
    }
}