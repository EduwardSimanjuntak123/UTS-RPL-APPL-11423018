@extends('layouts.app')
@section('page-title', 'Medical Record Details')

@section('sidebar-menu')
    <li><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('patient.appointments') }}">Appointments</a></li>
    <li><a href="{{ route('patient.medical-records') }}" class="active">Medical Records</a></li>
    <li><a href="{{ route('patient.prescriptions') }}">Prescriptions</a></li>
    <li><a href="{{ route('patient.payments') }}">Payments</a></li>
    <li><a href="{{ route('patient.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('patient.medical-records') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Medical Record</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label text-muted">Doctor</label>
                <p class="mb-0"><strong>Doctor Name</strong></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Date</label>
                <p class="mb-0">2026-03-25</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Diagnosis</label>
                <p class="mb-0">Diagnosis information here</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Treatment</label>
                <p class="mb-0">Treatment details here</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Medications</label>
                <p class="mb-0">Medication list here</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Lab Results</label>
                <p class="mb-0">Lab findings here</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Follow-up Date</label>
                <p class="mb-0">2026-04-25</p>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="{{ route('patient.medical-records') }}" class="btn btn-outline-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
