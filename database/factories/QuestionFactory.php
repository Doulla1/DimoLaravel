<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'text' => $this->faker->sentence,
            'questionnaire_id' => Questionnaire::factory(),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
