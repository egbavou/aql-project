<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('api/auth/logout');
        $response->assertNoContent();
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
