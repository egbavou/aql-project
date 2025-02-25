<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentCreateRequest;
use App\Http\Requests\DocumentFilterRequest;
use App\Http\Requests\DocumentUpdateRequest;
use App\Services\DocumentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentsService $documentsService)
    {
    }

    public function index(DocumentFilterRequest $request): JsonResponse
    {
        return response()->json($this->documentsService->list($request));
    }

    public function indexCreated(DocumentFilterRequest $request): JsonResponse
    {
        return response()->json($this->documentsService->listCreated($request));
    }

    public function indexSharedWith(DocumentFilterRequest $request): JsonResponse
    {
        return response()->json($this->documentsService->listSharedWith($request));
    }

    public function show(int $id, Request $request): JsonResponse
    {
        return response()->json($this->documentsService->show($id, $request));
    }

    public function showByToken(string $token): JsonResponse
    {
        return response()->json($this->documentsService->showByToken($token));
    }

    public function download(int $id): BinaryFileResponse
    {
        return $this->documentsService->download($id);
    }

    public function downloadByToken(string $token): BinaryFileResponse
    {
        return $this->documentsService->downloadByToken($token);
    }

    /**
     * @throws Throwable
     */
    public function store(DocumentCreateRequest $request): JsonResponse
    {
        return response()->json($this->documentsService->create($request));
    }

    /**
     * @throws Throwable
     */
    public function update(int $id, DocumentUpdateRequest $request): JsonResponse
    {
        return response()->json($this->documentsService->update($id, $request));
    }

    public function delete(int $id, Request $request): Response
    {
        $this->documentsService->delete($id, $request);
        return response()->noContent();
    }
}
