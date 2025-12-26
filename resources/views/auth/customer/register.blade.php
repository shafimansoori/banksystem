<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ env('APP_NAME') }} - Register</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="{{ url('') }}/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/css/login_util.css">
	<link rel="stylesheet" type="text/css" href="{{ url('') }}/css/login_main.css">
<!--===============================================================================================-->
<style>
    .wrap-login100 {
        padding: 40px 55px 30px 55px;
    }
    .login100-form {
        width: 100%;
    }
    .input-row {
        display: flex;
        gap: 15px;
    }
    .input-row .wrap-input100 {
        flex: 1;
    }
    @media (max-width: 576px) {
        .input-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>
</head>
<body style="background-color: #008080">


	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100" style="max-width: 600px;">

                <form class="login100-form validate-form" method="POST" action="{{ route('register') }}">
                    @csrf
					<span class="login100-form-title">
						Create Account
                    </span>

                    @if ($errors->any())
                        <div class="text-danger text-center" style="padding:10px;">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="text-danger text-center" style="padding:10px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- First Name & Last Name -->
                    <div class="input-row">
                        <div class="wrap-input100 validate-input" data-validate="First name is required">
                            <input class="input100" type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="Last name is required">
                            <input class="input100" type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Email -->
					<div class="wrap-input100 validate-input" data-validate="Valid email is required">
						<input class="input100" type="email" name="email" placeholder="Email" value="{{ old('email') }}">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

                    <!-- Username & Phone -->
                    <div class="input-row">
                        <div class="wrap-input100 validate-input" data-validate="Username is required">
                            <input class="input100" type="text" name="username" placeholder="Username" value="{{ old('username') }}">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-at" aria-hidden="true"></i>
                            </span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="Phone number is required">
                            <input class="input100" type="text" name="phone_number" placeholder="Phone Number" value="{{ old('phone_number') }}">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="wrap-input100 validate-input" data-validate="Country is required">
                        <select class="input100" name="country_id" style="padding-left: 54px; border: none; outline: none;">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                        </span>
                    </div>

                    <!-- Address -->
                    <div class="wrap-input100">
                        <input class="input100" type="text" name="address" placeholder="Address (Optional)" value="{{ old('address') }}">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                        </span>
                    </div>

                    <!-- Password -->
					<div class="wrap-input100 validate-input" data-validate="Password is required (min 6 characters)">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

                    <!-- Confirm Password -->
					<div class="wrap-input100 validate-input" data-validate="Please confirm your password">
						<input class="input100" type="password" name="password_confirmation" placeholder="Confirm Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Register
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Already have an account?
						</span>
						<a class="txt2" href="{{ route('login') }}">
							Login
						</a>
					</div>

				</form>
			</div>
		</div>
	</div>




<!--===============================================================================================-->
	<script src="{{ url('') }}/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ url('') }}/vendor/bootstrap/js/popper.js"></script>
	<script src="{{ url('') }}/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ url('') }}/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ url('') }}/vendor/tilt/tilt.jquery.min.js"></script>
<!--===============================================================================================-->
	<script src="{{ url('') }}/js/login_main.js"></script>

</body>
</html>
