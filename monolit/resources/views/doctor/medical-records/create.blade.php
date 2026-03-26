@extends('layouts.app')
@section('page-title', 'Create Medical Record')

@section('sidebar-menu')
    <li><a href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('doctor.appointments') }}">Appointments</a></li>
    <li><a href="{{ route('doctor.patients') }}">Patients</a></li>
    <li><a href="{{ route('doctor.medical-records') }}" class="active">Medical Records</a></li>
    <li><a href="{{ route('doctor.prescriptions') }}">Prescriptions</a></li>
    <li><a href="{{ route('doctor.profile') }}">Profile</a></li>
@endsection

@section('content')
<div class="container-lg">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('doctor.medical-records') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <h2>Create Medical Record</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('doctor.medical-records.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="patient_id" class="form-label">Select Patient</label>
                    <select class="form-select" id="patient_id" name="patient_id" required>
                        <option value="">Choose patient</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis</label>
                    <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="treatment" class="form-label">Treatment</label>
                    <textarea class="form-control" id="treatment" name="treatment" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="lab_results" class="form-label">Lab Results</label>
                    <textarea class="form-control" id="lab_results" name="lab_results" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="medications" class="form-label">Medications</label>
                    <textarea class="form-control" id="medications" name="medications" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                    <input type="date" class="form-control" id="follow_up_date" name="follow_up_date">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Record</button>
                    <a href="{{ route('doctor.medical-records') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
