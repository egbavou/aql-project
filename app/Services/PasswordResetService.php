<?php

namespace App\Services;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class PasswordResetService
{
    public function forgotPassword(ForgotPasswordRequest $request): string
    {
        do {
            $token = $this->generateCode();
        } while (PasswordResetToken::where('token', $token)->exists());

        PasswordResetToken::where('email', $request->input('email'))
            ->delete();
        PasswordResetToken::create([
            'token' => $token,
            'email' => $request->input('email')
        ]);

        return $token;
    }

    public function resetPassword(PasswordResetRequest $request): void
    {
        $passwordResetToken = PasswordResetToken::where('token', $request->input('token'))
            ->first();

        PasswordResetToken::where('token', $request->input('token'))
            ->delete();
        if ($passwordResetToken?->created_at->gte(now()->subMinutes(config('app.password_reset_token_lifetime')))) {
            $password = Hash::make($request->input('password'));
            User::where('email', $passwordResetToken->email)
                ->update(['password' => $password]);
        } else {
            abort(400);
        }
    }

    private function generateCode(): string
    {
        return strval(mt_rand(100000, 999999));
    }
}
