<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetById()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $subject = Subject::factory()->create();
        $response = $this->getJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['subject' => ['id', 'title', 'description', 'illustration', 'program_id']]);
    }

    public function testCreate()
    {
        Storage::fake('public');
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $program = Program::factory()->create();

        $data = [
            'title' => 'New Subject',
            'description' => 'Subject Description',
            'illustration' => UploadedFile::fake()->image('illustration.jpg'),
            'program_id' => $program->id
        ];

        $response = $this->postJson('/api/subjects', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['subject' => ['id', 'title', 'description', 'illustration', 'program_id']]);
    }



    public function testLeaveSubject()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $subject = Subject::factory()->create();

        // Join the subject first
        $teacher->teachedSubjects()->attach($subject->id);

        $response = $this->postJson('/api/leave-subject', ['subject_id' => $subject->id]);

        $response->assertStatus(200)
            ->assertJsonStructure(['subject' => ['id', 'title', 'description', 'illustration', 'program_id']]);
    }

    public function testDelete()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $subject = Subject::factory()->create();

        $response = $this->deleteJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Subject deleted']);
    }
}