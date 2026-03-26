@extends('layouts.app')

@section('page-title', 'My Profile')

@section('sidebar-menu')
    <li>
        <a href="{{ route('pharmacist.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('pharmacist.inventory') }}">
            <i class="bi bi-box-seam nav-icon"></i>
            Inventory
        </a>
    </li>
    <li>
        <a href="{{ route('pharmacist.orders') }}">
            <i class="bi bi-bag nav-icon"></i>
            Prescription Orders
        </a>
    </li>
    <li>
        <a href="{{ route('pharmacist.profile') }}" class="active">
            <i class="bi bi-person nav-icon"></i>
            Profile
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div style="font-size: 64px; margin-bottom: 20px;">
                    <i class="bi bi-capsule"></i>
                </div>
                <h4>{{ auth()->user()->name }}</h4>
                <p class="text-muted">Pharmacist</p>
                <div class="mt-4">
                    <p>
                        <strong>Status</strong><br>
                        <span class="badge" style="background-color: {{ auth()->user()->status === 'active' ? 'green' : 'gray' }};">
                            {{ ucfirst(auth()->user()->status) }}
                        </span>
                    </p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pharmacist.profile.edit') }}" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Quick Stats
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Orders Processed</strong><br>
                    <span class="text-muted">{{ $ordersProcessed ?? 0 }}</span>
                </div>
                <div class="mb-3">
                    <strong>Drug Items</strong><br>
                    <span class="text-muted">{{ $drugItems ?? 0 }}</span>
                </div>
                <div class="mb-3">
                    <strong>Inventory Value</strong><br>
                    <span class="text-muted">Rp {{ number_format($inventoryValue ?? 0, 0, ',', '.') }}</span>
                </div>
                <div>
                    <strong>Member Since</strong><br>
                    <span class="text-muted">{{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square"></i> Personal Information
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pharmacist.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control @error('license_number') is-invalid @enderror" id="license_number" name="license_number" value="{{ old('license_number', auth()->user()->license_number) }}">
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-lock"></i> Change Password
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pharmacist.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-arrow-repeat"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
