@extends('layouts.app')
@section('page-title', 'Prescription Details')

@section('sidebar-menu')
    <li><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('doctor.appointments') }}">Appointments</a></li>
    <li><a href="{{ route('doctor.patients') }}">Patients</a></li>
    <li><a href="{{ route('doctor.medical-records') }}">Medical Records</a></li>
    <li><a href="{{ route('doctor.prescriptions') }}" class="active">Prescriptions</a></li>
    <li><a href="{{ route('doctor.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('doctor.prescriptions') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Prescription Details</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Medication Information</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Medication</small>
                            <h6>Ibuprofen 400mg</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Patient</small>
                            <h6>Patient Name</h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Dosage</small>
                            <h6>1 tablet</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Frequency</small>
                            <h6>3 times daily</h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Duration</small>
                            <h6>7 days</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Status</small>
                            <div><span class="badge bg-success">Active</span></div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Instructions</small>
                        <p>Take with food to avoid stomach upset. Do not exceed 2400mg per day.</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Date Prescribed</small>
                        <h6>Jan 10, 2025</h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Start Date</small>
                        <h6>Jan 10, 2025</h6>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">End Date</small>
                        <h6>Jan 17, 2025</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <a href="{{ route('doctor.prescriptions') }}" class="btn btn-sm btn-outline-primary">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
