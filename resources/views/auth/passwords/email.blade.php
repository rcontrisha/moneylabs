@extends('layouts.app')

@section('content')
<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" id="forgot-tab" data-bs-toggle="tab"
                    href="#tab-item-forgot" role="tab" aria-controls="tab-item-forgot" aria-selected="true">
                    Reset Password
                </a>
            </li>
        </ul>

        <div class="tab-content pt-2" id="login_register_tab_content">
            <div class="tab-pane fade show active" id="tab-item-forgot" role="tabpanel" aria-labelledby="forgot-tab">
                <div class="login-form">

                    {{-- ✅ Notifikasi sukses kirim link --}}
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" name="forgot-form" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" placeholder="Email address"
                                value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button class="btn btn-primary w-100 text-uppercase" type="submit">
                            Send Password Reset Link
                        </button>

                        {{-- Back to login --}}
                        <div class="customer-option mt-4 text-center">
                            <span class="text-secondary">Remember your password?</span>
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
