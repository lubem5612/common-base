<?php


namespace Transave\CommonBase\Database\Factories;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Transave\CommonBase\Http\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'middle_name' => $this->faker->name,
            'business_name' => $this->faker->company,
            'phone' => $this->faker->phoneNumber,
            'bvn' => rand(10000000000, 99999999999),
            'account_number' => rand(3000000000, 9999999999),
            'withdrawal_limit' => $this->faker->randomDigit(),
            'role' => $this->faker->randomElement(['customer', 'staff', 'admin']),
            'verification_token' => Str::random(6),
            'account_verified_at' => Carbon::now(),
            'is_verified' => $this->faker->randomElement(['yes', 'no']),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'account_type' => $this->faker->randomElement(['ordinary', 'classic', 'premium', 'super']),
            'transaction_pin' => rand(1000, 9999),
            'account_status' => $this->faker->randomElement(['unverified', 'verified', 'suspended', 'banned']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'account_verified_at' => null,
            ];
        });
    }
}