<?php

namespace Raadaapartners\Raadaabase;

use Raadaapartners\Raadaabase\Kuda\Account\CreateVirtualAccount;
use Raadaapartners\Raadaabase\Kuda\Account\DisableVirtualAccount;
use Raadaapartners\Raadaabase\Kuda\Account\EnableVirtualAccount;
use Raadaapartners\Raadaabase\Kuda\Account\GetVirtualAccount;
use Raadaapartners\Raadaabase\Kuda\Account\ListVirtualAccounts;
use Raadaapartners\Raadaabase\Kuda\Account\MainBalanceCheck;
use Raadaapartners\Raadaabase\Kuda\Account\UpdateVirtualAccount;
use Raadaapartners\Raadaabase\Kuda\Account\VirtualBalanceCheck;
use Raadaapartners\Raadaabase\Kuda\Transaction\MainAccountTransactions;
use Raadaapartners\Raadaabase\Kuda\Transaction\TransactionStatusQuery;
use Raadaapartners\Raadaabase\Kuda\Transaction\VirtualAccountTransactions;
use Raadaapartners\Raadaabase\Kuda\Transfer\BankList;
use Raadaapartners\Raadaabase\Kuda\Transfer\MainAccountFundTransfer;
use Raadaapartners\Raadaabase\Kuda\Transfer\NameEnquiry;
use Raadaapartners\Raadaabase\Kuda\Transfer\VirtualAccountFundTransfer;
use Raadaapartners\Raadaabase\SMS\SendChamp;
use Raadaapartners\Raadaabase\SMS\Termii;

class Raadaabase
{
    private $result;

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
        return (new MainBalanceCheck())->handle();
    }

    public function getVirtualAccountBalance(string $trackingReference)
    {
        return (new VirtualBalanceCheck($trackingReference))->handle();
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
        return (new TransactionStatusQuery($transactionReference, $isThirdParty))->handle();
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
