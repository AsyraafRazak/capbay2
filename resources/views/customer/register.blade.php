@extends('layouts.app')

@section('title', 'CapBay Auto - Register for Test Drive')

@section('content')
<div class="hero-section">
    <h1 class="hero-title">Car Registration</h1>
    <p class="hero-subtitle">Experience our cutting-edge AI cars. Register below to book a premium test drive session with one of our specialized agents.</p>
</div>

<div style="max-width: 600px; margin: 0 auto;">
    @if(session('success'))
        <div class="alert alert-success">
            <svg style="width: 1.5rem; height: 1.5rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong>Registration Completed!</strong>
                <p style="margin-top: 0.25rem; font-size: 0.9rem; line-height: 1.4;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="glass-card glow">
        <div class="glass-card-header">
            <h2 class="glass-card-title">Test Drive Registration</h2>
            <span class="badge badge-glow badge-registered">Promotion Active</span>
        </div>

        <form action="{{ route('customer.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="customer_name" class="form-label">Full Name</label>
                <input type="text" 
                       id="customer_name" 
                       name="customer_name" 
                       value="{{ old('customer_name') }}" 
                       class="form-control @error('customer_name') is-invalid @enderror" 
                       placeholder="e.g. John Doe" 
                       required>
                @error('customer_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="customer_email" class="form-label">Email Address</label>
                <input type="email" 
                       id="customer_email" 
                       name="customer_email" 
                       value="{{ old('customer_email') }}" 
                       class="form-control @error('customer_email') is-invalid @enderror" 
                       placeholder="e.g. john@example.com" 
                       required>
                @error('customer_email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="customer_phone" class="form-label">Phone Number</label>
                <input type="text" 
                       id="customer_phone" 
                       name="customer_phone" 
                       value="{{ old('customer_phone') }}" 
                       class="form-control @error('customer_phone') is-invalid @enderror" 
                       placeholder="e.g. +60123456789" 
                       required>
                @error('customer_phone')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="car_model" class="form-label">Select Car Model</label>
                <select id="car_model" name="car_model" class="form-control form-select @error('car_model') is-invalid @enderror" required>
                    <option value="CapBay Vroom" {{ old('car_model') === 'CapBay Vroom' ? 'selected' : '' }}>CapBay Vroom (RM 200,000.00) - 15% Promotion Available</option>
                    <option value="CapBay Lite" {{ old('car_model') === 'CapBay Lite' ? 'selected' : '' }}>CapBay Lite (RM 120,000.00)</option>
                    <option value="CapBay Sport" {{ old('car_model') === 'CapBay Sport' ? 'selected' : '' }}>CapBay Sport (RM 250,000.00)</option>
                </select>
                @error('car_model')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Submit Registration
            </button>
        </form>
    </div>

    <div style="margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted); text-align: center; line-height: 1.4;">
        <p><strong>Note on 15% discount for CapBay Vroom:</strong> The promotion is only applicable for the first 10 active customers who complete down payment requirements (min 10% of required down payment) and secure loan approval.</p>
    </div>
</div>
@endsection
