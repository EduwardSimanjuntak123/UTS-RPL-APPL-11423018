@extends('layouts.app')

@section('page-title', 'My Prescriptions')

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
        <a href="{{ route('patient.prescriptions') }}" class="active">
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
        <a href="{{ route('patient.profile') }}">
            <i class="bi bi-person nav-icon"></i>
            Profile
        </a>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-capsule"></i> My Prescriptions
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                    <i class="bi bi-check-circle"></i> Active
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                    <i class="bi bi-clock-history"></i> History
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="bi bi-hourglass-split"></i> Pharmacy Orders
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Active Prescriptions -->
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                @if(isset($activePrescriptions) && $activePrescriptions->count() > 0)
                    <div class="row">
                        @foreach($activePrescriptions as $prescription)
                            <div class="col-md-6 mb-3">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <strong>{{ $prescription->medication }}</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Dosage</small><br>
                                                <strong>{{ $prescription->dosage }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Frequency</small><br>
                                                <strong>{{ $prescription->frequency }}</strong>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Duration</small><br>
                                                <strong>{{ $prescription->duration }} days</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Prescribed By</small><br>
                                                <strong>{{ $prescription->doctor->name ?? 'N/A' }}</strong>
                                            </div>
                                        </div>
                                        @if($prescription->notes)
                                            <div class="mb-2">
                                                <small class="text-muted">Notes</small><br>
                                                <small>{{ $prescription->notes }}</small>
                                            </div>
                                        @endif
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                Issued: {{ $prescription->created_at->format('d M Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $prescription->id }}">
                                            <i class="bi bi-bag"></i> Order from Pharmacy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No active prescriptions.
                    </div>
                @endif
            </div>

            <!-- Prescription History -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                @if(isset($historyPrescriptions) && $historyPrescriptions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historyPrescriptions as $prescription)
                                    <tr>
                                        <td><strong>{{ $prescription->medication }}</strong></td>
                                        <td>{{ $prescription->dosage }}</td>
                                        <td>{{ $prescription->frequency }}</td>
                                        <td>{{ $prescription->doctor->name ?? 'N/A' }}</td>
                                        <td>{{ $prescription->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($prescription->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($prescription->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($prescription->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No prescription history.
                    </div>
                @endif
            </div>

            <!-- Pharmacy Orders -->
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                @if(isset($prescriptionOrders) && $prescriptionOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Pharmacy</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Date Ordered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescriptionOrders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->prescription->medication ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            {{ $order->pharmacy->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $order->pharmacy->address ?? '' }}</small>
                                        </td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                        <td>
                                            @if($order->status === 'ready')
                                                <span class="badge bg-success">Ready for Pickup</span>
                                            @elseif($order->status === 'completed')
                                                <span class="badge bg-primary">Completed</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#orderDetailModal{{ $order->id }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No pharmacy orders yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Order from Pharmacy Modal -->
@if(isset($activePrescriptions))
    @foreach($activePrescriptions as $prescription)
        <div class="modal fade" id="orderModal{{ $prescription->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-bag"></i> Order {{ $prescription->medication }} from Pharmacy
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('patient.prescriptions.order', $prescription) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="pharmacy{{ $prescription->id }}" class="form-label">Select Pharmacy *</label>
                                <select class="form-select" id="pharmacy{{ $prescription->id }}" name="pharmacy_id" required>
                                    <option value="">-- Choose Pharmacy --</option>
                                    @if(isset($pharmacies))
                                        @foreach($pharmacies as $pharmacy)
                                            <option value="{{ $pharmacy->id }}">
                                                {{ $pharmacy->name }} - {{ $pharmacy->address }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quantity{{ $prescription->id }}" class="form-label">Quantity *</label>
                                        <input type="number" class="form-control" id="quantity{{ $prescription->id }}" name="quantity" value="1" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Medication</label>
                                        <input type="text" class="form-control" value="{{ $prescription->medication }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Confirm Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Order Detail Modal -->
@if(isset($prescriptionOrders))
    @foreach($prescriptionOrders as $order)
        <div class="modal fade" id="orderDetailModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Medication</label>
                            <input type="text" class="form-control" value="{{ $order->prescription->medication ?? 'N/A' }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pharmacy</label>
                            <input type="text" class="form-control" value="{{ $order->pharmacy->name ?? 'N/A' }}" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="text" class="form-control" value="{{ $order->quantity }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" value="Rp {{ number_format($order->price, 0, ',', '.') }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" disabled>
                        </div>
                        <hr>
                        <small class="text-muted">
                            Ordered on: {{ $order->created_at->format('d M Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
