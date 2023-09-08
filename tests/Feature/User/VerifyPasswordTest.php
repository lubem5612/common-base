<?php


namespace Transave\CommonBase\Tests\Feature\User;


use Transave\CommonBase\Actions\User\VerifyPassword;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Tests\TestCase;

class VerifyPasswordTest extends TestCase
{
    private $user, $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => bcrypt('random123')]);
    }

    /** @test */
    public function can_check_if_password_matches()
    {
        $response = (new VerifyPassword(['password' => 'random123', 'user_id' => $this->user->id]))->execute();
        $array = json_decode($response->getContent(), true);
        $this->assertTrue($array['success'], 'password matches');
    }

    /** @test */
    public function can_check_if_password_not_matches()
    {
        $response = (new VerifyPassword(['password' => 'random1234', 'user_id' => $this->user->id]))->execute();
        $array = json_decode($response->getContent(), true);
        $this->assertNotTrue($array['success'], 'password does not match');
    }
}