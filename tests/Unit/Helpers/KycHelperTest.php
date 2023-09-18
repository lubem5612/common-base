<?php


namespace Transave\CommonBase\Tests\Unit\Helpers;


use Transave\CommonBase\Helpers\KycHelper;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class KycHelperTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->where('role', 'customer')->first();
    }

    /** @test */
    function can_get_account_kyc()
    {
        $response = (new KycHelper(['user_id' => $this->user->id]))->execute();
        $this->assertTrue($response['success']);
        $this->assertNotNull($response['data']);
        dd($response['data']);
    }
}