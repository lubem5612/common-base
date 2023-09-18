<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Transave\CommonBase\Http\Models\Support;
use Transave\CommonBase\Http\Models\User;

class SupportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Support::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->title,
            'content' => $this->faker->sentence(10),
            'type' => $this->faker->randomElement(['FAILED_TRANSACTION', 'ACCOUNT_UPGRADE', 'AUTH_ISSUES']),
            'status' => $this->faker->randomElement(['closed', 'archived', 'opened']),
            'file' => UploadedFile::fake()->create('file.pdf', '200', 'application/pdf'),
        ];
    }
}