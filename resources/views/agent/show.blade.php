@extends('layouts.app')

@section('title','CapBay Auto - Customer Look-up')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('agent.index') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.25rem;">
        &larr; Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <svg style="width: 1.5rem; height: 1.5rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <svg style="width: 1.5rem; height: 1.5rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <strong>Error:</strong> {{ session('error') }}
        </div>
    </div>
@endif

<!-- State Flow Visualizer -->
<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
    <div class="glass-card-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h3 style="font-size: 1.15rem; color: var(--text-secondary);">State Transition Flow</h3>
        <span class="badge badge-{{ $registration->status }}">{{ str_replace('_', ' ', $registration->status) }}</span>
    </div>
    
    <div class="state-flow">
        <!-- Node 1 -->
        <div class="state-node {{ $registration->status === 'registered' ? 'active' : ($registration->status !== 'cancelled' ? 'completed' : '') }}">
            <div class="state-indicator">1</div>
            <div class="state-label">Registered</div>
        </div>
        <!-- Node 2 -->
        <div class="state-node {{ $registration->status === 'test_drive_scheduled' ? 'active' : (in_array($registration->status, ['test_drive_completed', 'purchased']) ? 'completed' : '') }}">
            <div class="state-indicator">2</div>
            <div class="state-label">Scheduled</div>
        </div>
        <!-- Node 3 -->
        <div class="state-node {{ $registration->status === 'test_drive_completed' ? 'active' : ($registration->status === 'purchased' ? 'completed' : '') }}">
            <div class="state-indicator">3</div>
            <div class="state-label">Completed</div>
        </div>
        <!-- Node 4 -->
        <div class="state-node {{ $registration->status === 'purchased' ? 'active' : '' }} {{ $registration->status === 'purchased' ? 'completed' : '' }}">
            <div class="state-indicator">✓</div>
            <div class="state-label">Purchased</div>
        </div>

        @if($registration->status === 'cancelled')
            <div class="state-node active cancelled-state">
                <div class="state-indicator">✗</div>
                <div class="state-label" style="color: #f43f5e;">Cancelled</div>
            </div>
        @endif
    </div>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Edit Form and Transitions -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Customer Info Card -->
        <div class="glass-card">
            <div class="glass-card-header">
                <h3 class="glass-card-title">Registration Detail</h3>
                <span style="font-size: 0.85rem; color: var(--text-muted);">CB-{{ $registration->id }}</span>
            </div>

            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">Customer Name</span>
                    <span class="info-value">{{ $registration->customer_name }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">{{ $registration->customer_email }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value">{{ $registration->customer_phone }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Car Model</span>
                    <span class="info-value">{{ $registration->car_model }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Registration Date</span>
                    <span class="info-value">{{ $registration->created_at->format('Y-m-d H:i:s') }}</span>
                </li>
            </ul>
        </div>

        <!-- Update details Form -->
        <div class="glass-card">
            <div class="glass-card-header">
                <h3 class="glass-card-title">Update Down Payment & Loan</h3>
            </div>

            <form action="{{ route('agent.update', $registration->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="down_payment_paid" class="form-label">Down Payment Paid (RM)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           id="down_payment_paid" 
                           name="down_payment_paid" 
                           value="{{ old('down_payment_paid', $registration->down_payment_paid_cents / 100) }}" 
                           class="form-control @error('down_payment_paid') is-invalid @enderror" 
                           required>
                    <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">
                        Standard down payment required: <strong>RM 20,000.00</strong> (10% of RM 200,000.00).<br>
                        Min. required for promotion: <strong>RM 2,000.00</strong> (10% of standard down payment).
                    </span>
                    @error('down_payment_paid')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-checkbox">
                        <input type="checkbox" name="loan_approved" value="1" {{ old('loan_approved', $registration->loan_approved) ? 'checked' : '' }}>
                        <span>Customer's loan is approved and signed</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Save Financial Data
                </button>
            </form>
        </div>

        <!-- Action / Transitions Card -->
        <div class="glass-card">
            <div class="glass-card-header">
                <h3 class="glass-card-title">State Operations</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @if($registration->status === 'registered')
                    <div style="display: flex; gap: 1rem;">
                        <form action="{{ route('agent.transition', $registration->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="test_drive_scheduled">
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                Schedule Test Drive
                            </button>
                        </form>
                        <form action="{{ route('agent.transition', $registration->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                Cancel Registration
                            </button>
                        </form>
                    </div>
                @elseif($registration->status === 'test_drive_scheduled')
                    <div style="display: flex; gap: 1rem;">
                        <form action="{{ route('agent.transition', $registration->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="test_drive_completed">
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                Complete Test Drive
                            </button>
                        </form>
                        <form action="{{ route('agent.transition', $registration->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                Cancel Registration
                            </button>
                        </form>
                    </div>
                @elseif($registration->status === 'test_drive_completed')
                    <div style="display: flex; gap: 1rem;">
                        <form action="{{ route('agent.transition', $registration->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="purchased">
                            <button type="submit" class="btn btn-success" style="width: 100%;" {{ !$registration->loan_approved ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>
                                Complete Purchase
                            </button>
                        </form>
                        <form action="{{ route('agent.transition', $registration->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                Cancel Registration
                            </button>
                        </form>
                    </div>
                    @if(!$registration->loan_approved)
                        <span style="font-size: 0.8rem; color: #fb7185; text-align: center; margin-top: 0.25rem;">
                            * Purchase completion is disabled until the customer's loan has been marked as approved.
                        </span>
                    @endif
                @elseif(in_array($registration->status, ['purchased', 'cancelled']))
                    <div style="text-align: center; padding: 1rem; background: rgba(255,255,255,0.03); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">
                            No further actions. State <strong>'{{ ucfirst($registration->status) }}'</strong> is a terminal state.
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Right Column: Promotion & Calculation Summary -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Promotion Status Card -->
        <div class="glass-card glow" style="border-color: rgba(59, 130, 246, 0.3);">
            <div class="glass-card-header">
                <h3 class="glass-card-title" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Promo Eligibility</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                @if($registration->car_model !== 'CapBay Vroom')
                    <div style="background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.2); padding: 1rem; border-radius: var(--radius-md); text-align: center;">
                        <span style="color: #fb7185; font-weight: 700; font-size: 0.95rem;">Not Eligible</span>
                        <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.25rem;">Promotion only applies to the 'CapBay Vroom' model.</p>
                    </div>
                @else
                    <div style="background: {{ $registration->meets_promotion_conditions ? 'rgba(16,185,129,0.08)' : 'rgba(245,158,11,0.08)' }}; border: 1px solid {{ $registration->meets_promotion_conditions ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }}; padding: 1rem; border-radius: var(--radius-md); text-align: center;">
                        <span style="color: {{ $registration->meets_promotion_conditions ? '#34d399' : '#fbbf24' }}; font-weight: 700; font-size: 0.95rem;">
                            {{ $registration->meets_promotion_conditions ? '15% Promo Applied!' : 'Promo Pending Requirements' }}
                        </span>
                        <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.25rem;">
                            {{ $registration->meets_promotion_conditions ? 'All conditions met. Final price reduced by 15%.' : 'One or more promotion requirements are missing.' }}
                        </p>
                    </div>
                @endif

                <div>
                    <h4 style="font-size: 0.9rem; margin-bottom: 0.75rem; color: var(--text-primary);">Checklist:</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.875rem;">
                        
                        <!-- Rule 1: First 10 Active -->
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="color: var(--text-secondary);">1. In Top 10 Active Queue</span>
                            @if($registration->is_eligible)
                                <span style="color: #34d399; font-weight: 600;">Yes (✓)</span>
                            @else
                                <span style="color: #f87171; font-weight: 600;">No (✗)</span>
                            @endif
                        </li>
                        
                        <!-- Rule 2: Minimum Down Payment -->
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="color: var(--text-secondary);">2. Min. DP Paid (&ge; RM 2,000)</span>
                            @if($registration->down_payment_paid_cents >= 200000)
                                <span style="color: #34d399; font-weight: 600;">Yes (✓)</span>
                            @else
                                <span style="color: #f87171; font-weight: 600;">No (✗) - Paid RM {{ number_format($registration->down_payment_paid_cents/100, 2) }}</span>
                            @endif
                        </li>
                        
                        <!-- Rule 3: Loan Approved -->
                        <li style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="color: var(--text-secondary);">3. Loan Approved</span>
                            @if($registration->loan_approved)
                                <span style="color: #34d399; font-weight: 600;">Yes (✓)</span>
                            @else
                                <span style="color: #f87171; font-weight: 600;">No (✗)</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Loan Calculator Card -->
        <div class="glass-card">
            <div class="glass-card-header">
                <h3 class="glass-card-title">Loan Calculator</h3>
            </div>

            <ul class="info-list" style="font-size: 0.95rem;">
                <li class="info-item">
                    <span class="info-label">Base Price</span>
                    <span class="info-value">{{ $registration->formatted_price }}</span>
                </li>
                
                @if($registration->meets_promotion_conditions)
                    <li class="info-item" style="color: #34d399;">
                        <span class="info-label" style="color: #34d399;">Promo Discount (15%)</span>
                        <span class="info-value" style="color: #34d399;">- RM {{ number_format(($registration->price_cents * 0.15) / 100, 2) }}</span>
                    </li>
                @else
                    <li class="info-item">
                        <span class="info-label">Promo Discount (15%)</span>
                        <span class="info-value" style="color: var(--text-muted);">RM 0.00</span>
                    </li>
                @endif

                <li class="info-item" style="background: rgba(255,255,255,0.02); border-top: 1px solid var(--border-color); padding: 1rem 0;">
                    <span class="info-label" style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Final Price</span>
                    <span class="info-value" style="font-size: 1.05rem; font-weight: 700; color: #60a5fa;">{{ $registration->formatted_final_price }}</span>
                </li>

                <li class="info-item">
                    <span class="info-label">Down Payment Paid</span>
                    <span class="info-value" style="color: #34d399;">- {{ $registration->formatted_down_payment_paid }}</span>
                </li>

                <li class="info-item" style="background: rgba(59,130,246,0.05); border-top: 2px solid #3b82f6; padding: 1rem 0;">
                    <span class="info-label" style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">Total Loan Amount</span>
                    <span class="info-value" style="font-size: 1.15rem; font-weight: 800; color: #a78bfa;">{{ $registration->formatted_loan_amount }}</span>
                </li>
            </ul>
        </div>

    </div>
</div>
@endsection
