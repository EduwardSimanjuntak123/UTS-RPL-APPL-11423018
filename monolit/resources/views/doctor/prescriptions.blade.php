@extends('layouts.app')

@section('page-title', 'My Prescriptions')

@section('sidebar-menu')
    <li>
        <a href="{{ route('doctor.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('doctor.appointments') }}">
            <i class="bi bi-calendar2-event nav-icon"></i>
            My Appointments
        </a>
    </li>
    <li>
        <a href="{{ route('doctor.medical-records') }}">
            <i class="bi bi-file-medical nav-icon"></i>
            Medical Records
        </a>
    </li>
    <li>
        <a href="{{ route('doctor.patients') }}">
            <i class="bi bi-people nav-icon"></i>
            My Patients
        </a>
    </li>
    <li>
        <a href="{{ route('doctor.prescriptions') }}" class="active">
            <i class="bi bi-capsule nav-icon"></i>
            Prescriptions
        </a>
    </li>
    <li>
        <a href="{{ route('doctor.profile') }}">
            <i class="bi bi-person nav-icon"></i>
            Profile
        </a>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-capsule"></i> My Prescriptions
            </div>
            <a href="{{ route('doctor.prescriptions.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Create Prescription
            </a>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                    <i class="bi bi-check-circle"></i> Active
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                    <i class="bi bi-check-lg"></i> Completed
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                    All
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                @include('doctor.partials.prescription-table', ['prescriptions' => $activePrescriptions ?? []])
            </div>
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                @include('doctor.partials.prescription-table', ['prescriptions' => $completedPrescriptions ?? []])
            </div>
            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                @include('doctor.partials.prescription-table', ['prescriptions' => $allPrescriptions ?? []])
            </div>
        </div>
    </div>
</div>
@endsection
