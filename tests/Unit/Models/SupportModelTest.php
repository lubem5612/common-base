<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\Support;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class SupportModelTest extends TestCase
{
    private $support;
    public function setUp(): void
    {
        parent::setUp();
        $this->support = Support::factory()->create();
    }

    /** @test */
    public function support_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->support instanceof Support);
    }

    /** @test */
    public function support_table_exists_in_database()
    {
        $this->assertModelExists($this->support);
    }

    /** @test */
    public function support_model_belongs_to_user_model()
    {
        $user = User::factory()->create();
        $support = Support::factory()->for($user)->create();
        $this->assertTrue($support->user instanceof User);
    }
}