<?php

namespace Tests\Feature;

use App\Enum\Language;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;


class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $path;

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
        /** @var Document $document */
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


        $validPdfContent = "%PDF-1.1
%¥±ë

1 0 obj
  << /Type /Catalog
     /Pages 2 0 R
  >>
endobj

2 0 obj
  << /Type /Pages
     /Kids [3 0 R]
     /Count 1
     /MediaBox [0 0 300 144]
  >>
endobj

3 0 obj
  <<  /Type /Page
      /Parent 2 0 R
      /Resources
       << /Font
           << /F1
               << /Type /Font
                  /Subtype /Type1
                  /BaseFont /Times-Roman
               >>
           >>
       >>
      /Contents 4 0 R
  >>
endobj

4 0 obj
  << /Length 55 >>
stream
  BT
    /F1 18 Tf
    0 0 Td
    (Hello World) Tj
  ET
endstream
endobj

xref
0 5
0000000000 65535 f
0000000018 00000 n
0000000077 00000 n
0000000178 00000 n
0000000457 00000 n
trailer
  <<  /Root 1 0 R
      /Size 5
  >>
startxref
565
%%EOF";

// Simulate a valid PDF file
        $data = [
            'title' => 'Test Document',
            'author' => 'Test Author',
            'pages' => 100,
            'language' => $language,
            'visibility' => $visibility,
            'file' => UploadedFile::fake()->createWithContent('document.pdf', $validPdfContent),
            'tags' => ['Tag1', 'Tag2']
        ];

        $response = $this->postJson('/api/documents', $data);

        $response->assertStatus(200)->assertJsonFragment(['title' => 'Test Document']);
    }

    public function test_update_a_document()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);
        $data = $document->toArray();
        $data['title'] = "Updated Title";

        $response = $this->postJson("/api/documents/$document->id", $data);

        $response->assertStatus(200)->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_delete_a_document()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/documents/$document->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    public function test_show_returns_a_document()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/documents/$document->id");

        $response->assertStatus(200)->assertJsonFragment(['id' => $document->id]);
    }

    public function test_show_private_document_while_having_access()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id, 'visibility' => 'private']);

        $response = $this->getJson("/api/documents/$document->id");

        $response->assertStatus(200)->assertJsonFragment(['id' => $document->id]);
    }

    public function test_show_private_document_with_no_access_returns_404()
    {
        $user = User::factory()->create();

        $document = Document::factory()->create(['user_id' => $user->id, 'visibility' => 'private']);

        $response = $this->getJson("/api/documents/$document->id");

        $response->assertForbidden();
    }

    public function test_show_by_token_returns_a_document()
    {
        $document = Document::factory()->create(['token' => Str::uuid()->toString(), 'visibility' => 'link_shared']);

        $response = $this->getJson("/api/documents/token/$document->token");

        $response->assertStatus(200)->assertJsonFragment(['token' => $document->token]);
    }

    public function test_download_document_by_id()
    {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'path' => $this->path
        ]);

        $response = $this->get("/api/documents/download/$document->id");

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    public function test_download_document_by_token()
    {
        $document = Document::factory()->create(['token' => 'test-token', 'visibility' => 'link_shared', 'path' => $this->path]);

        $response = $this->get("/api/documents/download-by-token/$document->token");

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }
}
