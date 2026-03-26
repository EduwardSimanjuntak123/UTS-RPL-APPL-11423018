@extends('layouts.app')

@section('page-title', 'My Appointments')

@section('sidebar-menu')
    <li>
        <a href="{{ route('doctor.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('doctor.appointments') }}" class="active">
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
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar2-event"></i> My Appointments
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                    <i class="bi bi-calendar-check"></i> Upcoming
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                    <i class="bi bi-check-circle"></i> Completed
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                    <i class="bi bi-x-circle"></i> Cancelled
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingAppointments as $appt)
                                    <tr>
                                        <td>
                                            <strong>{{ $appt->appointment_date->format('d M Y') }}</strong><br>
                                            <small class="text-muted">{{ $appt->appointment_date->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $appt->patient->name }}</strong><br>
                                            <small class="text-muted">{{ $appt->patient->phone }}</small>
                                        </td>
                                        <td><span class="badge bg-info">{{ ucfirst($appt->type) }}</span></td>
                                        <td>{{ $appt->location ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-status-{{ strtolower($appt->status) }}">
                                                {{ ucfirst($appt->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('doctor.appointments.show', $appt) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal{{ $appt->id }}">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No upcoming appointments.
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                @if(isset($completedAppointments) && $completedAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedAppointments as $appt)
                                    <tr>
                                        <td>{{ $appt->appointment_date->format('d M Y H:i') }}</td>
                                        <td>{{ $appt->patient->name }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($appt->type) }}</span></td>
                                        <td>
                                            <a href="{{ route('doctor.appointments.show', $appt) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No completed appointments.
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                @if(isset($cancelledAppointments) && $cancelledAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cancelledAppointments as $appt)
                                    <tr>
                                        <td>{{ $appt->appointment_date->format('d M Y H:i') }}</td>
                                        <td>{{ $appt->patient->name }}</td>
                                        <td><span class="badge bg-danger">{{ ucfirst($appt->type) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No cancelled appointments.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Complete Appointment Modal -->
@if(isset($upcomingAppointments))
    @foreach($upcomingAppointments as $appt)
        <div class="modal fade" id="completeModal{{ $appt->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('doctor.appointments.complete', $appt) }}">
                        @csrf
                        <div class="modal-body">
                            <p>Are you sure you want to mark this appointment as completed?</p>
                            <div class="mb-3">
                                <label for="notes{{ $appt->id }}" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes{{ $appt->id }}" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Complete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
