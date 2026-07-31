<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Portal') - SpinHR</title>
    
    <!-- Vendors styles -->
    <link rel="stylesheet" href="{{ asset('backend/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/vendors/simplebar.css') }}">
    
    <!-- Main styles for application -->
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <!-- Custom & Vendor icons styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('backend/css/custom.css') }}" rel="stylesheet">
    
    <script src="{{ asset('backend/js/config.js') }}"></script>
    <script src="{{ asset('backend/js/color-modes.js') }}"></script>
    @stack('styles')
</head>
<body>
    <!-- Employee Sidebar Navigation -->
    @include('layouts.employee_sidebar')

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

                @php
                    $emp = auth()->user()->employee ?? null;
                    $comp = $emp ? $emp->company : null;
                @endphp

                <!-- Company Branding -->
                <div class="d-flex align-items-center ms-2">
                    @if($comp && !empty($comp->logo))
                        <img src="{{ asset('storage/' . $comp->logo) }}" alt="Logo" class="me-2" style="max-height: 35px;">
                    @else
                        <i class="bi bi-building me-2 text-primary fs-5"></i>
                    @endif
                    <span class="fw-bold text-dark fs-6">{{ $comp->name ?? 'SpinHR' }}</span>
                </div>

                <ul class="header-nav ms-auto me-3">
                    <li class="nav-item">
                        <span class="nav-link fw-semibold text-body">
                            {{ $emp->name ?? Auth::user()->name }} 
                            <span class="badge bg-success ms-1">EMPLOYEE</span>
                        </span>
                    </li>
                </ul>

                <ul class="header-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            @if($emp && !empty($emp->photo))
                                <img src="{{ asset('storage/' . $emp->photo) }}" alt="Photo" class="avatar avatar-md rounded-circle object-fit-cover">
                            @else
                                <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'E', 0, 1)) }}
                                </div>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Employee Session</div>
                            <div class="px-3 py-2 small text-muted border-bottom">
                                <div><strong>{{ $emp->name ?? Auth::user()->name }}</strong></div>
                                <div>{{ Auth::user()->email }}</div>
                                <div class="text-primary mt-1">{{ $emp->employee_code ?? '' }}</div>
                            </div>
                            <a href="{{ route('employee.profile') }}" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a>
                            <div class="dropdown-divider my-1"></div>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="container-fluid px-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb my-0">
                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Employee Portal</a></li>
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
                <a href="#">SpinHR ESS</a> &copy; {{ date('Y') }} All Rights Reserved.
            </div>
            <div class="ms-auto">
                Powered by&nbsp;<a href="#">SpinHR System</a>
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

        function autoHideSuccessAlerts() {
            document.querySelectorAll('.alert-success:not([data-auto-dismiss])').forEach(function(alert) {
                alert.setAttribute('data-auto-dismiss', 'true');
                setTimeout(function() {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                }, 4000);
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            autoHideSuccessAlerts();

            const alertObserver = new MutationObserver(function() {
                autoHideSuccessAlerts();
            });
            alertObserver.observe(document.body, { childList: true, subtree: true });
        });
    </script>
    @stack('scripts')
</body>
</html>
