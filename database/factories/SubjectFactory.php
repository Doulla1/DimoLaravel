<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'illustration' => $this->faker->imageUrl,
            'program_id' => Program::factory(),
        ];
    }
}
