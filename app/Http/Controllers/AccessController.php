<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentShareRequest;
use App\Mail\DocumentAccessSharedMail;
use App\Services\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class AccessController extends Controller
{
    public function __construct(private readonly AccessService $accessService)
    {
    }

    public function store(int $documentId, DocumentShareRequest $request): Response
    {
        $document = $this->accessService->createAccess($documentId, $request);
        Mail::to($request->input('email'))
            ->send(new DocumentAccessSharedMail($document));
        return response()->noContent();
    }

    public function linkStore(int $documentId, Request $request): JsonResponse
    {
        return response()->json($this->accessService->createLinkAccess($documentId, $request));
    }

    public function destroy(int $accessId, Request $request): Response
    {
        $this->accessService->removeAccess($accessId, $request);
        return response()->noContent();
    }
}
