<?php

namespace Tests\Feature;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    public function test_code_generated_and_mail_sent_successfully(): void
    {
        $user = User::factory()->create();
        Mail::fake();
        $response = $this->post('/api/auth/forgot-password', ['email' => $user->email]);
        $response->assertNoContent();
        Mail::assertSent(ForgotPasswordMail::class);
    }
}
