<?php


namespace Transave\CommonBase\Database\Factories;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Transave\CommonBase\Http\Models\Country;
use Transave\CommonBase\Http\Models\Kyc;
use Transave\CommonBase\Http\Models\Lga;
use Transave\CommonBase\Http\Models\State;
use Transave\CommonBase\Http\Models\User;

class KycFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Kyc::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'image_url' => UploadedFile::fake()->image('photo.jpg'),
            'identity_card_url' => UploadedFile::fake()->image('id_card.jpg'),
            'address_proof_url' => UploadedFile::fake()->image('address.jpg'),
            'identity_type' => $this->faker->randomElement(['voter-card', 'NIN', 'driver-license', 'int-passport']),
            'identity_card_number' => 'NGN-'.$this->faker->randomDigit(),
            'country_of_origin_id' => Country::factory(),
            'country_of_residence_id' => Country::factory(),
            'state_id' => State::factory(),
            'lga_id' => Lga::factory(),
            'city' => $this->faker->city,
            'next_of_kin' => $this->faker->name,
            'next_of_kin_contact' => $this->faker->phoneNumber,
            'mother_maiden_name' => $this->faker->name,
            'residential_status' => $this->faker->randomElement(['tenant', 'house-owner', 'family']),
            'employment_status' => $this->faker->randomElement(['part-time', 'full-time', 'contract', 'unemployed']),
            'employer' => $this->faker->company,
            'job_title' => $this->faker->sentence(2, 6),
            'educational_qualification' => $this->faker->sentence(2, 6),
            'date_of_employment' => Carbon::now()->subYears(20),
            'number_of_children' => rand(1, 6),
            'income_range' => json_encode(['min' => rand(10000, 100000), 'max' => rand(200000, 400000)]),
            'verification_status' => $this->faker->randomElement(['verified', 'incomplete', 'unverified']),
            'is_loan_compliant' => $this->faker->randomElement(['no', 'yes']),
        ];
    }
}