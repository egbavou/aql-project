<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Access;
use App\Models\Document;
use App\Enum\DocumentVisibility;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessControllerTest extends TestCase
{
    use RefreshDatabase;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_shared_document_by_mail(): void
    {        
        $document = Document::factory()->create(['user_id' => $this->user->id]);
        $sharedUser = User::factory()->create();
        
        $response = $this->postJson("api/documents/$document->id/private-share", [
            'email' => $sharedUser->email
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('accesses', [
            'document_id' => $document->id,
            'user_id' => $sharedUser->id
        ]);
    }

    public function test_link_store(): void
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->postJson("api/documents/$document->id/link-share");
        
        $response->assertOk()
            ->assertJsonStructure(['id', 'token', 'visibility']);
        
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'visibility' => DocumentVisibility::linkSharedFile
        ]);
    }

    public function test_destroy(): void
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);
        $sharedUser = User::factory()->create();
        $access = Access::factory()->create([
            'document_id' => $document->id,
            'user_id' => $sharedUser->id
        ]);
        
        $response = $this->deleteJson("api/accesses/$access->id");
        
        $response->assertNoContent();
        $this->assertDatabaseMissing('accesses', ['id' => $access->id]);
    }
}
