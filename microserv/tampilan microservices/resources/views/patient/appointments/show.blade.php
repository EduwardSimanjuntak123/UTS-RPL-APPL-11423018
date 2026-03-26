@extends('layouts.app')
@section('page-title', 'Appointment Details')

@section('sidebar-menu')
    <li><a href="{{ route('patient.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('patient.appointments') }}" class="active">Appointments</a></li>
    <li><a href="{{ route('patient.medical-records') }}">Medical Records</a></li>
    <li><a href="{{ route('patient.prescriptions') }}">Prescriptions</a></li>
    <li><a href="{{ route('patient.payments') }}">Payments</a></li>
    <li><a href="{{ route('patient.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Appointment Details</h2>
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
                <label class="form-label text-muted">Date & Time</label>
                <p class="mb-0">2026-03-25 10:00</p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Type</label>
                <p class="mb-0"><span class="badge bg-info">Consultation</span></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Status</label>
                <p class="mb-0"><span class="badge bg-warning">Scheduled</span></p>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Reason</label>
                <p class="mb-0">Appointment reason here</p>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('patient.appointments.edit', 1) }}" class="btn btn-warning">Edit</a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Appointment</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this appointment?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <form method="POST" action="{{ route('patient.appointments.destroy', 1) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Cancel It</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
