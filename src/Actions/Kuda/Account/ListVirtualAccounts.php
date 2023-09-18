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
            return $this
                ->validateRequest()
                ->setPageNumber()
                ->setPageSize()
                ->listVirtualAccounts();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function listVirtualAccounts()
    {
        return (new KudaApiHelper(['serviceType' => 'ADMIN_VIRTUAL_ACCOUNTS', 'data' => $this->validatedData]))->execute();
    }

    private function setPageSize()
    {
        if (!array_key_exists('pageSize', $this->validatedData)) $this->validatedData['pageSize'] = 10;
        $this->validatedData['pageSize'] = (string)$this->validatedData['pageSize'];
        return $this;
    }

    private function setPageNumber()
    {
        if (!array_key_exists('pageNumber', $this->validatedData)) $this->validatedData['pageNumber'] = 1;
        $this->validatedData['pageNumber'] = (string)$this->validatedData['pageNumber'];
        return $this;
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