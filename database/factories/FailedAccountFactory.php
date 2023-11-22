<?php


namespace Transave\CommonBase\Database\Factories;



use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Transave\CommonBase\Http\Models\FailedAccount;

class FailedAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FailedAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dump = []; $response = [];
        foreach (range(1,6) as $item) array_push($dump, $this->faker->word);
        foreach (range(1,6) as $item) array_push($response, $this->faker->word);

        return [
            'reference_id' => Str::uuid()->toString(),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'data_dump' => json_encode($dump),
            'response' => json_encode($response)
        ];
    }
}