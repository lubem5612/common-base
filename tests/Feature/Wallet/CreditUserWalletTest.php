<?php


namespace Transave\CommonBase\Tests\Feature\Wallet;


use Faker\Factory;
use Laravel\Sanctum\Sanctum;
use Transave\CommonBase\Actions\Wallet\CreditUserWallet;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Http\Models\Wallet;
use Transave\CommonBase\Tests\TestCase;

class CreditUserWalletTest extends TestCase
{
    protected $user, $faker, $request;
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->user = User::factory()
            ->has(Kyc::factory())->create();
        Wallet::factory()->create(['status' => 'active', 'user_id' => $this->user->id]);
        Sanctum::actingAs($this->user);
        $this->testData();
    }

    /** @test */
    public function can_credit_wallet_successfully()
    {
        $response = (new CreditUserWallet($this->request))->execute();
        $array = json_decode($response->getContent(), true);
        $this->assertTrue($array['success']);
        $this->assertNotNull($array['data']);
    }

    private function testData()
    {
        $this->request = [
            "user_id" => $this->user->id,
            "amount" => rand(1000, 2000),
            "commission" => $this->faker->randomFloat(3, 10, 30),
            "charge" => $this->faker->randomFloat(3, 10, 30),
            'description' => $this->faker->sentence,
            'category' => strtoupper('user_deposit'),
            'status' => 'successful',
            'payload' => json_encode(['status' => 'successful', 'type' => 'credit']),
        ];
    }
}