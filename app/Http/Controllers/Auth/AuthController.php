<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $token = $user->createToken(Str::uuid()->toString())
            ->plainTextToken;

        return response()->json(compact('user', 'token'));
    }

    public function login(LoginRequest $request): JsonResponse|Response
    {
        $credentials = $request->validated();
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $credentials['email'])
                ->first();
            $token = $user->createToken(Str::uuid()->toString())
                ->plainTextToken;
            return response()->json(compact('user', 'token'));
        }

        return response()->noContent(401);
    }
}
