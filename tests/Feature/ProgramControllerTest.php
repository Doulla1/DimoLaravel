<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProgramControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAll()
    {
        Program::factory()->count(3)->create();
        $response = $this->getJson('/api/programs');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        Storage::fake('public');
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $data = [
            'name' => 'New Program',
            'description' => 'Program Description',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'illustration' => UploadedFile::fake()->image('illustration.jpg')
        ];

        $response = $this->postJson('/api/programs', $data);

        $response->assertStatus(200);
    }

    public function testGetStudents()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $program = Program::factory()->create();
        $response = $this->getJson("/api/programs/{$program->id}/students");

        $response->assertStatus(200)
            ->assertJsonStructure(['students']);
    }

    public function testGetByConnectedStudent()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student, 'sanctum');

        $response = $this->getJson('/api/student-programs');

        $response->assertStatus(200)
            ->assertJsonStructure(['programs']);
    }

    public function testRegisterStudent()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student, 'sanctum');

        $program = Program::factory()->create();

        $response = $this->postJson('/api/programs/register', ['program_id' => $program->id]);

        $response->assertStatus(200)
            ->assertJsonStructure(['program']);
    }


    public function testGetByConnectedTeacher()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        Program::factory()->count(3)->create(['head_teacher_id' => $teacher->id]);

        $response = $this->getJson('/api/teached-programs');

        $response->assertStatus(200)
            ->assertJsonStructure(['programs']);
    }

    public function testGetById()
    {
        $program = Program::factory()->create();
        $response = $this->getJson("/api/programs/{$program->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['program']);
    }

    public function testGetSubjectsByProgram()
    {
        $program = Program::factory()->create();
        $response = $this->getJson("/api/programs/{$program->id}/subjects");

        $response->assertStatus(200)
            ->assertJsonStructure(['subjects']);
    }

    public function testIsRegistered()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student, 'sanctum');

        $program = Program::factory()->create();

        $response = $this->getJson("/api/is-registered/{$program->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['isRegistered']);
    }

    public function testUpdate()
    {
        Storage::fake('public');
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $program = Program::factory()->create(['head_teacher_id' => $teacher->id]);

        $data = [
            'name' => 'Updated Program',
            'description' => 'Updated Description',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'illustration' => UploadedFile::fake()->image('illustration.jpg')
        ];

        $response = $this->putJson("/api/programs/{$program->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['program']);
    }
}