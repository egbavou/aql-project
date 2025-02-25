<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function __construct(private readonly TagService $tagService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->tagService->list());
    }
}
