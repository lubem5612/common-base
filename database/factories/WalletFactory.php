<?php


namespace Transave\CommonBase\Database\Factories;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\User;

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
            'user_id'           => User::factory(),
            'balance'           => 0,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ];
    }
}
