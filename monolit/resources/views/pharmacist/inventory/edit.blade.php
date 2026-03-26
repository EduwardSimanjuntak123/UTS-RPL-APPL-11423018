@extends('layouts.app')
@section('page-title', 'Edit Drug Stock')

@section('sidebar-menu')
    <li><a href="{{ route('pharmacist.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('pharmacist.inventory') }}" class="active">Inventory</a></li>
    <li><a href="{{ route('pharmacist.orders') }}">Orders</a></li>
    <li><a href="{{ route('pharmacist.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('pharmacist.inventory') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Edit Drug Stock</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pharmacist.inventory.update', 1) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="drug_name" class="form-label">Drug Name</label>
                    <input type="text" class="form-control" id="drug_name" value="Paracetamol 500mg" disabled>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Unit Price</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                </div>

                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                </div>

                <div class="mb-3">
                    <label for="reorder_level" class="form-label">Reorder Level</label>
                    <input type="number" class="form-control" id="reorder_level" name="reorder_level" min="0" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">Update Stock</button>
                    <a href="{{ route('pharmacist.inventory') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
