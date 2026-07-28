<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - SpinHR</title>

    <!-- Vendors styles -->
    <link rel="stylesheet" href="{{ asset('backend/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/vendors/simplebar.css') }}">
    
    <!-- Main styles for application -->
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/examples.css') }}" rel="stylesheet">
    
    <script src="{{ asset('backend/js/config.js') }}"></script>
    <script src="{{ asset('backend/js/color-modes.js') }}"></script>
</head>
<body>
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
        <div class="container" style="max-width: 28rem">
            <div class="d-flex flex-column gap-4">
                
                <div class="text-center">
                    <h1 class="h3 fw-bold text-primary mb-1">SpinHR</h1>
                    <p class="text-body-secondary small">Human Resource Management System</p>
                </div>

                <div class="card p-4 shadow-sm">
                    <div class="card-body d-flex flex-column gap-3">
                        <h2 class="h5 text-center mb-3">Sign in to your account</h2>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form class="d-flex flex-column gap-3" action="{{ route('login.post') }}" method="POST" autocomplete="off">
                            @csrf
                            <div>
                                <label class="form-label fw-semibold" for="email">Email address</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required autofocus autocomplete="off">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label fw-semibold" for="password">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="form-check-label small">Remember me</span>
                                </label>
                            </div>

                            <div>
                                <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center text-body-secondary small">
                    &copy; {{ date('Y') }} SpinHR. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- CoreUI and necessary plugins -->
    <script src="{{ asset('backend/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/vendors/simplebar/js/simplebar.min.js') }}"></script>
</body>
</html>
