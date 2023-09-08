<?php


namespace Transave\CommonBase\Tests\Unit\Commands;


use Illuminate\Support\Facades\Artisan;
use Transave\CommonBase\Tests\TestCase;

class SeederTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    function can_seed_database_successfully()
    {
        Artisan::call('transave:seed');
        $this->assertDatabaseCount('states', 37);
        $this->assertDatabaseHas('countries', ['name' => 'Nigeria']);
        $this->assertDatabaseHas('lgas', ['name' => 'Bogoro']);
        $this->assertDatabaseHas('users', ['role' => 'admin', 'account_type' => 'super']);
    }
}