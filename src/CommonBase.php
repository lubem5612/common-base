<?php

namespace Transave\CommonBase;

use Transave\CommonBase\Kuda\Account\CreateVirtualAccount;
use Transave\CommonBase\Kuda\Account\DisableVirtualAccount;
use Transave\CommonBase\Kuda\Account\EnableVirtualAccount;
use Transave\CommonBase\Kuda\Account\GetVirtualAccount;
use Transave\CommonBase\Kuda\Account\ListVirtualAccounts;
use Transave\CommonBase\Kuda\Account\MainAccountBalance;
use Transave\CommonBase\Kuda\Account\UpdateVirtualAccount;
use Transave\CommonBase\Kuda\Account\VirtualAccountBalance;
use Transave\CommonBase\Kuda\Transaction\MainAccountTransactions;
use Transave\CommonBase\Kuda\Transaction\QueryTransactionStatus;
use Transave\CommonBase\Kuda\Transaction\VirtualAccountTransactions;
use Transave\CommonBase\Kuda\Transfer\BankList;
use Transave\CommonBase\Kuda\Transfer\MainAccountFundTransfer;
use Transave\CommonBase\Kuda\Transfer\NameEnquiry;
use Transave\CommonBase\Kuda\Transfer\VirtualAccountFundTransfer;
use Transave\CommonBase\SMS\SendChamp;
use Transave\CommonBase\SMS\Termii;

class CommonBase
{
    public function createVirtualAccount(array $data)
    {
        return (new CreateVirtualAccount($data))->handle();
    }

    public function updateVirtualAccount(array $data)
    {
        return (new UpdateVirtualAccount($data))->handle();
    }

    public function disableVirtualAccount(string $trackingReference)
    {
        return (new DisableVirtualAccount($trackingReference))->handle();
    }

    public function enableVirtualAccount(string $trackingReference)
    {
        return (new EnableVirtualAccount($trackingReference))->handle();
    }

    public function getVirtualAccount(string $trackingReference)
    {
        return (new GetVirtualAccount($trackingReference))->handle();
    }

    public function ListVirtualAccounts($page_size, $page_number)
    {
        return (new ListVirtualAccounts($page_size, $page_number))->handle();
    }

    public function getMainAccountBalance()
    {
        return (new MainAccountBalance())->handle();
    }

    public function getVirtualAccountBalance(string $trackingReference)
    {
        return (new VirtualAccountBalance($trackingReference))->handle();
    }

    public function getBankList()
    {
        return (new BankList())->handle();
    }

    public function getBeneficiaryName(array $data)
    {
        return (new NameEnquiry($data))->handle();
    }

    public function outwardMainAccountTransfer(array $data)
    {
        return (new MainAccountFundTransfer($data))->handle();
    }

    public function outwardVirtualAccountTransfer(array $data)
    {
        return (new VirtualAccountFundTransfer($data))->handle();
    }

    public function getMainAccountTransaction($pageSize, $pageNumber, $startDate = "", $endDate = "")
    {
        return (new MainAccountTransactions($pageSize, $pageNumber, $startDate = "", $endDate = ""))->handle();
    }

    public function getVirtualAccountTransaction($pageSize, $pageNumber, $startDate = "", $endDate = "")
    {
        return (new VirtualAccountTransactions($pageSize, $pageNumber, $startDate = "", $endDate = ""))->handle();
    }

    public function checkTransactionStatus($transactionReference, $isThirdParty)
    {
        return (new QueryTransactionStatus($transactionReference, $isThirdParty))->handle();
    }

    public function sendWithTermii($number, $message)
    {
        return (new Termii($number, $message))->sendSMS();
    }

    public function sendWithSendChamp(array $numbers, string $message)
    {
        return (new SendChamp($numbers, $message))->sendSMS();
    }
}