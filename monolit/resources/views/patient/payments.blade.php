@extends('layouts.app')

@section('page-title', 'My Payments')

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
        <a href="{{ route('patient.payments') }}" class="active">
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
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-wallet"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Total Paid</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($totalPending ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-number">{{ $successfulTransactions ?? 0 }}</div>
            <div class="stat-label">Successful</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-number">{{ $failedTransactions ?? 0 }}</div>
            <div class="stat-label">Failed</div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-credit-card"></i> Payment History
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                    All
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="success-tab" data-bs-toggle="tab" data-bs-target="#success" type="button" role="tab">
                    <i class="bi bi-check-circle"></i> Success
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="bi bi-hourglass-split"></i> Pending
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="failed-tab" data-bs-toggle="tab" data-bs-target="#failed" type="button" role="tab">
                    <i class="bi bi-x-circle"></i> Failed
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                @include('patient.partials.payment-table', ['payments' => $allPayments ?? []])
            </div>
            <div class="tab-pane fade" id="success" role="tabpanel" aria-labelledby="success-tab">
                @include('patient.partials.payment-table', ['payments' => $successPayments ?? []])
            </div>
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                @include('patient.partials.payment-table', ['payments' => $pendingPayments ?? []])
            </div>
            <div class="tab-pane fade" id="failed" role="tabpanel" aria-labelledby="failed-tab">
                @include('patient.partials.payment-table', ['payments' => $failedPayments ?? []])
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-shield-check"></i> Insurance Claims
    </div>
    <div class="card-body">
        @if(isset($insuranceClaims) && $insuranceClaims->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($insuranceClaims as $claim)
                            <tr>
                                <td>
                                    <strong>#{{ $claim->id }}</strong>
                                </td>
                                <td>Rp {{ number_format($claim->claim_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($claim->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($claim->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($claim->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $claim->created_at->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#claimModal{{ $claim->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No insurance claims found.
            </div>
        @endif
    </div>
</div>

<!-- Insurance Claim Detail Modal -->
@if(isset($insuranceClaims))
    @foreach($insuranceClaims as $claim)
        <div class="modal fade" id="claimModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Claim Details - #{{ $claim->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Claim Amount</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($claim->claim_amount, 0, ',', '.') }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($claim->status) }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Approved Amount</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($claim->approved_amount ?? 0, 0, ',', '.') }}" disabled>
                        </div>
                        @if($claim->notes)
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="3" disabled>{{ $claim->notes }}</textarea>
                            </div>
                        @endif
                        <small class="text-muted">
                            Submitted: {{ $claim->created_at->format('d M Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
