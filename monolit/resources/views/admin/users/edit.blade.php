@extends('layouts.app')
@section('page-title', 'Edit User')

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
                <h2>Edit User</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user ?? 1) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="pharmacist">Pharmacist</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password (Kataksandi) - Leave blank to keep current password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password (Konfirmasi Kataksandi)</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
                    @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">Update User</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
