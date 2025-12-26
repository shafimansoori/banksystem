<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle after authentication - check for 2FA
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if user has login permission
        if (!$user->can('login')) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        // Check if 2FA is enabled for this user
        if ($user->two_factor_enabled) {
            // Logout temporarily
            Auth::logout();

            // Generate and send 2FA code
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = Carbon::now()->addMinutes(5);
            $user->save();

            // Send email
            try {
                Mail::raw("Your 2FA verification code is: {$code}\n\nThis code will expire in 5 minutes.", function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Bank System - Two-Factor Authentication Code');
                });
            } catch (\Exception $e) {
                \Log::error('2FA Email Error: ' . $e->getMessage());
            }

            // Store user email in session for 2FA page
            session(['2fa:user:email' => $user->email]);

            // Redirect to 2FA verification page
            $message = '2FA code sent to your email.';
            if (config('app.debug')) {
                $message .= ' Code: ' . $code;
            }

            return redirect()->route('2fa.verify')->with('info', $message);
        }

        // No 2FA - proceed to dashboard
        return redirect()->intended($this->redirectTo);
    }
}
