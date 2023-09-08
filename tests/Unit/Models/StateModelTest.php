<?php


namespace Transave\CommonBase\Tests\Unit\Models;


use Transave\CommonBase\Http\Models\Country;
use Transave\CommonBase\Http\Models\State;
use Transave\CommonBase\Tests\TestCase;

class StateModelTest extends TestCase
{
    private $state;
    public function setUp(): void
    {
        parent::setUp();
        $this->state = State::factory()->create();
    }

    public function state_model_can_be_initiated_with_factory()
    {
        $this->assertTrue($this->state instanceof State);
    }

    /** @test */
    public function state_table_exists_in_database()
    {
        $this->assertModelExists($this->state);
    }

    /** @test */
    public function state_model_belongs_to_country_model()
    {
        $country = Country::factory()->create();
        $state = State::factory()->for($country)->create();
        $this->assertTrue($this->state->country instanceof Country);
    }
}