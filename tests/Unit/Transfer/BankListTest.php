<?php


namespace Raadaapartners\Raadaabase\Tests\Unit\Transfer;


use Raadaapartners\Raadaabase\Kuda\Transfer\BankList;
use Raadaapartners\Raadaabase\Tests\TestCase;

class BankListTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_bank_list_successfully()
    {
        $response = (new BankList())->handle();
        $this->assertEquals($response['success'], true);
        $this->assertNotNull($response['data']);
    }
}