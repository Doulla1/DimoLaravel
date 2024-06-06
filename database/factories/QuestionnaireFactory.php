<?php

namespace Database\Factories;

use App\Models\Questionnaire;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireFactory extends Factory
{
    protected $model = Questionnaire::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'subject_id' => Subject::factory(),
            'is_visible' => $this->faker->boolean,
        ];
    }
}
