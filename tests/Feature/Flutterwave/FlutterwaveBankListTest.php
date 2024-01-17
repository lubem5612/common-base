<?php


namespace Transave\CommonBase\Tests\Feature\Flutterwave;


use Transave\CommonBase\Actions\Flutterwave\FlutterwaveBankList;
use Transave\CommonBase\Tests\TestCase;

class FlutterwaveBankListTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    function can_get_all_flutterwave_bank_list()
    {
        $response = (new FlutterwaveBankList(['country' => 'NG']))->execute();
        $this->assertTrue($response['success']);
        $this->assertIsArray($response['data']);
    }
}