<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Transave\CommonBase\Http\Models\Transaction;
use Transave\CommonBase\Http\Models\User;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'reference' => Str::random(32),
            'amount' => $this->faker->randomFloat(6, 1000, 20000),
            'charges' => $this->faker->randomFloat(6, 200, 1000),
            'commission' => $this->faker->randomFloat(6, 200, 1000),
            'type' => $this->faker->randomElement(['debit', 'credit']),
            'description' => $this->faker->sentence,
            'category' => $this->faker->word,
            'status' => $this->faker->randomElement(['pending', 'successful', 'failed', 'reversed', 'cancelled']),
            'payload' => json_encode(['unverified', 'verified', 'suspended', 'banned']),
        ];
    }
}