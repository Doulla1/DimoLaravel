<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur et le rôle pour les tests
        $this->user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);
        $this->user->assignRole('admin');
    }

    public function test_get_all_users()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson('api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    '*' => ['id', 'firstname', 'lastname', 'email', 'roles']
                ]
            ]);
    }

    public function test_get_my_role()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson('api/my-role');

        $response->assertStatus(200)
            ->assertJsonFragment(['admin']);
    }

    public function test_get_user_by_id()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson("api/admin/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'firstname', 'lastname', 'email', 'roles']
            ]);
    }

    public function test_get_user_by_email()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson("api/admin/users/email/{$this->user->email}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'firstname', 'lastname', 'email', 'roles']
            ]);
    }

    public function test_get_connected_user()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson('api/fetchUser');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'firstname', 'lastname', 'email', 'roles']
            ]);
    }

    public function test_delete_user()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $userToDelete = User::factory()->create();
        $response = $this->deleteJson("api/admin/users/{$userToDelete->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted']);
    }

    public function test_update_user()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $updatedData = [
            'firstname' => 'Updated',
            'lastname' => 'Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword123'
        ];

        $response = $this->putJson("api/admin/users/{$this->user->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'firstname' => 'Updated',
                    'lastname' => 'Name',
                    'email' => 'updated@example.com'
                ]
            ]);
    }

    public function test_update_connected_user()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $updatedData = [
            'firstname' => 'Updated',
            'lastname' => 'Name',
            'email' => 'updated@example.com'
        ];

        $response = $this->putJson('api/updateUser', $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'firstname' => 'Updated',
                    'lastname' => 'Name',
                    'email' => 'updated@example.com'
                ]
            ]);
    }

    public function test_update_connected_user_password()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $passwordData = [
            'currentPassword' => 'password123',
            'newPassword' => 'newpassword123'
        ];

        $response = $this->putJson('api/updateUserPassword', $passwordData);

        $response->assertStatus(200);
    }

    public function test_get_roles()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson('api/admin/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'roles' => [
                    '*' => ['id', 'name']
                ]
            ]);
    }

    public function test_assign_role()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $roleData = [
            'user_id' => $this->user->id,
            'role_id' => 1 // Assurez-vous d'avoir un rôle avec l'id 1 dans votre base de données
        ];

        $response = $this->postJson('api/admin/assign-role', $roleData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'firstname', 'lastname', 'email', 'roles']
            ]);
    }

    public function test_get_pending_users()
    {
        Sanctum::actingAs($this->user, ['*']);
        
        $response = $this->getJson('api/admin/users/pending');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    '*' => ['id', 'firstname', 'lastname', 'email', 'roles']
                ]
            ]);
    }
}
