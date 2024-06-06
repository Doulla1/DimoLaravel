<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testDelete()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $question = Question::factory()->create();

        $response = $this->deleteJson("/api/questions/{$question->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Question deleted']);
    }
}