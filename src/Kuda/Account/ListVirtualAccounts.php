<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Raadaapartners\Raadaabase\Helpers\ManageResponse;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class ListVirtualAccounts
{
    use ManageResponse, PostRequestHelper;

    private $page_size;
    private $page_number;

    public function __construct($PageSize, $PageNumber)
    {
        $this->page_size = $PageSize;
        $this->page_number = $PageNumber;
    }

    public function handle()
    {
        try {
            return $this->fetchAllAccounts();
        }catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }
    
    private function fetchAllAccounts()
    {
        $data = [
            "PageSize" => $this->page_size,
            "PageNumber" => $this->page_number,
        ];
        $callback = $this->processKuda(config('constants.list_virtual_accounts'), $data);
        if ($callback["errors"]) {
            return $this->errorResponse('error in fetching accounts', $callback['errors']);
        }
        return $this->successResponse('account list retrieved', $callback['data']['data']);
    }
}
