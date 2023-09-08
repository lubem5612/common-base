<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\Country;
use Transave\CommonBase\Http\Models\Lga;

class LgaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lga::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'state_id' => Country::factory(),
            'name' => $this->faker->name,
        ];
    }
}