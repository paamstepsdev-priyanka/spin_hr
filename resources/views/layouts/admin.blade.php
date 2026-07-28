<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>@yield('title', 'Admin Dashboard') - SpinHR</title>
    
    <!-- Vendors styles -->
    <link rel="stylesheet" href="{{ asset('backend/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/vendors/simplebar.css') }}">
    
    <!-- Main styles for application -->
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/examples.css') }}" rel="stylesheet">
    
    <script src="{{ asset('backend/js/config.js') }}"></script>
    <script src="{{ asset('backend/js/color-modes.js') }}"></script>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
        <div class="sidebar-header border-bottom">
            <div class="sidebar-brand me-auto">
                <span class="fs-4 fw-bold text-white px-2">SpinHR Admin</span>
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
        </div>
        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="var(--ci-primary-color, currentcolor)" d="M425.706 142.294A240 240 0 0 0 16 312v88h144v-32H48v-56c0-114.691 93.309-208 208-208s208 93.309 208 208v56H352v32h144v-88a238.43 238.43 0 0 0-70.294-169.706" class="ci-primary" />
                        <path fill="var(--ci-primary-color, currentcolor)" d="M80 264h32v32H80zm160-136h32v32h-32zm-104 40h32v32h-32zm264 96h32v32h-32zm-102.778 71.1 69.2-144.173-28.85-13.848-69.183 144.135a64.141 64.141 0 1 0 28.833 13.886M256 416a32 32 0 1 1 32-32 32.036 32.036 0 0 1-32 32" class="ci-primary" />
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="nav-title">Management</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}" href="{{ route('companies.index') }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="var(--ci-primary-color, currentcolor)" d="M432 16H80a16 16 0 0 0-16 16v448a16 16 0 0 0 16 16h352a16 16 0 0 0 16-16V32a16 16 0 0 0-16-16zm-16 448H96V48h320z" class="ci-primary"/>
                        <path fill="var(--ci-primary-color, currentcolor)" d="M128 96h64v64h-64zm128 0h64v64h-64zm128 0h64v64h-64zM128 224h64v64h-64zm128 0h64v64h-64zm128 0h64v64h-64zM128 352h64v64h-64zm128 0h64v64h-64zm128 0h64v64h-64z" class="ci-primary"/>
                    </svg>
                    Companies
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="var(--ci-primary-color, currentcolor)" d="M256 256a112 112 0 1 0-112-112 112.13 112.13 0 0 0 112 112zm0-192a80 80 0 1 1-80 80 80.09 80.09 0 0 1 80-80zM400 464H112a48.05 48.05 0 0 1-48-48v-32a112.13 112.13 0 0 1 112-112h160a112.13 112.13 0 0 1 112 112v32a48.05 48.05 0 0 1-48 48zm-224-160a80.09 80.09 0 0 0-80 80v32a16.02 16.02 0 0 0 16 16h288a16.02 16.02 0 0 0 16-16v-32a80.09 80.09 0 0 0-80-80z" class="ci-primary"/>
                    </svg>
                    User Management
                </a>
            </li>
        </ul>
        <div class="sidebar-footer border-top d-none d-md-flex">
            <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="wrapper d-flex flex-column min-vh-100">
        <!-- Header Navbar -->
        <header class="header header-sticky p-0 mb-4">
            <div class="container-fluid border-bottom px-4">
                <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px">
                    <svg class="icon icon-lg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="var(--ci-primary-color, currentcolor)" d="M80 96h352v32H80zm0 144h352v32H80zm0 144h352v32H80z" class="ci-primary" />
                    </svg>
                </button>
                <ul class="header-nav ms-auto me-3">
                    <li class="nav-item">
                        <span class="nav-link fw-semibold text-body">
                            {{ Auth::user()->name ?? 'Admin' }} 
                            <span class="badge bg-primary ms-1 text-uppercase">{{ Auth::user()->role ?? 'Admin' }}</span>
                        </span>
                    </li>
                </ul>
                <ul class="header-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Account</div>
                            <div class="px-3 py-2 small text-muted border-bottom">
                                <div><strong>{{ Auth::user()->name ?? '' }}</strong></div>
                                <div>{{ Auth::user()->email ?? '' }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                    <svg class="icon me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path fill="var(--ci-primary-color, currentcolor)" d="M77.155 272.034H351.75v-32.001H77.155l75.053-75.053v-.001l-22.628-22.626-113.681 113.68.001.001h-.001L129.58 369.715l22.628-22.627v-.001z" class="ci-primary" />
                                        <path fill="var(--ci-primary-color, currentcolor)" d="M160 16v32h304v416H160v32h336V16z" class="ci-primary" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="container-fluid px-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb my-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><span>@yield('title', 'Dashboard')</span></li>
                    </ol>
                </nav>
            </div>
        </header>

        <!-- Main Body Content -->
        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer px-4">
            <div>
                <a href="#">SpinHR</a> &copy; {{ date('Y') }} All Rights Reserved.
            </div>
            <div class="ms-auto">
                Developed by&nbsp;<a href="#">PaamStep</a>
            </div>
        </footer>
    </div>

    <!-- CoreUI and necessary plugins -->
    <script src="{{ asset('backend/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/vendors/simplebar/js/simplebar.min.js') }}"></script>
    <script>
        const header = document.querySelector("header.header");
        document.addEventListener("scroll", () => {
            if (header) {
                header.classList.toggle("shadow-sm", document.documentElement.scrollTop > 0);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
