<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Cache\RateLimiter;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $limiter = app(RateLimiter::class);
        $key = 'password-reset|' . $request->ip();

        $attempts = $limiter->attempts($key);
        $maxAttempts = $limiter->limiter($key)->limit();
        /*
        dd([
            'attempts' => $attempts,
            'max_attempts' => $maxAttempts,
            'key' => $key
        ]);*/

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent.']);
        }

        return response()->json([
            'message' => 'Gagal mengirim link reset password.',
            'errors' => [__($status)]
        ], 429);
    }


    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
    }
}
