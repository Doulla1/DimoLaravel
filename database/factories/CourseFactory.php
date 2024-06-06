<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\Subject;
use App\Models\Lobby;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'teacher_id' => User::factory(), // Assure que le teacher est un utilisateur valide
            'subject_id' => Subject::factory(), // Assure que le subject est une matière valide
            'lobby_id' => Lobby::factory(), // Assure que le lobby est un lobby valide
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_active' => $this->faker->boolean,
        ];
    }
}
