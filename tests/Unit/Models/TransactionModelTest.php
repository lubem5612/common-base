<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\Transaction;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class TransactionModelTest extends TestCase
{
    private $transaction;
    public function setUp(): void
    {
        parent::setUp();
        $this->transaction = Transaction::factory()->create();
    }

    /** @test */
    public function transaction_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->transaction instanceof Transaction);
    }

    /** @test */
    public function transaction_table_exists_in_database()
    {
        $this->assertModelExists($this->transaction);
    }

    /** @test */
    public function transaction_model_belongs_to_user_model()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();
        $this->assertTrue($transaction->user instanceof User);
    }
}