<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class KycModelTest extends TestCase
{
    private $kyc;

    public function setUp(): void
    {
        parent::setUp();
        $this->kyc = Kyc::factory()->create();
    }

    /** @test */
    public function kyc_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->kyc instanceof Kyc);
    }

    /** @test */
    public function kyc_table_exists_in_database()
    {
        $this->assertModelExists($this->kyc);
    }

    /** @test */
    public function kyc_model_belongs_to_user_model()
    {
        $user = User::factory()->create();
        $kyc = Kyc::factory()->for($user)->create();
        $this->assertTrue($this->kyc->user instanceof User);
    }
}