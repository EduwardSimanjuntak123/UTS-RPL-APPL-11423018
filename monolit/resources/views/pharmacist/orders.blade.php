@extends('layouts.app')

@section('page-title', 'Prescription Orders')

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
        <a href="{{ route('pharmacist.orders') }}" class="active">
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
<div class="card">
    <div class="card-header">
        <i class="bi bi-bag"></i> Prescription Orders
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="bi bi-hourglass-split"></i> Pending
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="ready-tab" data-bs-toggle="tab" data-bs-target="#ready" type="button" role="tab">
                    <i class="bi bi-check-circle"></i> Ready for Pickup
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                    <i class="bi bi-check-lg"></i> Completed
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Pending Orders -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                @if(isset($pendingOrders) && $pendingOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Date</th>
                                    <th>Patient</th>
                                    <th>Medication</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $order)
                                    <tr>
                                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $order->prescription->patient->name ?? 'N/A' }}</td>
                                        <td><strong>{{ $order->prescription->medication ?? 'N/A' }}</strong></td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#readyModal{{ $order->id }}">
                                                <i class="bi bi-check"></i> Mark Ready
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

            <!-- Ready for Pickup -->
            <div class="tab-pane fade" id="ready" role="tabpanel" aria-labelledby="ready-tab">
                @if(isset($readyOrders) && $readyOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Date</th>
                                    <th>Patient</th>
                                    <th>Medication</th>
                                    <th>Quantity</th>
                                    <th>Ready Since</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($readyOrders as $order)
                                    <tr>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>{{ $order->prescription->patient->name ?? 'N/A' }}</td>
                                        <td><strong>{{ $order->prescription->medication ?? 'N/A' }}</strong></td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>
                                            @php
                                                $readyDate = $order->updated_at;
                                                $days = \Carbon\Carbon::now()->diffInDays($readyDate);
                                            @endphp
                                            {{ $days }} day{{ $days > 1 ? 's' : '' }} ago
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#completeModal{{ $order->id }}">
                                                <i class="bi bi-check-lg"></i> Complete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No orders ready for pickup.
                    </div>
                @endif
            </div>

            <!-- Completed -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                @if(isset($completedOrders) && $completedOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Completed Date</th>
                                    <th>Patient</th>
                                    <th>Medication</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedOrders as $order)
                                    <tr>
                                        <td>{{ $order->updated_at->format('d M Y H:i') }}</td>
                                        <td>{{ $order->prescription->patient->name ?? 'N/A' }}</td>
                                        <td>{{ $order->prescription->medication ?? 'N/A' }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No completed orders.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Mark Ready Modal -->
@if(isset($pendingOrders))
    @foreach($pendingOrders as $order)
        <div class="modal fade" id="readyModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Mark Order as Ready</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('pharmacist.orders.update-status', $order) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="status" value="ready">
                            <p>Mark this order as ready for pickup?</p>
                            <div class="mb-3">
                                <label for="notes{{ $order->id }}" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes{{ $order->id }}" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Ready
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Complete Order Modal -->
@if(isset($readyOrders))
    @foreach($readyOrders as $order)
        <div class="modal fade" id="completeModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('pharmacist.orders.update-status', $order) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="status" value="completed">
                            <p>Mark this order as completed?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Complete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
