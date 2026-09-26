@extends('layouts.app')

@section('title', 'Create your account')

@section('content')
    <section class="auth-layout">
        <div class="auth-aside">
            <span class="eyebrow"><span class="eyebrow-dot"></span> A fresh start</span>
            <h1>Make room<br>for <em>good answers.</em></h1>
            <p>Your forms, your responses, all together in one tidy workspace.</p>
            <div class="auth-aside-mark auth-mark-star" aria-hidden="true">✳</div>
        </div>
        <div class="auth-panel">
            <div class="panel-heading"><span class="panel-kicker">GET STARTED</span><h2>Create account</h2><p>Set up your workspace in a moment.</p></div>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <form class="stack-form" method="POST" action="{{ route('register') }}">
                @csrf
                <label class="field-label">Your name<input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus></label>
                <label class="field-label">Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
                <label class="field-label">Password<input type="password" name="password" autocomplete="new-password" minlength="8" required><span class="field-hint">Use at least 8 characters.</span></label>
                <label class="field-label">Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                <button class="button button-dark button-wide" type="submit">Create account <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
@endsection