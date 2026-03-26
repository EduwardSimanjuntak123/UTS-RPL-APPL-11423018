@extends('layouts.app')

@section('page-title', 'Doctor Dashboard')

@section('sidebar-menu')
    <li>
        <a href="{{ route('doctor.dashboard') }}" class="active">
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
        <a href="{{ route('doctor.prescriptions') }}">
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
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-calendar2-check"></i>
            </div>
            <div class="stat-number">{{ $todayAppointments ?? 0 }}</div>
            <div class="stat-label">Today's Appointments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-number">{{ $totalPatients ?? 0 }}</div>
            <div class="stat-label">Total Patients</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-file-medical"></i>
            </div>
            <div class="stat-number">{{ $medicalRecordsCreated ?? 0 }}</div>
            <div class="stat-label">Records Created</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-capsule"></i>
            </div>
            <div class="stat-number">{{ $prescriptionsIssued ?? 0 }}</div>
            <div class="stat-label">Prescriptions Issued</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar2-event"></i> Today's Schedule
                <a href="{{ route('doctor.appointments') }}" class="btn btn-sm btn-primary float-end">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if(isset($todaySchedule) && $todaySchedule->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todaySchedule as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->appointment_date->format('H:i') }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $appointment->patient->name }}</strong><br>
                                            <small class="text-muted">{{ $appointment->patient->phone }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($appointment->type) }}</span>
                                        </td>
                                        <td>
                                            @if($appointment->status === 'scheduled')
                                                <span class="badge bg-warning">Scheduled</span>
                                            @elseif($appointment->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('doctor.appointments.show', $appointment) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No appointments scheduled for today.
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-file-medical"></i> Pending Medical Records
            </div>
            <div class="card-body">
                @if(isset($pendingRecords) && $pendingRecords->count() > 0)
                    <div class="list-group">
                        @foreach($pendingRecords as $appointment)
                            <a href="{{ route('doctor.medical-records.create', ['appointment_id' => $appointment->id]) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $appointment->patient->name }}</h6>
                                    <small>{{ $appointment->appointment_date->format('d M Y H:i') }}</small>
                                </div>
                                <p class="mb-1 text-muted">Appointment Type: {{ ucfirst($appointment->type) }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No pending medical records.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> Performance Metrics
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Appointment Rate</span>
                        <strong>{{ $appointmentCompletionRate ?? 0 }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $appointmentCompletionRate ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Patient Satisfaction</span>
                        <strong>{{ $patientSatisfaction ?? 0 }}/5</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ ($patientSatisfaction ?? 0) * 20 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Average Wait Time</span>
                        <strong>{{ $avgWaitTime ?? 0 }} mins</strong>
                    </div>
                </div>
                <hr>
                <div class="text-center text-muted">
                    <small>Based on last 30 days</small>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> About You
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="font-size: 48px; margin-bottom: 10px;">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
                <p>
                    <strong>Name:</strong> {{ auth()->user()->name }}<br>
                    <strong>Specialty:</strong> {{ auth()->user()->specialty ?? 'N/A' }}<br>
                    <strong>License:</strong> {{ auth()->user()->license_number ?? 'N/A' }}<br>
                    <strong>Status:</strong>
                    <span class="badge bg-success">{{ ucfirst(auth()->user()->status) }}</span>
                </p>
                <a href="{{ route('doctor.profile') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-pencil"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
