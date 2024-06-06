<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'file_path' => $this->faker->filePath(),
            'subject_id' => Subject::factory(),
        ];
    }
}
