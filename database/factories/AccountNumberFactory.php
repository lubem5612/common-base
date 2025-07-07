<?php


namespace Transave\CommonBase\Database\Factories;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\AccountNumber;
use Transave\CommonBase\Http\Models\User;

class AccountNumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountNumber::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'           => User::factory(),
            'account_number'    => "1001" . mt_rand(123456, 999999),
            'bank_name'         => config('commonbase.vfd.bank_name'),
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ];
    }
}
