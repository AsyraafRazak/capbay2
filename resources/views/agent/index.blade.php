@extends('layouts.app')

@section('Agent Dashboard - CapBay Auto')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Agent Dashboard</h1>
        <p style="color: var(--text-secondary); margin-top: 0.25rem;">Manage and look up customer test drive registrations, eligibility, and transitions.</p>
    </div>
    <div>
        <span class="badge badge-test_drive_completed" style="padding: 0.5rem 1rem;">Total Records: {{ number_format(\App\Models\Registration::count()) }}</span>
    </div>
</div>

<div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
    <form action="{{ route('agent.index') }}" method="GET" class="filters-bar">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="search" class="form-label">Search Customer</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   value="{{ request('search') }}" 
                   class="form-control" 
                   placeholder="Search name, email, phone or CB-ID...">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-control form-select">
                <option value="">All Statuses</option>
                <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registered</option>
                <option value="test_drive_scheduled" {{ request('status') === 'test_drive_scheduled' ? 'selected' : '' }}>Test Drive Scheduled</option>
                <option value="test_drive_completed" {{ request('status') === 'test_drive_completed' ? 'selected' : '' }}>Test Drive Completed</option>
                <option value="purchased" {{ request('status') === 'purchased' ? 'selected' : '' }}>Purchased</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="car_model" class="form-label">Car Model</label>
            <select id="car_model" name="car_model" class="form-control form-select">
                <option value="">All Models</option>
                <option value="CapBay Vroom" {{ request('car_model') === 'CapBay Vroom' ? 'selected' : '' }}>CapBay Vroom</option>
                <option value="CapBay Lite" {{ request('car_model') === 'CapBay Lite' ? 'selected' : '' }}>CapBay Lite</option>
                <option value="CapBay Sport" {{ request('car_model') === 'CapBay Sport' ? 'selected' : '' }}>CapBay Sport</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="height: 42px;">
                Filter
            </button>
            <a href="{{ route('agent.index') }}" class="btn btn-secondary" style="height: 42px; display: inline-flex; align-items: center;">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="glass-card" style="padding: 0;">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Registration ID</th>
                    <th>Customer Name</th>
                    <th>Email Address</th>
                    <th>Phone</th>
                    <th>Car Model</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $registration)
                    <tr>
                        <td style="font-weight: 700; color: #60a5fa;">CB-{{ $registration->id }}</td>
                        <td style="font-weight: 600;">{{ $registration->customer_name }}</td>
                        <td>{{ $registration->customer_email }}</td>
                        <td>{{ $registration->customer_phone }}</td>
                        <td>{{ $registration->car_model }}</td>
                        <td>
                            <span class="badge badge-{{ $registration->status }}">
                                {{ str_replace('_', ' ', $registration->status) }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted);">{{ $registration->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('agent.show', $registration->id) }}" class="btn btn-secondary btn-sm">
                                View / Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                            No registrations found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $registrations->links() }}
</div>
@endsection
