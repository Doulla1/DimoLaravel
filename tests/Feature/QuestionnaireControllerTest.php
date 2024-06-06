<?php

namespace Tests\Feature;

use App\Models\Questionnaire;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionnaireControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAll()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        Questionnaire::factory()->count(3)->create();
        $response = $this->getJson('/api/questionnaires');

        $response->assertStatus(200)
            ->assertJsonStructure(['questionnaires' => [['id', 'title', 'description', 'subject_id', 'is_visible']]]);
    }

    public function testGetUnique()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');
        
        $questionnaire = Questionnaire::factory()->create();
        $response = $this->getJson("/api/questionnaires/{$questionnaire->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['questionnaire' => ['id', 'title', 'description', 'subject_id', 'is_visible']]);
    }

    public function testCreate()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $subject = Subject::factory()->create();

        $data = [
            'title' => 'New Questionnaire',
            'description' => 'Questionnaire Description',
            'subject_id' => $subject->id
        ];

        $response = $this->postJson('/api/questionnaires', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['questionnaire' => ['id', 'title', 'description', 'subject_id', 'is_visible']]);
    }

    public function testCreateFull()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $subject = Subject::factory()->create();

        $data = [
            'title' => 'New Full Questionnaire',
            'description' => 'Full Questionnaire Description',
            'subject_id' => $subject->id,
            'questions' => [
                [
                    'text' => 'Question 1',
                    'order' => 1,
                    'options' => [
                        ['text' => 'Option 1', 'is_correct' => true],
                        ['text' => 'Option 2', 'is_correct' => false]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/questionnaires/create-full', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['questionnaire' => ['id', 'title', 'description', 'subject_id', 'is_visible', 'questions']]);
    }

    public function testUpdate()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $questionnaire = Questionnaire::factory()->create();

        $data = [
            'title' => 'Updated Questionnaire',
            'description' => 'Updated Description',
            'subject_id' => $questionnaire->subject_id
        ];

        $response = $this->putJson("/api/questionnaires/{$questionnaire->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['questionnaire' => ['id', 'title', 'description', 'subject_id', 'is_visible']]);
    }

    public function testDelete()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $questionnaire = Questionnaire::factory()->create();

        $response = $this->deleteJson("/api/questionnaires/{$questionnaire->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Questionnaire deleted']);
    }

    public function testGetBySubject()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $subject = Subject::factory()->create();
        Questionnaire::factory()->count(3)->create(['subject_id' => $subject->id]);

        $response = $this->getJson("/api/questionnaires/subject/{$subject->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['questionnaires' => [['id', 'title', 'description', 'subject_id', 'is_visible']]]);
    }

    public function testSaveAnswers()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student, 'sanctum');

        $questionnaire = Questionnaire::factory()->create();
        $question = $questionnaire->questions()->create(['text' => 'Question 1', 'order' => 1]);
        $option = $question->options()->create(['text' => 'Option 1', 'is_correct' => true]);

        $data = [
            'id' => $questionnaire->id,
            'questions' => [
                [
                    'id' => $question->id,
                    'options' => [
                        ['id' => $option->id, 'selected' => true]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/questionnaires/answers', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['score']);
    }
}