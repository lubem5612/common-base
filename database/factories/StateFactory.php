<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\Country;
use Transave\CommonBase\Http\Models\State;

class StateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = State::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'country_id' => Country::factory(),
            'name' => $this->faker->name,
            'capital' => $this->faker->city,
        ];
    }
}