<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration(): void
    {
        $response = $this->post('/api/auth/register', [
            'name'     => 'John Doe',
            'email'    => 'contact@john.doe.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_user_login_with_correct_credentials(): void
    {
        $user = User::factory()->create();
        $response = $this->post('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_user_login_with_invalid_password(): void
    {
        $user = User::factory()->create();
        $response = $this->post('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'this_is_an_invalid_password'
        ]);
        $response->assertStatus(401);
    }

    public function test_user_login_with_non_existing_account(): void
    {
        $response = $this->post('/api/auth/login', [
            'email'    => 'contact@idonoitexist.com',
            'password' => 'this_is_an_invalid_password'
        ]);
        $response->assertStatus(401);
    }
}
