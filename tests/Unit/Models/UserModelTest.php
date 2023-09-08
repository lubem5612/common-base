<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class UserModelTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->user instanceof User);
    }

    /** @test */
    public function user_table_exists_in_database()
    {
        $this->assertModelExists($this->user);
    }
}