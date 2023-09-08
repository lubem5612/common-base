<?php


namespace Transave\CommonBase\Tests\Feature\Kuda\Account;


use Transave\CommonBase\Actions\Kuda\Account\ListVirtualAccounts;
use Transave\CommonBase\Database\Seeders\KudaAccountSeeder;
use Transave\CommonBase\Tests\TestCase;

class ListVirtualAccountsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        (new KudaAccountSeeder())->run();
    }

    /** @test */
    public function can_fetch_all_virtual_accounts()
    {
        $response = (new ListVirtualAccounts(['PageSize' => '3', 'PageNumber' => '1']))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']['accounts']);
    }
}