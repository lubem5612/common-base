<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Http\Models\Wallet;
use Transave\CommonBase\Tests\TestCase;

class WalletModelTest extends TestCase
{
    private $wallet;
    public function setUp(): void
    {
        parent::setUp();
        $this->wallet = Wallet::factory()->create();
    }

    /** @test */
    public function wallet_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->wallet instanceof Wallet);
    }

    /** @test */
    public function wallet_table_exists_in_database()
    {
        $this->assertModelExists($this->wallet);
    }

    /** @test */
    public function wallet_model_belongs_to_user_model()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->for($user)->create();
        $this->assertTrue($wallet->user instanceof User);
    }
}