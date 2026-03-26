@extends('layouts.app')
@section('page-title', 'Prescription Order Details')

@section('sidebar-menu')
    <li><a href="{{ route('pharmacist.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('pharmacist.inventory') }}">Inventory</a></li>
    <li><a href="{{ route('pharmacist.orders') }}" class="active">Orders</a></li>
    <li><a href="{{ route('pharmacist.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('pharmacist.orders') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chevron-left"></i> Back
                    </a>
                    <h2>Order Details</h2>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#fulfillModal">
                        <i class="bi bi-check-circle"></i> Mark as Fulfilled
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Order ID</small>
                            <h6>#ORD-001</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Date Created</small>
                            <h6>Jan 15, 2025</h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Patient</small>
                            <h6>Patient Name</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Doctor</small>
                            <h6>Dr. John Doe</h6>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Medications</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Medication</th>
                                <th>Quantity</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ibuprofen</td>
                                <td>10</td>
                                <td>400mg</td>
                                <td>3x daily</td>
                            </tr>
                            <tr>
                                <td>Paracetamol</td>
                                <td>20</td>
                                <td>500mg</td>
                                <td>2x daily</td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Status</small>
                            <div><span class="badge bg-warning">Pending</span></div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Total Items</small>
                            <h6>30 items</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#fulfillModal">
                            <i class="bi bi-check-circle"></i> Fulfill Order
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle"></i> Cancel Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fulfill Modal -->
<div class="modal fade" id="fulfillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fulfill Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Mark this order as fulfilled?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('pharmacist.orders.update', 1) }}" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="fulfilled">
                    <button type="submit" class="btn btn-success">Fulfill</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Order</button>
                <form method="POST" action="{{ route('pharmacist.orders.update', 1) }}" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
