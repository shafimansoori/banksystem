<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ env('APP_NAME') }} - Two-Factor Authentication</title>
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
	.code-input {
		font-size: 24px;
		letter-spacing: 15px;
		text-align: center;
		font-weight: bold;
	}
</style>
</head>
<body style="background-color: #008080">

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="https://cdn-icons-png.flaticon.com/512/6195/6195699.png" alt="2FA" style="max-width: 200px;">
				</div>

                <form class="login100-form validate-form" method="POST" action="{{ route('2fa.verify.post') }}" id="twoFactorForm">
                    @csrf
					<span class="login100-form-title">
						Two-Factor Authentication
                    </span>

                    @if ($errors->any())
                        <div class="alert alert-danger text-center" style="padding:10px;">
                            {{ implode('', $errors->all(':message')) }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger text-center" style="padding:10px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info text-center" style="padding:10px;">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="text-center mb-3" style="padding: 15px; color: #666;">
                        <i class="fa fa-shield" style="font-size: 48px; color: #008080; margin-bottom: 10px;"></i>
                        <p style="margin: 0;">A 6-digit verification code has been sent to your email address.</p>
                        <p style="margin: 5px 0 0 0; font-size: 12px;">The code will expire in 5 minutes.</p>
                    </div>

                    <input type="hidden" name="email" value="{{ session('2fa:user:email') }}">

                    <div class="wrap-input100 validate-input">
						<input class="input100 code-input" type="text" name="code" placeholder="000000" maxlength="6" required autofocus>
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-key" aria-hidden="true"></i>
						</span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit">
							Verify
						</button>
					</div>

                    <div class="text-center p-t-12">
						<a class="txt2" href="{{ route('login') }}">
							<i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
                            Back to Login
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
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="{{ url('') }}/js/login_main.js"></script>
    <script>
        // Auto-submit when 6 digits entered
        document.querySelector('input[name="code"]').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Auto-submit after small delay
                setTimeout(() => {
                    document.getElementById('twoFactorForm').submit();
                }, 300);
            }
        });

        // Only allow numbers
        document.querySelector('input[name="code"]').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    </script>

</body>
</html>
