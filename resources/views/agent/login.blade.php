@extends('layouts.app')

@section('title', 'Agent Login - CapBay Auto')

@section('content')
<div style="max-width: 420px; margin: 4rem auto;">
    <div class="glass-card glow">
        <div class="glass-card-header" style="margin-bottom: 1.5rem;">
            <h2 class="glass-card-title">Agent Login</h2>
            <span class="badge badge-registered">Staff Only</span>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
                <svg style="width: 1.5rem; height: 1.5rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('agent.login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="agent@capbay.com"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-checkbox">
                    <input type="checkbox" name="remember" value="0">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Log In
            </button>
        </form>

        <p style="margin-top: 1.25rem; font-size: 0.8rem; color: var(--text-muted); text-align: center;">
            Access restricted to authorised CapBay Auto agents only.
        </p>
    </div>
</div>
@endsection
