
<header class="topbar d-print-none" data-navbarbg="skin5">
    <nav class="navbar top-navbar navbar-expand-md">
        <!-- Mobile Toggle -->
        <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
            <i class="ti-menu ti-close"></i>
        </a>

        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <!-- Left Side -->
            <ul class="navbar-nav float-left mr-auto"></ul>

            <!-- Right Side -->
            <ul class="navbar-nav float-right align-items-center">
                <!-- Messages -->
                <li class="nav-item">
                    <a href="{{route('inbox')}}" class="nav-link nav-icon-link" title="Messages">
                        <i class="fa fa-envelope"></i>
                    </a>
                </li>

                <!-- User Profile -->
                <li class="nav-item dropdown user-dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle user-avatar" src="{{ url('') }}/assets/images/users/shafi.jpg" alt="user" width="40" height="40">
                        <span class="user-name ml-2 d-none d-lg-inline">{{ auth()->user()->first_name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right user-dd animated fadeIn">
                        <div class="dropdown-header">
                            <h6 class="mb-0">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{route('profile')}}">
                            <i class="ti-user mr-2"></i> My Profile
                        </a>
                        <a class="dropdown-item" href="{{route('inbox')}}">
                            <i class="ti-email mr-2"></i> Inbox
                        </a>
                        <a class="dropdown-item" href="{{route('settings')}}">
                            <i class="ti-settings mr-2"></i> Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{route('logout')}}">
                            <i class="ti-power-off mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>

<style>
    /* Modern Topbar Styles */
    .topbar {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .top-navbar {
        background: #ffffff !important;
        padding: 0 25px;
        padding-left: 270px;
        height: 70px;
    }

    /* Navigation Icons */
    .nav-icon-link {
        position: relative;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #6c757d;
        transition: all 0.3s ease;
        margin: 0 5px;
    }

    .nav-icon-link:hover {
        background: #f8f9fa;
        color: #1e3c72;
    }

    .nav-icon-link i {
        font-size: 18px;
    }

    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 10px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
    }

    /* User Dropdown */
    .user-dropdown .nav-link {
        padding: 0;
        color: #495057;
        text-decoration: none;
    }

    .user-dropdown .nav-link:hover {
        color: #1e3c72;
    }

    .user-avatar {
        border: 2px solid #e9ecef;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .user-avatar:hover {
        border-color: #1e3c72;
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.2);
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
    }

    /* Dropdown Menu */
    .user-dd {
        min-width: 260px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-radius: 12px;
        padding: 0;
        margin-top: 10px;
    }

    .dropdown-header {
        padding: 20px 20px 15px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .dropdown-header h6 {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .dropdown-header small {
        color: rgba(255, 255, 255, 0.8);
    }

    .user-dd .dropdown-item {
        padding: 12px 20px;
        font-size: 14px;
        color: #495057;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .user-dd .dropdown-item i {
        width: 20px;
        font-size: 16px;
    }

    .user-dd .dropdown-item:hover {
        background: #f8f9fa;
        color: #1e3c72;
        padding-left: 25px;
    }

    .user-dd .dropdown-item.text-danger:hover {
        background: #fff5f5;
        color: #dc3545;
    }

    .user-dd .dropdown-divider {
        margin: 0;
    }

    /* Mobile Toggle */
    .nav-toggler {
        color: #1e3c72;
        font-size: 24px;
        cursor: pointer;
        padding: 10px;
        border: none;
        background: transparent;
        margin-left: 20px;
    }

    .nav-toggler:hover {
        color: #2a5298;
    }

    /* Animation */
    .fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .top-navbar {
            padding: 0 15px;
            padding-left: 15px;
        }
    }
</style>
