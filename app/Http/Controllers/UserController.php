<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserUpdateRequest;
use Throwable;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json($this->userService->show($request));
    }

    /**
     * @throws Throwable
     */
    public function update(int $id, UserUpdateRequest $request): JsonResponse
    {
        return response()->json($this->userService->update($id, $request));
    }

    public function delete(int $id, Request $request): Response
    {
        $this->userService->delete($id, $request);
        return response()->noContent();
    }
}
