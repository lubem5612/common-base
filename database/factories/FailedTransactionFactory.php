<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\FailedTransaction;
use Transave\CommonBase\Http\Models\Transaction;
use Transave\CommonBase\Http\Models\User;

class FailedTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FailedTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'payload' => json_encode(['unverified', 'verified', 'suspended', 'banned']),
        ];
    }
}