<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Transave\CommonBase\Http\Models\Country;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->countryCode,
            'name' => $this->faker->country(),
            'continent' => $this->faker->randomElement(['Africa', 'Asia', 'South America', 'North America', 'Europe', 'Australia', 'Antarctica']),
        ];
    }
}