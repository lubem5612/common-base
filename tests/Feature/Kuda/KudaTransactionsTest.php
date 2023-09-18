<?php


namespace Transave\CommonBase\Tests\Feature\Kuda;



use Carbon\Carbon;
use Transave\CommonBase\Actions\Kuda\Transaction\MainAccountTransactions;
use Transave\CommonBase\Actions\Kuda\Transaction\QueryTransactionStatus;
use Transave\CommonBase\Tests\TestCase;

class KudaTransactionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    function can_fetch_main_account_transactions()
    {
        $inputs = [
            "pageSize" => '10',
            "pageNumber" => '1',
        ];
        $response = (new MainAccountTransactions($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['postingsHistory']);
    }

    /** @test */
    function can_fetch_main_account_transactions_with_filter()
    {
        $inputs = [
            "pageSize" => '10',
            "pageNumber" => '1',
            "startDate" => Carbon::now()->subDays(1)->format('Y-m-d'),
            "endDate" => Carbon::now()->addDays(1)->format('Y-m-d'),
        ];
        $response = (new MainAccountTransactions($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['postingsHistory']);
    }

    /** @test */
    function can_fetch_virtual_account_transactions()
    {
        $inputs = [
            "user_id" => "c0329e2b-67d8-4c30-8bd9-51208c0b5975",
            "pageSize" => '10',
            "pageNumber" => '1',
        ];
        $response = (new MainAccountTransactions($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['postingsHistory']);
    }

    /** @test */
    function can_fetch_virtual_account_transactions_with_filter()
    {
        $inputs = [
            "user_id" => "c0329e2b-67d8-4c30-8bd9-51208c0b5975",
            "pageSize" => '10',
            "pageNumber" => '1',
            "startDate" => Carbon::now()->subDays(1)->format('Y-m-d'),
            "endDate" => Carbon::now()->addDays(1)->format('Y-m-d'),
        ];
        $response = (new MainAccountTransactions($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['postingsHistory']);
    }

    /** @test */
    function can_query_transaction_status()
    {
        $reference = "transave-202309091735-8fsvdd36f";
        $inputs = [
            "isThirdPartyBankTransfer" => "false",
            "transactionRequestReference" => $reference
        ];
        $response = (new QueryTransactionStatus($inputs))->execute();
        $this->assertTrue($response['success']);
        $this->assertNull($response['data']);
    }
}