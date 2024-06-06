<?php

namespace Database\Factories;

use App\Models\Skin2;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class Skin2Factory extends Factory
{
    protected $model = Skin2::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'hair_version' => $this->faker->numberBetween(1, 10),
            'hair_color' => $this->faker->safeColorName(),
            'upper_body_color' => $this->faker->safeColorName(),
            'lower_body_color' => $this->faker->safeColorName(),
            'skin_color' => $this->faker->safeColorName()
        ];
    }
}
