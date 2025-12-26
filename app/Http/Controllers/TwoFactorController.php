<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class TwoFactorController extends Controller
{
    /**
     * Generate and send 2FA code
     */
    public function sendCode(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save code and expiration (5 minutes)
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        // Send email
        try {
            Mail::raw("Your 2FA verification code is: {$code}\n\nThis code will expire in 5 minutes.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Bank System - Two-Factor Authentication Code');
            });

            return response()->json([
                'status' => 'success',
                'message' => '2FA code sent to your email'
            ]);
        } catch (\Exception $e) {
            \Log::error('2FA Email Error: ' . $e->getMessage());

            // For development, return the code
            if (config('app.debug')) {
                return response()->json([
                    'status' => 'success',
                    'message' => '2FA code generated (email not configured)',
                    'code' => $code // Only in debug mode
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send code. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify 2FA code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Check if code exists
        if (!$user->two_factor_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'No verification code found. Please request a new one.'
            ], 400);
        }

        // Check if code expired
        if (Carbon::now()->greaterThan($user->two_factor_expires_at)) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            return response()->json([
                'status' => 'error',
                'message' => 'Verification code expired. Please request a new one.'
            ], 400);
        }

        // Verify code
        if ($user->two_factor_code !== $request->code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification code'
            ], 401);
        }

        // Clear the code
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // Log the user in
        \Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => '2FA verification successful',
            'redirect' => route('dashboard')
        ]);
    }

    /**
     * Toggle 2FA for user
     */
    public function toggle(Request $request)
    {
        $user = auth()->user();
        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->save();

        return back()->with('success', '2FA has been ' . ($user->two_factor_enabled ? 'enabled' : 'disabled'));
    }

    /**
     * Show 2FA verification page
     */
    public function showVerifyForm()
    {
        if (!Session::has('2fa:user:email')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        return view('auth.two-factor');
    }
}
