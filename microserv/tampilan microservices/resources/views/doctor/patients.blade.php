@extends('layouts.app')

@section('page-title', 'My Patients')

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
        <a href="{{ route('doctor.patients') }}" class="active">
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
        <i class="bi bi-people"></i> My Patients
    </div>

    <div class="card-body">
        <div class="mb-3">
            <input type="text" class="form-control" id="searchPatients" placeholder="Search by name or email...">
        </div>

        @if(isset($patients) && $patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Insurance</th>
                            <th>Last Visit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTable">
                        @foreach($patients as $patient)
                            <tr>
                                <td><strong>{{ $patient->name }}</strong></td>
                                <td>{{ $patient->email }}</td>
                                <td>{{ $patient->phone ?? '-' }}</td>
                                <td>{{ $patient->insurance_provider ?? '-' }}</td>
                                <td>
                                    @if($patient->latestAppointment)
                                        {{ $patient->latestAppointment->appointment_date->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#patientModal{{ $patient->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No patients found.
            </div>
        @endif
    </div>
</div>

<!-- Patient Detail Modal -->
@if(isset($patients))
    @foreach($patients as $patient)
        <div class="modal fade" id="patientModal{{ $patient->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $patient->name }} - Patient Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Personal Information</h6>
                                <p>
                                    <strong>Email:</strong> {{ $patient->email }}<br>
                                    <strong>Phone:</strong> {{ $patient->phone ?? '-' }}<br>
                                    <strong>Address:</strong> {{ $patient->address ?? '-' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Health Information</h6>
                                <p>
                                    <strong>Insurance:</strong> {{ $patient->insurance_provider ?? '-' }}<br>
                                    <strong>Total Appointments:</strong>
                                    @php
                                        $appointmentCount = $patient->patientAppointments ? $patient->patientAppointments->count() : 0;
                                    @endphp
                                    {{ $appointmentCount }}<br>
                                    <strong>Medical Records:</strong>
                                    @php
                                        $recordCount = $patient->medicalRecords ? $patient->medicalRecords->count() : 0;
                                    @endphp
                                    {{ $recordCount }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('doctor.appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Schedule Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPatients');
    const table = document.getElementById('patientsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    }
});
</script>
@endsection
