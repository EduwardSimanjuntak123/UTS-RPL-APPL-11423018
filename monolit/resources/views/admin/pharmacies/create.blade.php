@extends('layouts.app')
@section('page-title', 'Add New Pharmacy')

@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('admin.users') }}">Users</a></li>
    <li><a href="{{ route('admin.analytics') }}">Analytics</a></li>
    <li><a href="{{ route('admin.pharmacies') }}" class="active">Pharmacies</a></li>
    <li><a href="{{ route('admin.settings') }}">Settings</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.pharmacies') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Add New Pharmacy</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.pharmacies.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Pharmacy Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="mb-3">
                    <label for="license_number" class="form-label">License Number</label>
                    <input type="text" class="form-control" id="license_number" name="license_number" required>
                </div>

                <div class="mb-3">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="number" class="form-control" id="latitude" name="latitude" step="0.000001">
                </div>

                <div class="mb-3">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="number" class="form-control" id="longitude" name="longitude" step="0.000001">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Pharmacy</button>
                    <a href="{{ route('admin.pharmacies') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
