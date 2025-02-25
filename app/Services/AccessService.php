<?php

namespace App\Services;

use App\Enum\DocumentVisibility;
use App\Http\Requests\DocumentShareRequest;
use App\Models\Access;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AccessService
{
    public function createAccess(int $documentId, DocumentShareRequest $request): Document
    {
        $document = Document::with('user')
            ->where('user_id', $request->user()->id)
            ->findOrFail($documentId);
        $document->update(['visibility' => DocumentVisibility::accountSharedFile]);
        $user = User::where('email', $request->input('email'))
            ->first();
        $exists = Access::where('document_id', $document->id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$exists) {
            Access::create([
                'document_id' => $document->id,
                'user_id'     => $user->id
            ]);
        }

        return $document;
    }

    public function createLinkAccess(int $documentId, Request $request): Document
    {
        $document = Document::with('tags')
            ->where('user_id', $request->user()->id)
            ->findOrFail($documentId);
        $document->token = Str::uuid()->toString();
        $document->visibility = DocumentVisibility::linkSharedFile;
        $document->save();
        return $document;
    }

    public function removeAccess(int $accessId, Request $request): void
    {
        $access = Access::with('document')
            ->findOrFail($accessId);
        abort_if($access->document->user_id !== $request->user()->id, 403);
        $access->delete();
    }
}
