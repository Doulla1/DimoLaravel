<?php

namespace Tests\Feature;

use App\Models\Skin2;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Skin2ControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and assign a role for the tests
        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        // Create a Skin2 entry for the user
        $this->skin = Skin2::factory()->create([
            'user_id' => $this->user->id,
            'hair_version' => 1,
            'hair_color' => 'black',
            'upper_body_color' => 'red',
            'lower_body_color' => 'blue',
            'skin_color' => 'fair'
        ]);
    }

    public function test_get_skin_by_user()
    {
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->getJson('api/skin');

        $response->assertStatus(200)
                 ->assertJson([
                     'skin' => [
                         'hair_version' => $this->skin->hair_version,
                         'hair_color' => $this->skin->hair_color,
                         'upper_body_color' => $this->skin->upper_body_color,
                         'lower_body_color' => $this->skin->lower_body_color,
                         'skin_color' => $this->skin->skin_color
                     ]
                 ]);
    }

    public function test_update_skin_by_user()
    {
        Sanctum::actingAs($this->user, ['*']);

        $newSkinData = [
            'hair_version' => 2,
            'hair_color' => 'brown',
            'upper_body_color' => 'green',
            'lower_body_color' => 'yellow',
            'skin_color' => 'tan'
        ];

        $response = $this->putJson('api/skin', $newSkinData);

        $response->assertStatus(200)
                 ->assertJson([
                     'skin' => $newSkinData
                 ]);

        $this->assertDatabaseHas('skin2s', [
            'user_id' => $this->user->id,
            'hair_version' => 2,
            'hair_color' => 'brown',
            'upper_body_color' => 'green',
            'lower_body_color' => 'yellow',
            'skin_color' => 'tan'
        ]);
    }
}
