@extends('layouts.app')
@section('page-title', 'Prescription Details')

@section('sidebar-menu')
    <li><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('patient.appointments') }}">Appointments</a></li>
    <li><a href="{{ route('patient.medical-records') }}">Medical Records</a></li>
    <li><a href="{{ route('patient.prescriptions') }}" class="active">Prescriptions</a></li>
    <li><a href="{{ route('patient.payments') }}">Payments</a></li>
    <li><a href="{{ route('patient.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Prescription Details</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label text-muted">Medication</label>
                <p class="mb-0"><strong>Paracetamol 500mg</strong></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Doctor</label>
                <p class="mb-0">Dr. Ahmad</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Dosage</label>
                <p class="mb-0">500mg</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Frequency</label>
                <p class="mb-0">Twice daily</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Duration</label>
                <p class="mb-0">7 days</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Status</label>
                <p class="mb-0"><span class="badge bg-success">Active</span></p>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('patient.prescriptions') }}" class="btn btn-outline-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
