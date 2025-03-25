<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_current_user(): void
    {
        $response = $this->getJson("api/users/current-user");
        
        $response->assertStatus(200);
    }

    public function test_update_user(): void
    {
        $payload = $this->user->toArray();
        $payload["email"] =  'updated@example.com';
        
        $response = $this->postJson("api/users/{$this->user->id}", $payload);
        
        $response->assertStatus(200)
            ->assertJson(['email' => 'updated@example.com']);
    }

    public function test_delete(): void
    {        
        $response = $this->deleteJson("api/users/{$this->user->id}");
        
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }
}
