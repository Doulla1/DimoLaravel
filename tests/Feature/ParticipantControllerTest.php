<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Course;

class ParticipantControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAll()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        Participant::factory()->count(3)->create();
        $response = $this->getJson('/api/admin/participants');

        $response->assertStatus(200);
    }

    public function testDelete()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $participant = Participant::factory()->create();

        $response = $this->deleteJson("/api/admin/participants/{$participant->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Participant deleted']);
    }
}