<?php

namespace App\Services;

use App\Enum\DocumentVisibility;
use App\Http\Requests\DocumentCreateRequest;
use App\Http\Requests\DocumentFilterRequest;
use App\Http\Requests\DocumentUpdateRequest;
use App\Models\Access;
use App\Models\Document;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

final class DocumentsService
{
    private const PRIVATE_PATH = 'app/private/';

    /**
     * @param DocumentFilterRequest $request
     * @return LengthAwarePaginator<Document>
     */
    public function list(DocumentFilterRequest $request): LengthAwarePaginator
    {
        $query = Document::with('tags')
            ->where('visibility', DocumentVisibility::publicFile);
        return $this->finalizeDocumentFiltering($query, $request);
    }

    /**
     * @param DocumentFilterRequest $request
     * @return LengthAwarePaginator<Document>
     */
    public function listCreated(DocumentFilterRequest $request): LengthAwarePaginator
    {
        $query = Document::with('tags', 'accesses', 'accesses.user')
            ->where('user_id', $request->user()->id);
        return $this->finalizeDocumentFiltering($query, $request);
    }

    /**
     * @param DocumentFilterRequest $request
     * @return LengthAwarePaginator<Document>
     */
    public function listSharedWith(DocumentFilterRequest $request): LengthAwarePaginator
    {
        $query = Document::select('documents.*')
            ->join('accesses', 'documents.id', '=', 'accesses.document_id')
            ->where('accesses.user_id', $request->user()->id)
            ->with('tags');

        return $this->finalizeDocumentFiltering($query, $request);
    }

    public function show(int $id, Request $request): Document
    {
        $document = Document::with('tags')
            ->findOrFail($id);
        abort_if(!$this->hasAccess($request->user(), $document), 403);
        return $document;
    }

    public function showByToken(string $token): Document
    {
        return Document::with('tags')
            ->where('token', $token)
            ->where('visibility', DocumentVisibility::linkSharedFile)
            ->firstOrFail();
    }

    public function download(int $id): BinaryFileResponse
    {
        $document = Document::findOrFail($id);
        /** @var User $currentUser */
        $currentUser = Auth::guard('sanctum')->user();
        abort_if(!$this->hasAccess($currentUser, $document), 403);
        $document->update(['downloads' => $document->downloads + 1]);
        return response()->download(storage_path(self::PRIVATE_PATH . $document->path));
    }

    public function downloadByToken(string $token): BinaryFileResponse
    {
        $document = Document::where('token', $token)
            ->where('visibility', DocumentVisibility::linkSharedFile)
            ->firstOrFail();
        $document->update(['downloads' => $document->downloads + 1]);
        return response()->download(storage_path(self::PRIVATE_PATH . $document->path), $document->title);
    }

    /**
     * @throws Throwable
     */
    public function create(DocumentCreateRequest $request): Document
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');
        $filePath = $file->store(Document::FOLDER);
        $data = $request->safe()->except(['file', 'tags']);
        $parser = new Parser();
        $pdf = $parser->parseFile(storage_path(self::PRIVATE_PATH . $filePath));
        $data['path'] = $filePath;
        $data['pages'] = count($pdf->getPages());
        $data['size'] = $file->getSize();
        $data['user_id'] = $request->user()->id;
        $tags = $request->input('tags', []);

        return DB::transaction(function () use ($data, $tags) {
            $tagIds = array_map(fn($tag) => Tag::firstOrCreate(['name' => $tag])->id, $tags);
            $document = Document::create($data);
            $document->tags()->sync($tagIds);
            $document->load('tags');
            return $document;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(int $id, DocumentUpdateRequest $request): Document
    {
        $document = Document::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($request->hasFile('file')) {
            File::delete(storage_path(self::PRIVATE_PATH . $document->path));
            /** @var UploadedFile $file */
            $file = $request->file('file');
            $fileSavePath = $file->store(Document::FOLDER);

            if ($fileSavePath === false) {
                Log::error("Failed to store document file {$file->getClientOriginalName()}");
                abort(500);
            }

            $filePath = strval($fileSavePath);
            $fileSize = $file->getSize();
            $parser = new Parser();
            $pdf = $parser->parseFile(storage_path(self::PRIVATE_PATH . $filePath));
            $pages = count($pdf->getPages());
        } else {
            $pages = $document->pages;
            $filePath = $document->path;
            $fileSize = $document->size;
        }

        $tags = $request->input('tags', []);
        $data = $request->safe()->except(['file', 'tags']);
        return DB::transaction(function () use ($pages, $document, $data, $filePath, $fileSize, $tags) {
            $tagIds = array_map(fn($tag) => Tag::firstOrCreate(['name' => $tag])->id, $tags);
            $document->fill($data);
            $document->path = $filePath;
            $document->size = $fileSize;
            $document->pages = $pages;
            $document->save();
            $document->tags()->sync($tagIds);
            $document->load('tags');
            return $document;
        });
    }

    public function delete(int $id, Request $request): void
    {
        $document = Document::where('user_id', $request->user()->id)
            ->findOrFail($id);
        File::delete(storage_path(self::PRIVATE_PATH . $document->path));
        $document->delete();
    }

    /**
     * @param Builder<Document> $builder
     * @param DocumentFilterRequest $request
     * @return LengthAwarePaginator<Document>
     */
    private function finalizeDocumentFiltering(Builder $builder, DocumentFilterRequest $request): LengthAwarePaginator
    {
        $query = $builder;
        $request->whenFilled('title', function ($value) use (&$query) {
            $query = $query->where('title', 'like', '%' . $value . '%');
        })->whenFilled('author', function ($value) use (&$query) {
            $query = $query->where('author', 'like', '%' . $value . '%');
        })->whenFilled('tag', function ($value) use (&$query) {
            $query = $query->whereHas('tags', function ($q) use ($value) {
                $q->where('tags.id', $value);
            });
        })->whenFilled('language', function ($value) use (&$query) {
            $query = $query->where('language', $value);
        });

        return $query
            ->paginate(
                perPage: $request->input('per_page', 10),
                page: $request->input('page', 1)
            );
    }

    private function hasAccess(?User $user, Document $document): bool
    {
        if ($document->visibility === DocumentVisibility::publicFile
            || $document->visibility === DocumentVisibility::linkSharedFile) {
            return true;
        }

        if ($user?->id === $document->user_id) {
            return true;
        }

        return Access::where('user_id', $user->id)
            ->where('document_id', $document->id)
            ->exists();
    }
}
