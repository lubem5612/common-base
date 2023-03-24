<?php


namespace Raadaapartners\Raadaabase\Kuda\Account;


use Illuminate\Support\Facades\Log;
use Raadaapartners\RaadaaBase\Helpers\ResponseHelper;
use Raadaapartners\Raadaabase\Kuda\Helpers\PostRequestHelper;

class ListVirtualAccounts
{
    use ResponseHelper, PostRequestHelper;

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
            Log::error($e);
            $this->message = $e->getMessage();
            return $this->buildResponse();
        }
    }

    private function fetchAllAccounts()
    {
        $data = [
            "PageSize" => $this->page_size,
            "PageNumber" => $this->page_number,
        ];
        return $this->processKuda(config('constants.list_virtual_accounts'), $data);
    }
}