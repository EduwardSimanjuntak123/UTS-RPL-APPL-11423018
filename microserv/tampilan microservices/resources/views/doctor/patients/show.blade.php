@extends('layouts.app')
@section('page-title', 'Patient Details')

@section('sidebar-menu')
    <li><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('doctor.appointments') }}">Appointments</a></li>
    <li><a href="{{ route('doctor.patients') }}" class="active">Patients</a></li>
    <li><a href="{{ route('doctor.medical-records') }}">Medical Records</a></li>
    <li><a href="{{ route('doctor.prescriptions') }}">Prescriptions</a></li>
    <li><a href="{{ route('doctor.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('doctor.patients') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Patient Details</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label text-muted">Name</label>
                <p class="mb-0"><strong>Patient Name</strong></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Email</label>
                <p class="mb-0">patient@meditrack.com</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Phone</label>
                <p class="mb-0">08123456789</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Address</label>
                <p class="mb-0">Jakarta, Indonesia</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Insurance Provider</label>
                <p class="mb-0">BPJS</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Total Appointments</label>
                <p class="mb-0">5</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Last Visit</label>
                <p class="mb-0">2026-03-20</p>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#appointmentModal">
                    <i class="bi bi-calendar-plus"></i> Schedule Appointment
                </button>
                <a href="{{ route('doctor.patients') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Schedule appointment functionality here</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Schedule</button>
            </div>
        </div>
    </div>
</div>
@endsection
