<?php

namespace Tests\Feature;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_with_valid_code()
    {
        $user = User::factory()->create();
        PasswordResetToken::create([
            'email' => $user->email,
            'token' => '123456'
        ]);

        $response = $this->post('api/auth/password-reset', [
            'token'    => '123456',
            'password' => 'password'
        ]);
        $response->assertNoContent();
    }

    public function test_reset_password_with_invalid_code()
    {
        $user = User::factory()->create();
        PasswordResetToken::create([
            'email' => $user->email,
            'token' => '123456'
        ]);

        $response = $this->post('api/auth/password-reset', [
            'token'    => '000000',
            'password' => 'password'
        ]);
        $response->assertBadRequest();
    }

    public function test_reset_password_with_expired_code()
    {
        $user = User::factory()->create();
        PasswordResetToken::insert([
            'email' => $user->email,
            'token' => '123456',
            'created_at' => now()->subYear()
        ]);

        $response = $this->post('api/auth/password-reset', [
            'token'    => '123456',
            'password' => 'password'
        ]);
        $response->assertBadRequest();
    }

    public function test_reset_password_with_non_existing_code()
    {
        $response = $this->post('api/auth/password-reset', [
            'token'    => '123456',
            'password' => 'password'
        ]);
        $response->assertBadRequest();
    }
}
