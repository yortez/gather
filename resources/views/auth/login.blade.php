@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <section class="auth-layout">
        <div class="auth-aside">
            <span class="eyebrow"><span class="eyebrow-dot"></span> Welcome back</span>
            <h1>Your next<br><em>good question</em><br>starts here.</h1>
            <p>Pick up where you left off and see what people have shared.</p>
            <div class="auth-aside-mark" aria-hidden="true">?</div>
        </div>
        <div class="auth-panel">
            <div class="panel-heading"><span class="panel-kicker">YOUR WORKSPACE</span><h2>Sign in</h2><p>Enter the details for your Gather account.</p></div>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <form class="stack-form" method="POST" action="{{ route('login') }}">
                @csrf
                <label class="field-label">Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
                <label class="field-label">Password<input type="password" name="password" autocomplete="current-password" required></label>
                <label class="check-label"><input type="checkbox" name="remember" value="1"><span>Keep me signed in</span></label>
                <button class="button button-dark button-wide" type="submit">Sign in <span aria-hidden="true">→</span></button>
            </form>
            <p class="auth-switch">New around here? <a href="{{ route('register') }}">Create an account</a></p>
        </div>
    </section>
@endsection