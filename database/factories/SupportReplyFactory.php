<?php


namespace Transave\CommonBase\Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Transave\CommonBase\Http\Models\Support;
use Transave\CommonBase\Http\Models\SupportReply;
use Transave\CommonBase\Http\Models\User;

class SupportReplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupportReply::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'support_id' => Support::factory(),
            'content' => $this->faker->sentence(10),
            'file' => UploadedFile::fake()->create('file.pdf', '200', 'application/pdf'),
        ];
    }
}