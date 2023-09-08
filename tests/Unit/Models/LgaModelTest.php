<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\Lga;
use Transave\CommonBase\Http\Models\State;
use Transave\CommonBase\Tests\TestCase;

class LgaModelTest extends TestCase
{
    private $lga;
    public function setUp(): void
    {
        parent::setUp();
        $this->lga = Lga::factory()->create();
    }

    public function lga_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->lga instanceof Kyc);
    }

    /** @test */
    public function lga_table_exists_in_database()
    {
        $this->assertModelExists($this->lga);
    }

    /** @test */
    public function lga_model_belongs_to_state_model()
    {
        $state = State::factory()->create();
        $lga = Lga::factory()->for($state)->create();
        $this->assertTrue($this->lga->state instanceof State);
    }
}