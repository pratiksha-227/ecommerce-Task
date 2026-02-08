@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h2 class="h5 mb-0">Login</h2>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group password-wrap">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="login_password_toggle" title="Show password" aria-label="Toggle password visibility">
                                <i class="fa-regular fa-eye" id="login_password_icon"></i>
                            </button>
                        </div>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
        
    </div>
</div>
@push('styles')
<style>.password-wrap .form-control { flex: 1; min-width: 0; } .password-wrap .btn { flex-shrink: 0; }</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('login_password_toggle');
    if (toggle) {
        toggle.addEventListener('click', function() {
            var input = document.getElementById('password');
            var icon = document.getElementById('login_password_icon');
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
});
</script>
@endpush
@endsection
