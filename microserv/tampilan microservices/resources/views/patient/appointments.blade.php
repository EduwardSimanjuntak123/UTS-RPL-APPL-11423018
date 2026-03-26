@extends('layouts.app')

@section('page-title', 'My Appointments')

@section('sidebar-menu')
    <li>
        <a href="{{ route('patient.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('patient.appointments') }}" class="active">
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
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
            <div>
                <i class="bi bi-calendar2-event"></i> My Appointments
            </div>
            <a href="{{ route('patient.appointments.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Book New Appointment
            </a>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                    <i class="bi bi-calendar-check"></i> Upcoming
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                    <i class="bi bi-check-circle"></i> Completed
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">
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
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Duration</th>
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
                                            <strong>{{ $appt->doctor->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $appt->doctor->specialty ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($appt->type) }}</span>
                                        </td>
                                        <td>{{ $appt->location ?? '-' }}</td>
                                        <td>{{ $appt->duration }} mins</td>
                                        <td>
                                            <span class="badge badge-status-{{ strtolower($appt->status) }}">
                                                {{ ucfirst($appt->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('patient.appointments.edit', $appt) }}" class="btn btn-sm btn-warning" title="Reschedule">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $appt->id }}" title="Cancel">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No upcoming appointments. <a href="{{ route('patient.appointments.create') }}">Book one now</a>
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
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedAppointments as $appt)
                                    <tr>
                                        <td>{{ $appt->appointment_date->format('d M Y H:i') }}</td>
                                        <td>{{ $appt->doctor->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($appt->type) }}</span></td>
                                        <td>{{ $appt->notes ? substr($appt->notes, 0, 50) . '...' : '-' }}</td>
                                        <td>
                                            <a href="{{ route('patient.appointments.show', $appt) }}" class="btn btn-sm btn-info">
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
                        <i class="bi bi-info-circle"></i> No completed appointments yet.
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
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cancelledAppointments as $appt)
                                    <tr>
                                        <td>{{ $appt->appointment_date->format('d M Y H:i') }}</td>
                                        <td>{{ $appt->doctor->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-danger">{{ ucfirst($appt->type) }}</span></td>
                                        <td>{{ $appt->notes ?? '-' }}</td>
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

<!-- Cancel Modal -->
@if(isset($upcomingAppointments))
    @foreach($upcomingAppointments as $appt)
        <div class="modal fade" id="cancelModal{{ $appt->id }}" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Cancel Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('patient.appointments.cancel', $appt) }}">
                        @csrf
                        <div class="modal-body">
                            <p>Are you sure you want to cancel this appointment?</p>
                            <div class="mb-3">
                                <label for="reason{{ $appt->id }}" class="form-label">Reason (Optional)</label>
                                <textarea class="form-control" id="reason{{ $appt->id }}" name="reason" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Cancel Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
