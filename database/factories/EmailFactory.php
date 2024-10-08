<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'from_email' => $this->faker->unique()->safeEmail,
            'to_email' => $this->faker->unique()->safeEmail,
            'subject' => $this->faker->sentence,
            'body_text' => $this->faker->paragraph,
        ];
    }
}
