<?php


namespace Transave\CommonBase\Kuda\Transaction;


use Transave\CommonBase\Helpers\ManageResponse;
use Transave\CommonBase\Kuda\Helpers\Api;

class VirtualAccountTransactions
{
    use ManageResponse, Api;

    private $page_size;
    private $page_number;
    private $start_date;
    private $end_date;
    private $trackingReference;
    private $serviceType;

    public function __construct($PageSize, $PageNumber, $trackingReference, $startDate = "", $endDate = "")
    {
        $this->page_size = $PageSize;
        $this->page_number = $PageNumber;
        $this->start_date = $startDate;
        $this->end_date = $endDate;
        $this->trackingReference = $trackingReference;
    }

    public function handle()
    {
        try {
            return $this->virtualAccountFetchAllTransactions();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    private function virtualAccountFetchAllTransactions()
    {
        $data = [
            "trackingReference" => $this->trackingReference,
            "pageSize" => $this->page_size,
            "pageNumber" => $this->page_number,
        ];
        if (isset($this->start_date) && isset($this->end_date))
        {
            $data["startDate"] = $this->start_date;
            $data["endDate"] = $this->end_date;
            $this->serviceType = config('constants.transaction.virtual_account_filter');
        }else {
            $this->serviceType = config('constants.transaction.virtual_account');
        }

        $callback = $this->processKuda($this->serviceType, $data);
        if ($callback["errors"]) {
            return $this->errorResponse('error in fetching transactions', $callback['errors']);
        }
        return $this->successResponse('transactions list retrieved', $callback['data']['data']);
    }
}