<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - SpinHR</title>
    
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
    <!-- Sidebar Navigation -->
    @include('layouts.navbar')

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
                <!-- Company Switcher Dropdown -->
                @if(isset($userCompanies) && count($userCompanies) > 0)
                <ul class="header-nav me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link py-1 px-3 border rounded text-body d-flex align-items-center bg-body-tertiary" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-building me-2 text-primary"></i>
                            <span class="fw-semibold me-1">
                                @if(isset($currentCompany) && $currentCompany)
                                    {{ $currentCompany->name }}
                                @else
                                    All Companies
                                @endif
                            </span>
                            <i class="bi bi-chevron-down ms-1 small"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                                Select Company
                            </div>
                            
                            @if(isset($isSuperAdmin) && $isSuperAdmin)
                            <form action="{{ route('company.switch') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="company_id" value="all">
                                <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between {{ (isset($currentCompanyId) && $currentCompanyId === null) ? 'active bg-primary text-white fw-bold' : '' }}">
                                    <span><i class="bi bi-buildings me-2"></i>All Companies</span>
                                    @if(isset($currentCompanyId) && $currentCompanyId === null)
                                        <i class="bi bi-check-lg"></i>
                                    @endif
                                </button>
                            </form>
                            <div class="dropdown-divider my-1"></div>
                            @endif

                            @foreach($userCompanies as $comp)
                            <form action="{{ route('company.switch') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="company_id" value="{{ $comp->id }}">
                                <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between {{ (isset($currentCompanyId) && (int)$currentCompanyId === (int)$comp->id) ? 'active bg-primary text-white fw-bold' : '' }}">
                                    <span><i class="bi bi-building me-2"></i>{{ $comp->name }}</span>
                                    @if(isset($currentCompanyId) && (int)$currentCompanyId === (int)$comp->id)
                                        <i class="bi bi-check-lg"></i>
                                    @endif
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </li>
                </ul>
                @endif

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
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
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

        // Auto hide success alerts after 4 seconds (handles initial page load & dynamic AJAX alerts across all modules)
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

            // Observe DOM changes for dynamically inserted alerts via AJAX across all modules
            const alertObserver = new MutationObserver(function() {
                autoHideSuccessAlerts();
            });
            alertObserver.observe(document.body, { childList: true, subtree: true });

            // Restore save button state if re-enabled on validation errors
            setInterval(function() {
                document.querySelectorAll('button[data-is-loading="true"]').forEach(function(btn) {
                    if (!btn.disabled && !btn.classList.contains('disabled')) {
                        const origHtml = btn.getAttribute('data-original-html');
                        if (origHtml) {
                            btn.innerHTML = origHtml;
                        }
                        btn.removeAttribute('data-is-loading');
                    }
                });
            }, 200);
        });

        // Global small spinner loader for save/submit buttons across all modules
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const btn = form.querySelector('button[type="submit"], #btn-save');
            if (btn && !btn.getAttribute('data-is-loading')) {
                btn.setAttribute('data-is-loading', 'true');
                btn.setAttribute('data-original-html', btn.innerHTML);
                
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + btn.innerHTML;
                btn.disabled = true;
                btn.classList.add('disabled');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
