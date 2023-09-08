<?php


namespace Transave\CommonBase\Tests\Feature\Kuda\Account;


use Faker\Factory;
use Transave\CommonBase\Actions\Kuda\Account\CreateVirtualAccount;
use Transave\CommonBase\Tests\TestCase;

class CreateVirtualAccountTest extends TestCase
{
    private $request;
    private $faker;
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->testData();
    }

    /** @test */
    public function can_create_virtual_account_successfully()
    {
        $response = (new CreateVirtualAccount($this->request))->execute();
        $arrayData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $arrayData['success']);
        $this->assertNull($arrayData['data']);
    }

    private function testData()
    {
        $this->request = [
            "email" => $this->faker->safeEmail,
            "phone" => '080'.(string)rand(30000000, 99999999),
            "last_name" => explode(' ', $this->faker->name, 2)[1],
            "first_name" => explode(' ', $this->faker->name, 2)[1],
            "password" => 'password',
            "password_confirmation" => "password"
        ];
    }
}