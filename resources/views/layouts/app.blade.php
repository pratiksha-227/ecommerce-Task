<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Commerce CMS') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body { min-height: 100vh; background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.08); }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .product-img { max-height: 300px;height: 100%; width: 100%; object-fit: cover; }
        .img-thumb { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .product-thumb:hover { opacity: 0.9; }
        .password-wrap .form-control { flex: 1; min-width: 0; }
        .password-wrap .btn { flex-shrink: 0; }
        @media (max-width: 991.98px) {
            .navbar .container { flex-wrap: nowrap; }
            .navbar-collapse { display: none !important; }
        }
        #navSidebar.offcanvas { width: 280px; }
        #navSidebar .nav-link { padding: 0.6rem 0; }
        #navSidebar .dropdown-menu { position: static !important; box-shadow: none; border: none; padding-left: 1rem; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container d-flex flex-wrap flex-lg-nowrap align-items-center">
            <a class="navbar-brand" href="{{ route('products.index') }}">E-Commerce CMS</a>
            <button class="navbar-toggler order-last d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#navSidebar" aria-controls="navSidebar" aria-label="Toggle sidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end order-lg-0 d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                            Cart
                            @if(isset($headerCartCount) && $headerCartCount > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $headerCartCount }}</span>
                            @endif
                        </a>
                    </li>
                    @if(auth()->check())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('my-orders.*') ? 'active' : '' }}" href="{{ route('my-orders.index') }}">My Orders</a>
                    </li>
                    @endif
                    @if(auth()->check() && auth()->user()->is_admin)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ auth()->user()->name }} (ID: {{ auth()->id() }})</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->is_admin ? 'Admin' : 'Customer' }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                @if(auth()->user()->is_admin)
                                <li><a class="dropdown-item" href="{{ route('products.create') }}">Add Product</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">All Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('my-orders.index') }}">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="p-0 m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signinModal">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupModal">Sign up</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Sidebar for mobile/tablet: open/close via navbar toggler --}}
    <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="navSidebar" aria-labelledby="navSidebarLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="navSidebarLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="navbar-nav flex-column p-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        Cart
                        @if(isset($headerCartCount) && $headerCartCount > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $headerCartCount }}</span>
                        @endif
                    </a>
                </li>
                @if(auth()->check())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('my-orders.*') ? 'active' : '' }}" href="{{ route('my-orders.index') }}">My Orders</a>
                </li>
                @endif
                @if(auth()->check() && auth()->user()->is_admin)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav flex-column p-3 border-top">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ auth()->user()->name }} (ID: {{ auth()->id() }})</a>
                        <ul class="dropdown-menu dropdown-menu-end w-100">
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->is_admin ? 'Admin' : 'Customer' }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            @if(auth()->user()->is_admin)
                            <li><a class="dropdown-item" href="{{ route('products.create') }}">Add Product</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}">All Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('my-orders.index') }}">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="p-0 m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signinModal" data-bs-dismiss="offcanvas">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="offcanvas">Sign up</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>

    <main class="container py-4">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- Login modal (email or phone + password) --}}
    @guest
    <div class="modal fade" id="signinModal" tabindex="-1" aria-labelledby="signinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signinModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="modal-body">
                        @if($errors->has('email_or_phone'))
                            <div class="alert alert-danger py-2">{{ $errors->first('email_or_phone') }}</div>
                        @endif
                        <div class="mb-3">
                            <label for="modal_email_or_phone" class="form-label">Email or phone number</label>
                            <input type="text" class="form-control @error('email_or_phone') is-invalid @enderror" id="modal_email_or_phone" name="email_or_phone" value="{{ old('email_or_phone') }}" placeholder="Enter email or phone" required autocomplete="username">
                            @error('email_or_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="modal_password" class="form-label">Password</label>
                            <div class="input-group password-wrap">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="modal_password" name="password" placeholder="Password" required autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" id="modal_password_toggle" title="Show password" aria-label="Toggle password visibility">
                                    <i class="fa-regular fa-eye" id="modal_password_icon"></i>
                                </button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0 form-check">
                            <input type="checkbox" class="form-check-input" id="modal_remember" name="remember" value="1">
                            <label class="form-check-label" for="modal_remember">Remember me</label>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-wrap">
                        <div class="w-100 small text-muted mb-2">Don't have an account?</div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal">Sign up</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sign up modal (name + email or phone + password) --}}
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupModalLabel">Create account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="modal-body">
                        @if($errors->has('name'))
                            <div class="alert alert-danger py-2">{{ $errors->first('name') }}</div>
                        @endif
                        @if($errors->has('email_or_phone'))
                            <div class="alert alert-danger py-2">{{ $errors->first('email_or_phone') }}</div>
                        @endif
                        @if($errors->has('password'))
                            <div class="alert alert-danger py-2">{{ $errors->first('password') }}</div>
                        @endif
                        <div class="mb-3">
                            <label for="signup_name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="signup_name" name="name" value="{{ old('name') }}" placeholder="Your name" required autocomplete="name">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="signup_email_or_phone" class="form-label">Email or phone number</label>
                            <input type="text" class="form-control @error('email_or_phone') is-invalid @enderror" id="signup_email_or_phone" name="email_or_phone" value="{{ old('email_or_phone') }}" placeholder="Email or phone" required autocomplete="username">
                            <small class="text-muted">Use email (e.g. you@example.com) or phone (e.g. 9876543210)</small>
                            @error('email_or_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="signup_password" class="form-label">Password</label>
                            <div class="input-group password-wrap">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="signup_password" name="password" placeholder="Min 6 characters" required autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="signup_password_toggle" title="Show password">
                                    <i class="fa-regular fa-eye" id="signup_password_icon"></i>
                                </button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="signup_password_confirmation" class="form-label">Confirm password</label>
                            <div class="input-group password-wrap">
                                <input type="password" class="form-control" id="signup_password_confirmation" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="signup_password_conf_toggle" title="Show password">
                                    <i class="fa-regular fa-eye" id="signup_password_conf_icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-wrap">
                        <div class="w-100 small text-muted mb-2">Already have an account?</div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signinModal">Login</button>
                        <button type="submit" class="btn btn-primary">Sign up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endguest

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('open_signup_modal'))
            var signup = document.getElementById('signupModal');
            if (signup) new bootstrap.Modal(signup).show();
            @elseif(session('open_signin_modal') || $errors->has('email_or_phone') || $errors->has('password'))
            var m = document.getElementById('signinModal');
            if (m) new bootstrap.Modal(m).show();
            @endif
            var toggle = document.getElementById('modal_password_toggle');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    var input = document.getElementById('modal_password');
                    var icon = document.getElementById('modal_password_icon');
                    if (!input || !icon) return;
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        toggle.setAttribute('title', 'Hide password');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        toggle.setAttribute('title', 'Show password');
                    }
                });
            }
            function setupPasswordToggle(btnId, inputId, iconId) {
                var btn = document.getElementById(btnId);
                if (!btn) return;
                btn.addEventListener('click', function() {
                    var input = document.getElementById(inputId);
                    var icon = document.getElementById(iconId);
                    if (!input || !icon) return;
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            }
            setupPasswordToggle('signup_password_toggle', 'signup_password', 'signup_password_icon');
            setupPasswordToggle('signup_password_conf_toggle', 'signup_password_confirmation', 'signup_password_conf_icon');

            // Close mobile sidebar when a nav link (Products/Cart) is clicked
            var sidebar = document.getElementById('navSidebar');
            if (sidebar) {
                sidebar.querySelectorAll('a[href^="/"]').forEach(function(link) {
                    link.addEventListener('click', function() {
                        var offcanvas = bootstrap.Offcanvas.getInstance(sidebar);
                        if (offcanvas) offcanvas.hide();
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
