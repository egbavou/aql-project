<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enum\Language;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;
    private $user;
    private $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->path = 'documents/test.pdf';

        $file = UploadedFile::fake()->create('test.pdf', 100);
        Storage::put($this->path, $file->getContent());

        $this->assertInstanceOf(User::class, $this->user); // Vérification
        $this->actingAs($this->user);
        
    }

    public function test_index_returns_documents()
    { 
        Document::factory()->count(3)->create(['visibility' => 'public']);
        
        $response = $this->getJson('/api/documents');
        
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_index_created_returns_user_documents()
    {
        Document::factory()->count(2)->create(['user_id' => $this->user->id]);
        Document::factory()->create();

        $response = $this->getJson('/api/documents/created');
        $documents = Document::where('user_id', $this->user->id)->get();
        $response->assertStatus(200)->assertJsonCount($documents->count(), 'data');
    }

    public function test_index_shared_with_returns_shared_documents()
    {
        $document = Document::factory()->create();
        $document->accesses()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/documents/shared');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_store_creates_a_document()
    {
        $languages = Language::values();
        $language = $languages[array_rand($languages)];

        $visibilities = ['public', 'private'];
        $visibility = $visibilities[array_rand($visibilities)];

        $data = [
            'title' => 'Test Document',
            'author' => 'Test Author',
            'pages' => 100,
            'language' => $language,
            'visibility' => $visibility,
            'file' => UploadedFile::fake()->create('document.pdf', 100),
            'tags' => ['Tag1', 'Tag2']
        ];

        $response = $this->postJson('/api/documents', $data);

        $response->assertStatus(200)->assertJsonFragment(['title' => 'Test Document']);
    }

    public function test_update_modifies_a_document()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);
        $data = $document->toArray();
        $data['title'] = "Updated Title";

        $response = $this->postJson("/api/documents/{$document->id}", $data);

        $response->assertStatus(200)->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_delete_removes_a_document()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    public function test_show_returns_a_document()
    {
        $document = Document::factory()->create(['user_id' => $this->user?->id]);

        $response = $this->getJson("/api/documents/{$document->id}");

        $response->assertStatus(200)->assertJsonFragment(['id' => $document->id]);
    }

    public function test_show_by_token_returns_a_document()
    {
        $document = Document::factory()->create(['token' => 'test-token', 'visibility' => 'link_shared']);

        $response = $this->getJson("/api/documents/token/{$document->token}");

        $response->assertStatus(200)->assertJsonFragment(['token' => 'test-token']);
    }
    
    public function test_download_document_by_id()
    {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'path' =>  $this->path
        ]);

        $response = $this->get("/api/documents/download/{$document->id}");

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    public function test_download_document_by_token()
    {
        $document = Document::factory()->create(['token' => 'test-token', 'visibility' => 'link_shared', 'path' =>  $this->path]);

        $response = $this->get("/api/documents/download-by-token/{$document->token}");

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }
}
