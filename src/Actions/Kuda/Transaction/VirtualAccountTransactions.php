<?php


namespace Transave\CommonBase\Actions\Kuda\Transaction;


use Carbon\Carbon;
use Transave\CommonBase\Helpers\KudaApiHelper;
use Transave\CommonBase\Helpers\ResponseHelper;
use Transave\CommonBase\Helpers\ValidationHelper;

class VirtualAccountTransactions
{
    use ResponseHelper, ValidationHelper;

    private $request, $validatedData;
    private $serviceType;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try {
            return $this
                ->validateRequest()
                ->setPageSize()
                ->setPageNumber()
                ->setStartDate()
                ->setEndDate()
                ->adminFetchAllTransactions();
        }catch (\Exception $e) {
            return $this->sendServerError($e);
        }
    }

    private function adminFetchAllTransactions()
    {
        if (isset($this->validatedData['startDate']) && isset($this->validatedData['endDate']))
        {
            $this->serviceType = 'ADMIN_VIRTUAL_ACCOUNT_FILTERED_TRANSACTIONS';
        }else {
            $this->serviceType = 'ADMIN_VIRTUAL_ACCOUNT_TRANSACTIONS';
        }

        return (new KudaApiHelper(['serviceType' => $this->serviceType, 'data' => $this->validatedData]))->execute();
    }

    private function setPageSize()
    {
        if (!array_key_exists('pageSize', $this->validatedData)) $this->validatedData['pageSize'] = 10;
        return $this;
    }

    private function setPageNumber()
    {
        if (!array_key_exists('pageNumber', $this->validatedData)) $this->validatedData['pageNumber'] = 1;
        return $this;
    }

    private function setStartDate()
    {
        if (array_key_exists('startDate', $this->validatedData))
            $this->validatedData['startDate'] = Carbon::parse($this->validatedData['startDate']);
        return $this;
    }

    private function setEndDate()
    {
        if (array_key_exists('endDate', $this->validatedData))
            $this->validatedData['endDate'] = Carbon::parse($this->validatedData['endDate']);
        return $this;
    }

    private function validateRequest()
    {
        $this->validatedData = $this->validate($this->request, [
            "trackingReference" => 'required|string',
            "pageSize" => 'nullable|numeric',
            "pageNumber" => 'nullable|numeric',
            "startDate" => 'nullable',
            "endDate" => 'nullable',
        ]);

        return $this;
    }
}