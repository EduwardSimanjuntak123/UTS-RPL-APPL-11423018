@extends('layouts.app')
@section('page-title', 'Appointment Details')

@section('sidebar-menu')
    <li><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('doctor.appointments') }}" class="active">Appointments</a></li>
    <li><a href="{{ route('doctor.patients') }}">Patients</a></li>
    <li><a href="{{ route('doctor.medical-records') }}">Medical Records</a></li>
    <li><a href="{{ route('doctor.prescriptions') }}">Prescriptions</a></li>
    <li><a href="{{ route('doctor.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('doctor.appointments') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Appointment Details</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label text-muted">Patient</label>
                <p class="mb-0"><strong>Patient Name</strong></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Date & Time</label>
                <p class="mb-0">2026-03-25 10:00</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Type</label>
                <p class="mb-0">Consultation</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Status</label>
                <p class="mb-0"><span class="badge bg-warning">Scheduled</span></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Reason</label>
                <p class="mb-0">Appointment reason</p>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                    <i class="bi bi-check-circle"></i> Mark as Complete
                </button>
                <a href="{{ route('doctor.appointments') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('doctor.appointments.update', 1) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
