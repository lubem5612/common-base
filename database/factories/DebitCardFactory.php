<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Transave\CommonBase\Http\Models\DebitCard;
use Transave\CommonBase\Http\Models\User;

class DebitCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DebitCard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'first_digits' => rand(100000, 999999),
            'last_digits' => rand(1000, 9999),
            'issuer' => $this->faker->company(),
            'email' => $this->faker->safeEmail,
            'type' => $this->faker->randomElement(['MASTERCARD', 'VERVECARD', 'VISACARD']),
            'country' => $this->faker->country,
            'expiry' => rand(10, 99).'/'.rand(10, 99),
            'token' => Str::random(30),
            'is_third_party' => $this->faker->randomElement(['yes', 'no']),
            'currency' => $this->faker->currencyCode,
        ];
    }
}