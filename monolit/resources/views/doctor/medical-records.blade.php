@extends('layouts.app')

@section('page-title', 'Medical Records')

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
        <a href="{{ route('doctor.medical-records') }}" class="active">
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
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-file-medical"></i> Medical Records
            </div>
            <a href="{{ route('doctor.medical-records.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Create Record
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <input type="text" class="form-control" id="searchRecords" placeholder="Search by patient name or diagnosis...">
        </div>

        @if(isset($medicalRecords) && $medicalRecords->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                            <th>Follow-up</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTable">
                        @foreach($medicalRecords as $record)
                            <tr>
                                <td>
                                    <strong>{{ $record->created_at->format('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ $record->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $record->patient->name }}</strong><br>
                                    <small class="text-muted">{{ $record->patient->phone }}</small>
                                </td>
                                <td>{{ $record->diagnosis ? substr($record->diagnosis, 0, 30) . '...' : '-' }}</td>
                                <td>{{ $record->treatment ? substr($record->treatment, 0, 30) . '...' : '-' }}</td>
                                <td>
                                    @if($record->follow_up_date)
                                        {{ \Carbon\Carbon::parse($record->follow_up_date)->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('doctor.medical-records.show', $record) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('doctor.medical-records.edit', $record) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No medical records created yet.
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchRecords');
    const table = document.getElementById('recordsTable');
    
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
