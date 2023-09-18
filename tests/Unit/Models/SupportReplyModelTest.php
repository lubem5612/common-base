<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\Support;
use Transave\CommonBase\Http\Models\SupportReply;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class SupportReplyModelTest extends TestCase
{
    private $support_reply;
    public function setUp(): void
    {
        parent::setUp();
        $this->support_reply = SupportReply::factory()->create();
    }

    /** @test */
    public function support_reply_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->support_reply instanceof SupportReply);
    }

    /** @test */
    public function support_reply_table_exists_in_database()
    {
        $this->assertModelExists($this->support_reply);
    }

    /** @test */
    public function support_reply_model_belongs_to_user_model()
    {
        $user = User::factory()->create();
        $support_reply = SupportReply::factory()->for($user)->create();
        $this->assertTrue($support_reply->user instanceof User);
    }
    
    /** @test */
    public function support_reply_model_belongs_to_support_model()
    {
        $support = Support::factory()->create();
        $support_reply = SupportReply::factory()->for($support)->create();
        $this->assertTrue($support_reply->support instanceof Support);
    }
}