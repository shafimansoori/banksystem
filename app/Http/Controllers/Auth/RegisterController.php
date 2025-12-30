<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        $countries = Country::all();
        return view('auth.customer.register', compact('countries'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'country_id' => 'required|exists:countries,id',
            'address' => 'nullable|string|max:500',
        ], [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'username.required' => 'Username is required',
            'username.unique' => 'This username is already taken',
            'username.alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores',
            'phone_number.required' => 'Phone number is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'country_id.required' => 'Please select a country',
        ]);

        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name ?? '',
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'country_id' => $request->country_id,
            'address' => $request->address ?? '',
            'description' => 'Customer Account',
            'picture' => 'https://cdn1.iconfinder.com/data/icons/bokbokstars-121-classic-stock-icons-1/512/person-man.png',
            'two_factor_enabled' => true,
        ]);

        // Assign Customer role
        $customerRole = Role::where('name', 'Customer')->first();
        if ($customerRole) {
            $user->assignRole($customerRole);
        }

        // Generate and send 2FA code for new user
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = \Carbon\Carbon::now()->addMinutes(5);
        $user->save();

        // Send 2FA email
        try {
            \Mail::raw("Welcome to Bank System!\n\nYour 2FA verification code is: {$code}\n\nThis code will expire in 5 minutes.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Bank System - Email Verification Code');
            });
        } catch (\Exception $e) {
            \Log::error('2FA Email Error for new user: ' . $e->getMessage());
        }

        // Store user email in session for 2FA page
        session(['2fa:user:email' => $user->email]);
        session()->save();

        // Redirect to 2FA verification page
        return redirect()->route('2fa.verify')->with('success', 'Account created successfully! Please verify your email with the 2FA code. Code: ' . (config('app.debug') ? $code : '******'));
    }
}
