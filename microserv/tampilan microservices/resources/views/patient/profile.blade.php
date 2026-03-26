@extends('layouts.app')

@section('page-title', 'My Profile')

@section('sidebar-menu')
    <li>
        <a href="{{ route('patient.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('patient.appointments') }}">
            <i class="bi bi-calendar2-event nav-icon"></i>
            My Appointments
        </a>
    </li>
    <li>
        <a href="{{ route('patient.medical-records') }}">
            <i class="bi bi-file-medical nav-icon"></i>
            Medical Records
        </a>
    </li>
    <li>
        <a href="{{ route('patient.prescriptions') }}">
            <i class="bi bi-capsule nav-icon"></i>
            Prescriptions
        </a>
    </li>
    <li>
        <a href="{{ route('patient.payments') }}">
            <i class="bi bi-credit-card nav-icon"></i>
            Payments
        </a>
    </li>
    <li>
        <a href="{{ route('patient.profile') }}" class="active">
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
                    <i class="bi bi-person-circle"></i>
                </div>
                <h4>{{ auth()->user()->name }}</h4>
                <p class="text-muted">Patient</p>
                <div class="mt-4">
                    <p>
                        <strong>Member Since</strong><br>
                        <span class="text-muted">{{ auth()->user()->created_at->format('d M Y') }}</span>
                    </p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('patient.profile.edit') }}" class="btn btn-primary btn-sm w-100">
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
                    <strong>Total Appointments</strong><br>
                    <span class="text-muted">{{ $totalAppointments ?? 0 }}</span>
                </div>
                <div class="mb-3">
                    <strong>Medical Records</strong><br>
                    <span class="text-muted">{{ $totalRecords ?? 0 }}</span>
                </div>
                <div class="mb-3">
                    <strong>Active Prescriptions</strong><br>
                    <span class="text-muted">{{ $activePrescriptions ?? 0 }}</span>
                </div>
                <div>
                    <strong>Total Spent</strong><br>
                    <span class="text-muted">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</span>
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
                <form method="POST" action="{{ route('patient.profile.update') }}">
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
                            <label for="insurance_provider" class="form-label">Insurance Provider</label>
                            <input type="text" class="form-control @error('insurance_provider') is-invalid @enderror" id="insurance_provider" name="insurance_provider" value="{{ old('insurance_provider', auth()->user()->insurance_provider) }}">
                            @error('insurance_provider')
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

        {{-- Password update form commented out - to be implemented via API
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-lock"></i> Change Password
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('patient.password.update') }}">
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
        --}}

        <div class="card mt-3">
            <div class="card-header text-danger">
                <i class="bi bi-exclamation-triangle"></i> Danger Zone
            </div>
            <div class="card-body">
                <p class="text-muted">Once you delete your account, there is no going back. Please be certain.</p>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash"></i> Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirm Account Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                <p class="text-danger"><strong>All your data will be permanently deleted:</strong></p>
                <ul class="text-danger">
                    <li>Medical records</li>
                    <li>Appointments history</li>
                    <li>Payment information</li>
                    <li>Prescriptions</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('patient.account.delete') }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Yes, Delete My Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
