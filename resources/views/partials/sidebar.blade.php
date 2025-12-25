
<aside class="left-sidebar d-print-none" data-sidebarbg="skin6">
    <div class="scroll-sidebar">
        <!-- Logo & Brand -->
        <div class="sidebar-logo">
            <a class="d-flex align-items-center" href="{{ url('/dashboard') }}">
                <img src="{{ url('') }}/assets/images/bank.webp" width="40" height="40" alt="Bank Logo" class="logo-img" />
                <span class="brand-title">Online Banking</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="pt-3">

                <!-- Dashboard -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{route('dashboard')}}" aria-expanded="false">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <!-- Profile -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{route('profile')}}" aria-expanded="false">
                        <i class="mdi mdi-account-circle"></i>
                        <span class="hide-menu">Profile</span>
                    </a>
                </li>

                <!-- Accounts -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{route('accounts')}}" aria-expanded="false">
                        <i class="mdi mdi-bank"></i>
                        <span class="hide-menu">Accounts</span>
                    </a>
                </li>

                <!-- Cards -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{route('cards')}}" aria-expanded="false">
                        <i class="mdi mdi-credit-card-multiple"></i>
                        <span class="hide-menu">Cards</span>
                    </a>
                </li>

                <!-- Transactions -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{route('all_transactions')}}" aria-expanded="false">
                        <i class="mdi mdi-swap-horizontal"></i>
                        <span class="hide-menu">Transactions</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{route('settings')}}" aria-expanded="false">
                        <i class="mdi mdi-settings"></i>
                        <span class="hide-menu">Settings</span>
                    </a>
                </li>

                @if(auth()->user()->hasRole('System-Admin'))
                    <li class="sidebar-divider mt-3 mb-3"></li>
                    <li class="nav-small-cap">
                        <span class="hide-menu">ADMINISTRATION</span>
                    </li>
                @endif

                <!-- Users -->
                @can('list-users')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark" href="{{ route('users') }}" aria-expanded="false">
                            <i class="mdi mdi-account-multiple"></i>
                            <span class="hide-menu">Users</span>
                        </a>
                    </li>
                @endcan

                <!-- Currencies -->
                @can('list-currencies')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark" href="{{ route('currencies') }}" aria-expanded="false">
                            <i class="mdi mdi-currency-usd"></i>
                            <span class="hide-menu">Currencies</span>
                        </a>
                    </li>
                @endcan

                <!-- Card Types -->
                @can('list-card-types')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark" href="{{ route('card_types') }}" aria-expanded="false">
                            <i class="mdi mdi-cards"></i>
                            <span class="hide-menu">Card Types</span>
                        </a>
                    </li>
                @endcan

                <!-- Banks -->
                @can('list-banks')
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark" href="{{ route('banks') }}" aria-expanded="false">
                            <i class="mdi mdi-domain"></i>
                            <span class="hide-menu">Banks</span>
                        </a>
                    </li>
                @endcan

            </ul>
        </nav>
    </div>

    <!-- User Profile Section -->
    <div class="sidebar-user-section">
        <div class="sidebar-user-info">
            <img class="sidebar-user-avatar" src="{{ url('') }}/assets/images/users/oms.jpeg" alt="user">
            <div class="sidebar-user-details">
                <span class="sidebar-user-name">{{ auth()->user()->first_name }}</span>
                <span class="sidebar-user-email">{{ auth()->user()->email }}</span>
            </div>
        </div>
        <div class="sidebar-user-actions">
            <a href="{{route('inbox')}}" class="sidebar-user-action" title="Messages">
                <i class="mdi mdi-email-outline"></i>
            </a>
            <a href="{{route('settings')}}" class="sidebar-user-action" title="Settings">
                <i class="mdi mdi-settings"></i>
            </a>
            <a href="{{route('logout')}}" class="sidebar-user-action text-danger" title="Logout">
                <i class="mdi mdi-logout"></i>
            </a>
        </div>
    </div>
</aside>

<style>
    /* Modern Minimal SaaS Sidebar - Override all global styles */
    aside.left-sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        height: 100vh !important;
        width: 250px !important;
        background: #ffffff !important;
        border-right: 1px solid #e8e8e8 !important;
        z-index: 1000 !important;
        overflow: hidden !important;
        box-shadow: none !important;
    }

    aside.left-sidebar .scroll-sidebar {
        background: transparent !important;
        height: 100vh !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding-top: 0 !important;
        padding-bottom: 100px !important;
        display: flex !important;
        flex-direction: column !important;
    }

    aside.left-sidebar .sidebar-nav {
        flex: 1 !important;
    }

    /* User Profile Section at Bottom */
    aside.left-sidebar > .sidebar-user-section {
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        padding: 16px !important;
        background: #ffffff !important;
        border-top: 1px solid #e8e8e8 !important;
        z-index: 10 !important;
    }

    aside.left-sidebar .sidebar-user-info {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        margin-bottom: 12px !important;
    }

    aside.left-sidebar .sidebar-user-avatar {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        flex-shrink: 0 !important;
    }

    aside.left-sidebar .sidebar-user-details {
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    aside.left-sidebar .sidebar-user-name {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #18181b !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    aside.left-sidebar .sidebar-user-email {
        font-size: 12px !important;
        color: #71717a !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    aside.left-sidebar .sidebar-user-actions {
        display: flex !important;
        gap: 8px !important;
    }

    aside.left-sidebar .sidebar-user-action {
        width: 36px !important;
        height: 36px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        color: #71717a !important;
        background: #f4f4f5 !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
    }

    aside.left-sidebar .sidebar-user-action:hover {
        background: #e4e4e7 !important;
        color: #18181b !important;
    }

    aside.left-sidebar .sidebar-user-action.text-danger {
        color: #ef4444 !important;
    }

    aside.left-sidebar .sidebar-user-action.text-danger:hover {
        background: #fef2f2 !important;
        color: #dc2626 !important;
    }

    aside.left-sidebar .sidebar-user-action i {
        font-size: 18px !important;
    }

    /* Sidebar Logo */
    aside.left-sidebar .sidebar-logo {
        padding: 20px 16px !important;
        border-bottom: 1px solid #e8e8e8 !important;
        margin-bottom: 20px !important;
    }

    aside.left-sidebar .sidebar-logo a {
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    aside.left-sidebar .sidebar-logo .logo-img {
        border-radius: 8px !important;
        object-fit: cover !important;
        flex-shrink: 0 !important;
    }

    aside.left-sidebar .sidebar-logo .brand-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #18181b !important;
        margin: 0 !important;
    }

    aside.left-sidebar .sidebar-nav {
        padding: 0 !important;
        background: transparent !important;
        margin: 0 !important;
    }

    aside.left-sidebar #sidebarnav {
        padding: 0 16px !important;
        background: transparent !important;
        margin: 0 !important;
        list-style: none !important;
    }

    aside.left-sidebar .sidebar-item {
        margin-bottom: 4px !important;
        list-style: none !important;
        padding: 0 !important;
    }

    aside.left-sidebar .sidebar-link {
        display: flex !important;
        align-items: center !important;
        padding: 11px 14px !important;
        color: #1b1b1cff !important;
        text-decoration: none !important;
        border-radius: 6px !important;
        transition: all 0.15s ease !important;
        font-size: 14px !important;
        font-weight: 400 !important;
        position: relative !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        line-height: 1.4 !important;
    }

    aside.left-sidebar .sidebar-link:hover {
        background: #f4f4f5 !important;
        color: #18181b !important;
        text-decoration: none !important;
        box-shadow: none !important;
        border: none !important;
    }

    aside.left-sidebar .sidebar-link.active,
    aside.left-sidebar .sidebar-link:active {
        background: #f4f4f5 !important;
        color: #18181b !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        box-shadow: none !important;
        border: none !important;
    }

    aside.left-sidebar .sidebar-link i {
        font-size: 18px !important;
        margin-right: 12px !important;
        width: 18px !important;
        text-align: center !important;
        color: #71717a !important;
        transition: color 0.15s ease !important;
        flex-shrink: 0 !important;
    }

    aside.left-sidebar .sidebar-link:hover i,
    aside.left-sidebar .sidebar-link.active i {
        color: #27272a !important;
    }

    aside.left-sidebar .hide-menu {
        margin: 0 !important;
        padding: 0 !important;
    }

    aside.left-sidebar .sidebar-divider {
        border-top: 1px solid #e8e8e8 !important;
        margin: 16px 16px !important;
        list-style: none !important;
        padding: 0 !important;
    }

    aside.left-sidebar .nav-small-cap {
        padding: 16px 14px 8px !important;
        list-style: none !important;
        margin: 0 !important;
    }

    aside.left-sidebar .nav-small-cap span {
        color: #a1a1aa !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
    }

    /* Minimal Scrollbar */
    .scroll-sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .scroll-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .scroll-sidebar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .scroll-sidebar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }

    /* Adjust main content to not overlap with fixed sidebar */
    .page-wrapper {
        margin-left: 250px !important;
        padding-top: 20px !important;
    }

    .page-breadcrumb {
        padding: 20px 30px !important;
    }

    .page-title {
        font-size: 24px !important;
        font-weight: 600 !important;
        color: #18181b !important;
        margin: 0 !important;
    }

    /* Mobile Header */
    .mobile-topbar {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: #ffffff;
        border-bottom: 1px solid #e8e8e8;
        z-index: 999;
        padding: 0 16px;
        align-items: center;
        justify-content: space-between;
    }

    .mobile-menu-toggle {
        width: 40px;
        height: 40px;
        border: none;
        background: #f4f4f5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #18181b;
        font-size: 22px;
    }

    .mobile-menu-toggle:hover {
        background: #e4e4e7;
    }

    .mobile-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .mobile-brand img {
        border-radius: 6px;
    }

    .mobile-brand span {
        font-size: 16px;
        font-weight: 600;
        color: #18181b;
    }

    .mobile-user-avatar img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e8e8e8;
    }

    /* Sidebar Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }

    @media (max-width: 767.98px) {
        .mobile-topbar {
            display: flex !important;
        }

        aside.left-sidebar {
            transform: translateX(-250px) !important;
            transition: transform 0.3s ease !important;
            z-index: 1001 !important;
        }

        aside.left-sidebar.show {
            transform: translateX(0) !important;
        }

        .page-wrapper {
            margin-left: 0 !important;
            padding-top: 70px !important;
        }

        .page-breadcrumb {
            padding: 15px 20px !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.left-sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('active');
        });
    }
});
</script>
