<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Home</title>
    <link rel="icon" type="image/png" href="{{ url('') }}/images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="{{ url('') }}/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-section img {
            width: 45px;
            height: 45px;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 600;
            color: #212529;
            letter-spacing: -0.5px;
        }

        .login-btn {
            background: #212529;
            color: white;
            padding: 10px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border: 2px solid #212529;
            font-size: 15px;
        }

        .login-btn:hover {
            background: white;
            color: #212529;
            text-decoration: none;
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 60px;
        }

        .welcome-section h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #212529;
            letter-spacing: -1px;
        }

        .welcome-section p {
            font-size: 18px;
            color: #6c757d;
            font-weight: 400;
        }

        /* Announcements Section */
        .announcements-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 45px;
            border: 1px solid #e9ecef;
        }

        .section-title {
            font-size: 28px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .section-title i {
            color: #212529;
        }

        .announcement-card {
            background: #f8f9fa;
            border-left: 4px solid;
            padding: 24px;
            margin-bottom: 18px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .announcement-card:hover {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .announcement-card.info { border-left-color: #6c757d; }
        .announcement-card.warning { border-left-color: #212529; }
        .announcement-card.success { border-left-color: #495057; }
        .announcement-card.danger { border-left-color: #212529; }

        .announcement-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .announcement-icon {
            font-size: 22px;
            color: #212529;
        }

        .announcement-title {
            font-weight: 600;
            font-size: 18px;
            color: #212529;
            margin: 0;
        }

        .announcement-content {
            color: #495057;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 12px;
            padding-left: 34px;
        }

        .announcement-meta {
            padding-left: 34px;
            font-size: 13px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .no-announcements {
            text-align: center;
            padding: 60px 20px;
            color: #adb5bd;
        }

        .no-announcements i {
            font-size: 56px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .no-announcements p {
            font-size: 16px;
            color: #6c757d;
        }

        /* Features Section */
        .features-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .feature-card {
            background: #ffffff;
            padding: 32px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.2s;
            border: 1px solid #e9ecef;
        }

        .feature-card:hover {
            border-color: #212529;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .feature-icon {
            font-size: 40px;
            color: #212529;
            margin-bottom: 16px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 8px;
        }

        .feature-desc {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-container">
            <div class="logo-section">
                <img src="https://static.vecteezy.com/system/resources/previews/011/107/359/original/bank-3d-icon-illustration-png.png" alt="Bank Logo">
                <span class="logo-text">{{ env('APP_NAME') }}</span>
            </div>
            <a href="{{ route('login') }}" class="login-btn">
                <i class="mdi mdi-login"></i> Login
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome to Our Bank</h1>
            <p>Secure, Fast, and Reliable Banking Services</p>
        </div>

        <!-- Announcements Section -->
        <div class="announcements-section">
            <div class="section-title">
                <i class="mdi mdi-bullhorn"></i>
                Latest Announcements
            </div>

            @if(isset($announcements) && $announcements->count() > 0)
                @foreach($announcements as $announcement)
                    <div class="announcement-card {{ $announcement->type }}">
                        <div class="announcement-header">
                            @if($announcement->type == 'info')
                                <i class="mdi mdi-information announcement-icon" style="color: #17a2b8;"></i>
                            @elseif($announcement->type == 'warning')
                                <i class="mdi mdi-alert announcement-icon" style="color: #ffc107;"></i>
                            @elseif($announcement->type == 'success')
                                <i class="mdi mdi-check-circle announcement-icon" style="color: #28a745;"></i>
                            @elseif($announcement->type == 'danger')
                                <i class="mdi mdi-alert-circle announcement-icon" style="color: #dc3545;"></i>
                            @endif
                            <h3 class="announcement-title">{{ $announcement->title }}</h3>
                        </div>
                        <div class="announcement-content">
                            {{ $announcement->content }}
                        </div>
                        <div class="announcement-meta">
                            <i class="mdi mdi-clock-outline"></i>
                            {{ $announcement->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-announcements">
                    <i class="mdi mdi-bullhorn-outline"></i>
                    <p>No announcements at this time</p>
                </div>
            @endif
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <div class="feature-card">
                <i class="mdi mdi-shield-check feature-icon"></i>
                <div class="feature-title">Secure Banking</div>
                <div class="feature-desc">Your money is protected with advanced security measures</div>
            </div>
            <div class="feature-card">
                <i class="mdi mdi-credit-card-multiple feature-icon"></i>
                <div class="feature-title">Multiple Accounts</div>
                <div class="feature-desc">Manage all your accounts from one place</div>
            </div>
            <div class="feature-card">
                <i class="mdi mdi-swap-horizontal feature-icon"></i>
                <div class="feature-title">Easy Transfers</div>
                <div class="feature-desc">Send money quickly and securely</div>
            </div>
            <div class="feature-card">
                <i class="mdi mdi-chart-line feature-icon"></i>
                <div class="feature-title">Track Expenses</div>
                <div class="feature-desc">Monitor your spending with detailed reports</div>
            </div>
        </div>
    </div>

    <script src="{{ url('') }}/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="{{ url('') }}/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
