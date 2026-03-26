@extends('layouts.app')

@section('page-title', 'Pharmacist Dashboard')

@section('sidebar-menu')
    <li>
        <a href="{{ route('pharmacist.dashboard') }}" class="active">
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
        <a href="{{ route('pharmacist.profile') }}">
            <i class="bi bi-person nav-icon"></i>
            Profile
        </a>
    </li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-bag"></i>
            </div>
            <div class="stat-number">{{ $pendingOrders ?? 0 }}</div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-number">{{ $expiringItems ?? 0 }}</div>
            <div class="stat-label">Expiring Soon</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="stat-number">{{ $lowStockItems ?? 0 }}</div>
            <div class="stat-label">Low Stock</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-boxes"></i>
            </div>
            <div class="stat-number">{{ $totalItems ?? 0 }}</div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bag"></i> Pending Prescription Orders
                <a href="{{ route('pharmacist.orders') }}" class="btn btn-sm btn-primary float-end">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if(isset($recentOrders) && $recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Quantity</th>
                                    <th>Patient</th>
                                    <th>Date Ordered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->prescription->medication ?? 'N/A' }}</strong>
                                        </td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ $order->prescription->patient->name ?? 'N/A' }}</td>
                                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#fulfillModal{{ $order->id }}">
                                                <i class="bi bi-check"></i> Fulfill
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No pending orders.
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-exclamation-triangle"></i> Expiring Items Alert
            </div>
            <div class="card-body">
                @if(isset($expiringDrugs) && $expiringDrugs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-danger">
                            <thead>
                                <tr>
                                    <th>Drug Name</th>
                                    <th>Batch</th>
                                    <th>Expiry Date</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expiringDrugs as $drug)
                                    <tr>
                                        <td><strong>{{ $drug->drug_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $drug->batch_number }}</td>
                                        <td>
                                            <strong class="text-danger">
                                                {{ \Carbon\Carbon::parse($drug->expiry_date)->format('d M Y') }}
                                            </strong>
                                        </td>
                                        <td>{{ $drug->quantity }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#removeModal{{ $drug->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No items expiring in the next 30 days.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Pharmacy Info
            </div>
            <div class="card-body">
                <p>
                    <strong>Location:</strong><br>
                    <span class="text-muted">{{ $pharmacyInfo->address ?? auth()->user()->pharmacy->address ?? 'N/A' }}</span>
                </p>
                <p>
                    <strong>License:</strong><br>
                    <span class="text-muted">{{ $pharmacyInfo->license_number ?? auth()->user()->pharmacy->license_number ?? 'N/A' }}</span>
                </p>
                <p>
                    <strong>Phone:</strong><br>
                    <span class="text-muted">{{ $pharmacyInfo->phone ?? auth()->user()->pharmacy->phone ?? 'N/A' }}</span>
                </p>
                <hr>
                <a href="{{ route('pharmacist.profile') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-pencil"></i> Edit Pharmacy Info
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> Quick Stats
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span>Orders Fulfilled Today</span><br>
                    <strong>{{ $ordersToday ?? 0 }}</strong>
                </div>
                <div class="mb-2">
                    <span>Inventory Value</span><br>
                    <strong>Rp {{ number_format($inventoryValue ?? 0, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <span>Active Drugs</span><br>
                    <strong>{{ $activeDrugs ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fulfill Order Modals -->
@if(isset($recentOrders))
    @foreach($recentOrders as $order)
        <div class="modal fade" id="fulfillModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Fulfill Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('pharmacist.orders.fulfill', $order) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Medication</label>
                                <input type="text" class="form-control" value="{{ $order->prescription->medication ?? 'N/A' }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="text" class="form-control" value="{{ $order->quantity }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="notes{{ $order->id }}" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes{{ $order->id }}" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Mark as Ready
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
