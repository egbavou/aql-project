<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Mail\ForgotPasswordMail;
use App\Services\PasswordResetService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request, PasswordResetService $passwordResetService): Response
    {
        $token = $passwordResetService->forgotPassword($request);
        Mail::to($request->input('email'))
            ->send(new ForgotPasswordMail($token));
        return response()->noContent();
    }
}
