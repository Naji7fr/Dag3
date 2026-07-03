@extends('layouts.guest')

@section('title', $pageTitle)

@section('content')
<div class="login-wrap">
    <div class="card login-card">
        <h1 class="login-title">Inloggen</h1>
        <p class="login-subtitle">Log in om Kniploket Tiko te beheren.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="post" action="{{ route('login.submit') }}" id="login-form" novalidate>
            @csrf

            <div class="form-group">
                <label for="email">E-mailadres <span class="required">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    @class(['input-error' => $errors->has('email')])
                    required
                >
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Wachtwoord <span class="required">*</span></label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    @class(['input-error' => $errors->has('password')])
                    required
                >
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    Onthoud mij
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
        </form>

        <p class="login-hint">
            Inloggen eigenaar: <strong>eigenaar@kniplokettiko.nl</strong> / wachtwoord: <strong>password</strong>
        </p>
    </div>
</div>
@endsection
