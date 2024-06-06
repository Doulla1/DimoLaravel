<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lobby;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAll()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        Course::factory()->count(3)->create();
        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonStructure(['courses' => [['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]]);
    }

    public function testGetById()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('admin');
        $this->actingAs($teacher, 'sanctum');
        
        $course = Course::factory()->create();
        $response = $this->getJson("/api/admin/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['course' => ['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]);
    }

    public function testUpdate()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $data = [
            'start_date' => '2024-01-01 10:00:00',
            'end_date' => '2024-01-01 12:00:00'
        ];

        $response = $this->putJson("/api/courses/{$course->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['course' => ['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]);
    }

    public function testDelete()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->deleteJson("/api/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function testGetByTeacherId()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        Course::factory()->count(3)->create(['teacher_id' => $teacher->id]);

        $response = $this->getJson("/api/admin/courses/teacher/{$teacher->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['courses' => [['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]]);
    }

    public function testGetByConnectedTeacher()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        Course::factory()->count(3)->create(['teacher_id' => $teacher->id]);

        $response = $this->getJson('/api/teached-courses');

        $response->assertStatus(200)
            ->assertJsonStructure(['courses' => [['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]]);
    }


    public function testGetBySubject()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $subject = Subject::factory()->create();
        Course::factory()->count(3)->create(['subject_id' => $subject->id]);

        $response = $this->getJson("/api/courses/subject/{$subject->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['courses' => [['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]]);
    }

    public function testStart()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $course = Course::factory()->create(['teacher_id' => $teacher->id, 'is_active' => false]);

        $lobby = Lobby::factory()->create();

        $response = $this->putJson("/api/courses/{$course->id}/start");

        $response->assertStatus(200)
            ->assertJsonStructure([            'course' => ['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]);
    }
    
    public function testEnd()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $course = Course::factory()->create(['teacher_id' => $teacher->id, 'is_active' => true]);

        $response = $this->putJson("/api/courses/{$course->id}/end");

        $response->assertStatus(200)
            ->assertJsonStructure(['course' => ['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]);
    }

    public function testJoinCourse()
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $this->actingAs($student, 'sanctum');

        $course = Course::factory()->create();

        $response = $this->postJson('/api/join-course', ['course_id' => $course->id]);

        $response->assertStatus(201)
            ->assertJsonStructure(['participant' => ['id', 'user_id', 'course_id', 'is_currently_present']]);
    }

    public function testGetNextCourse()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->actingAs($teacher, 'sanctum');

        $course = Course::factory()->create(['teacher_id' => $teacher->id, 'start_date' => now()->addHour()]);

        $response = $this->getJson('/api/next-course');

        $response->assertStatus(200)
            ->assertJsonStructure(['course' => ['id', 'subject_id', 'teacher_id', 'start_date', 'end_date', 'is_active', 'lobby_id']]);
    }
}