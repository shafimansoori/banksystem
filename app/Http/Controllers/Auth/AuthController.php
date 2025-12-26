<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\CustomerResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    function login(Request $request){

        //Check using Email Address
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $loginValue = $request->input('email');
        //Get Login Type
        $login_type = $this->getLoginType( $loginValue);

        //Change request type based on user input
        $request->merge([
            $login_type => $loginValue
        ]);


        $credentials = $request->only($login_type, 'password');

        if (Auth::attempt($credentials, $request->remember)) {

            $user = Auth::user();

            // Check if 2FA is enabled for this user
            if ($user->two_factor_enabled) {
                // Logout temporarily
                Auth::logout();

                // Generate and send 2FA code
                $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->two_factor_code = $code;
                $user->two_factor_expires_at = \Carbon\Carbon::now()->addMinutes(5);
                $user->save();

                // Send email (or show code in debug mode)
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
                return redirect()->route('2fa.verify')->with('info', '2FA code sent to your email. Code: ' . (config('app.debug') ? $code : '******'));
            }

            // Log them in (no 2FA)
            return redirect('/dashboard');

        }

        return back()->withInput()->with('error', 'We cant find an account with this credentials. Please make sure you entered the right information');

    }

    public function getLoginType($loginValue) {

        if(filter_var($loginValue, FILTER_VALIDATE_EMAIL ) ){
            return "email";
        }

        return "username";

    }

    function logout(Request $request){

        Auth::logout();
        return redirect('login');
    }


    function reset_account(Request $request){

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where("email",$request->email)
        ->orWhere("username",$request->email)->first();

        if(!empty($user)){

            $reset = new PasswordReset();
            $reset->email = $request->email;
            $reset->token = Str::random(40);
            $reset->save();

            $link = route("password_setup")."/".$reset->token;

            //Sending Reset Password mail to customer
            Mail::to($user->email)->queue(new CustomerResetPasswordMail($user,$link));

            return back()->withInput()->with('success', 'A password resent link has been sent to your your email');

        }else{
            return back()->withInput()->with('error', 'We cant find an account with this credentials. Please make sure you entered the right information');
        }


    }

    function start_account_password(Request $request, $code){

        $passwordReset = PasswordReset::where("token",$code)->first();
        if( !empty($passwordReset) ){
            return view('auth.customer.set_account_password')->with("code",$code);
        }

        return redirect()->route('reset_account')->with('error', '<b>Ooops!!</b> Invalid Credentials. Please make sure you entered the right information');
    }

    function set_account_password(Request $request){

        $passwordReset = PasswordReset::where("token",$request->code)->first();

        if( !empty($passwordReset) ){

            $this->validate($request, [
                'password' => 'required|confirmed|min:6',
            ]);

            //SET THE NEW PASSWORD
            $new_password = Hash::make($request->password);
            $user = User::where("email",$passwordReset->email)->first();
            $user->password = $new_password;
            $user->save();
            return redirect()->route('reset_account')->with('success', '<b>Successful!!</b> Your Account Password Has Been Reset');
            //END PASSWORD SETTING

        }

        return redirect()->route('reset_account')->with('error', '<b>Ooops!!</b> Invalid Credentials. Please make sure you entered the right information');


    }


}
