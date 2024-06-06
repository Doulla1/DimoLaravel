<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition()
    {
        return [
            'text' => $this->faker->sentence,
            'question_id' => Question::factory(),
            'is_correct' => $this->faker->boolean,
        ];
    }
}
