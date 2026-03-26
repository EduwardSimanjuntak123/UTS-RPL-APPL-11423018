@extends('layouts.app')
@section('page-title', 'Book New Appointment')

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
                <h2>Book New Appointment</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('patient.appointments.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select Doctor</label>
                    <select class="form-select" id="doctor_id" name="doctor_id" required>
                        <option value="">Choose a doctor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="appointment_date" class="form-label">Appointment Date</label>
                    <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Visit</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Appointment Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="consultation">Consultation</option>
                        <option value="checkup">Checkup</option>
                        <option value="surgery">Surgery</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                    <a href="{{ route('patient.appointments') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
