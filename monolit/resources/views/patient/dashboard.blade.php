@extends('layouts.app')

@section('page-title', 'Patient Dashboard')

@section('sidebar-menu')
    <li>
        <a href="{{ route('patient.dashboard') }}" class="active">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('patient.appointments') }}">
            <i class="bi bi-calendar2-event nav-icon"></i>
            My Appointments
        </a>
    </li>
    <li>
        <a href="{{ route('patient.medical-records') }}">
            <i class="bi bi-file-medical nav-icon"></i>
            Medical Records
        </a>
    </li>
    <li>
        <a href="{{ route('patient.prescriptions') }}">
            <i class="bi bi-capsule nav-icon"></i>
            Prescriptions
        </a>
    </li>
    <li>
        <a href="{{ route('patient.payments') }}">
            <i class="bi bi-credit-card nav-icon"></i>
            Payments
        </a>
    </li>
    <li>
        <a href="{{ route('patient.profile') }}">
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
            <div class="stat-number">{{ $upcomingAppointments ?? 0 }}</div>
            <div class="stat-label">Upcoming Appointments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-file-medical"></i>
            </div>
            <div class="stat-number">{{ $medicalRecords ?? 0 }}</div>
            <div class="stat-label">Medical Records</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-capsule"></i>
            </div>
            <div class="stat-number">{{ $activePrescriptions ?? 0 }}</div>
            <div class="stat-label">Active Prescriptions</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-wallet"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($totalPayments ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Total Paid</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar2-event"></i> Upcoming Appointments
                <a href="{{ route('patient.appointments.create') }}" class="btn btn-sm btn-primary float-end">
                    <i class="bi bi-plus"></i> Book Appointment
                </a>
            </div>
            <div class="card-body">
                @if(isset($appointments) && $appointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    <tr>
                                        <td>
                                            <i class="bi bi-calendar3"></i>
                                            {{ $appointment->appointment_date->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            <strong>{{ $appointment->doctor->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $appointment->doctor->specialty ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($appointment->type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-status-{{ strtolower($appointment->status) }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('patient.appointments.show', $appointment) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($appointment->status === 'scheduled')
                                                <a href="{{ route('patient.appointments.edit', $appointment) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size: 48px; color: #cbd5e1;"></i>
                        <p class="text-muted mt-3">No appointments scheduled</p>
                        <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Book Your First Appointment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-capsule"></i> Active Prescriptions
            </div>
            <div class="card-body">
                @if(isset($prescriptions) && $prescriptions->count() > 0)
                    @foreach($prescriptions as $prescription)
                        <div class="mb-3 p-3" style="background: #f8fafc; border-radius: 6px;">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <strong>{{ $prescription->medication }}</strong><br>
                                    <small class="text-muted">
                                        {{ $prescription->dosage }} - {{ $prescription->frequency }}
                                    </small><br>
                                    <small class="text-muted">
                                        Duration: {{ $prescription->duration }} days
                                    </small>
                                </div>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-capsule" style="font-size: 32px; color: #cbd5e1;"></i>
                        <p class="text-muted mt-2">No active prescriptions</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Quick Info
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Insurance Provider:</strong><br>
                    <span class="text-muted">{{ auth()->user()->insurance_provider ?? 'Not set' }}</span>
                </div>
                <div>
                    <strong>Last Check-up:</strong><br>
                    <span class="text-muted">
                        @if(isset($lastCheckup))
                            {{ $lastCheckup->created_at->format('d M Y') }}
                        @else
                            No check-up recorded
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
