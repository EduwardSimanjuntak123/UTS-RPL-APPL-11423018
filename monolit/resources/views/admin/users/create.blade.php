@extends('layouts.app')
@section('page-title', 'Add New User')

@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('admin.users') }}" class="active">Users</a></li>
    <li><a href="{{ route('admin.analytics') }}">Analytics</a></li>
    <li><a href="{{ route('admin.pharmacies') }}">Pharmacies</a></li>
    <li><a href="{{ route('admin.settings') }}">Settings</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Add New User</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password (Kataksandi)</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password (Konfirmasi Kataksandi)</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                    @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="pharmacist">Pharmacist</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
