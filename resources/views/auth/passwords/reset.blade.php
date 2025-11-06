@extends('layouts.app')

@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" id="reset-tab" data-bs-toggle="tab"
                    href="#tab-item-reset" role="tab" aria-controls="tab-item-reset" aria-selected="true">
                    Reset Password
                </a>
            </li>
        </ul>

        <div class="tab-content pt-2" id="login_register_tab_content">
            <div class="tab-pane fade show active" id="tab-item-reset" role="tabpanel" aria-labelledby="reset-tab">
                <div class="login-form">

                    {{-- ✅ Pesan sukses (link reset terkirim) --}}
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" name="reset-form" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" placeholder="Email address"
                                value="{{ $email ?? old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" placeholder="New password" required>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">Confirm Password</label>
                            <input id="password-confirm" type="password"
                                class="form-control" name="password_confirmation"
                                placeholder="Confirm password" required>
                        </div>

                        {{-- Submit --}}
                        <button class="btn btn-primary w-100 text-uppercase" type="submit">
                            Reset Password
                        </button>

                        {{-- Back to login --}}
                        <div class="customer-option mt-4 text-center">
                            <span class="text-secondary">Remembered your password?</span>
                            <a href="{{ route('login') }}" class="btn-text" style="color:#222222;">
                                Log In
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
