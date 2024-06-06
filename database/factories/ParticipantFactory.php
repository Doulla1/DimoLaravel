<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'is_currently_present' => $this->faker->boolean,
        ];
    }
}
