<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\User;
use Transave\CommonBase\Http\Models\Wallet;

class WalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'balance' => rand(10000, 30000),
            'previous_balance' => rand(5000, 9999),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}