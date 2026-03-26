@extends('layouts.app')
@section('page-title', 'Payment Details')

@section('sidebar-menu')
    <li><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('patient.appointments') }}">Appointments</a></li>
    <li><a href="{{ route('patient.medical-records') }}">Medical Records</a></li>
    <li><a href="{{ route('patient.prescriptions') }}">Prescriptions</a></li>
    <li><a href="{{ route('patient.payments') }}" class="active">Payments</a></li>
    <li><a href="{{ route('patient.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Payment Details</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label text-muted">Transaction ID</label>
                <p class="mb-0"><strong>TRX-123456789</strong></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Amount</label>
                <p class="mb-0">Rp 500.000</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Method</label>
                <p class="mb-0">Bank Transfer</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Status</label>
                <p class="mb-0"><span class="badge bg-success">Completed</span></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Date</label>
                <p class="mb-0">2026-03-25</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Appointment</label>
                <p class="mb-0">Consultation with Dr. Ahmad</p>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Receipt
                </button>
                <a href="{{ route('patient.payments') }}" class="btn btn-outline-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
