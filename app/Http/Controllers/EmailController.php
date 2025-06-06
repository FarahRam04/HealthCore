<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class
EmailController extends Controller
{



    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email']);

        $existing = EmailVerification::where('email', $request->email)->first();

        if ($existing && $existing->updated_at > now()->subSeconds(30)) {
            return response()->json([
                'message' => 'Please wait before requesting a new code.',
            ], 429); // 429 Too Many Requests
        }

        $code = rand(100000, 999999);

        EmailVerification::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(5),
            ]
        );

        Mail::raw("Your verification code is: $code", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Email Verification Code');
        });

        return response()->json(['message' => 'Verification code sent.']);
    }
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|digits:6',
        ]);

        $verification = EmailVerification::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'Invalid or expired code.'], 401);
        }


        // حذف الكود من جدول التحقق
        $verification->delete();
        return response()->json(['message' => 'Email verified successfully.']);}
}
