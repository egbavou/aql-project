<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\Response;

class PasswordResetController extends Controller
{
    public function __invoke(PasswordResetRequest $request, PasswordResetService $passwordResetService): Response
    {
        $passwordResetService->resetPassword($request);
        return response()->noContent();
    }
}
