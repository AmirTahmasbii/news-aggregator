<?php

namespace Tests\Feature\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Amir',
            'family' => 'Tahmasbi',
            'email' => 'amir@example.com',
            'password' => 'Amir1234!',
            'password_confirmation' => 'Amir1234!'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['token']
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'amir@example.com'
        ]);
    }

    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'amir@example.com',
            'password' => Hash::make('Amir1234!')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Amir1234!'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['token']
            ]);
    }

    public function test_user_login_fails_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'amir@example.com',
            'password' => Hash::make('Amir1234!')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'WrongPassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Email or Password is incorrect!'
            ]);
    }

    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'logout was success!'
            ]);
    }

    public function test_send_reset_link_email_successfully()
    {
        $user = User::factory()->create(['email' => 'amir@example.com']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'amir@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Reset link sent to your email.'
            ]);
    }
}
