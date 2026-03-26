@extends('layouts.app')

@section('page-title', 'Medical Records')

@section('sidebar-menu')
    <li>
        <a href="{{ route('patient.dashboard') }}">
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
        <a href="{{ route('patient.medical-records') }}" class="active">
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
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-file-medical"></i> My Medical Records
            </div>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-download"></i> Export Records
            </button>
        </div>
    </div>

    <div class="card-body">
        @if(isset($medicalRecords) && $medicalRecords->count() > 0)
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchRecords" placeholder="Search by diagnosis, treatment...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="recordsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicalRecords as $record)
                            <tr>
                                <td>
                                    <strong>{{ $record->created_at->format('d M Y') }}</strong><br>
                                    <small class="text-muted">{{ $record->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $record->doctor->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $record->doctor->specialty ?? '' }}</small>
                                </td>
                                <td>
                                    <span title="{{ $record->diagnosis ?? '' }}">
                                        {{ $record->diagnosis ? substr($record->diagnosis, 0, 30) . '...' : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span title="{{ $record->treatment ?? '' }}">
                                        {{ $record->treatment ? substr($record->treatment, 0, 30) . '...' : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Recorded</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#recordModal{{ $record->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No medical records found yet. Your records will appear here after your appointments.
            </div>
        @endif
    </div>
</div>

<!-- Medical Record Details Modal -->
@if(isset($medicalRecords))
    @foreach($medicalRecords as $record)
        <div class="modal fade" id="recordModal{{ $record->id }}" tabindex="-1" aria-labelledby="recordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recordModalLabel">
                            <i class="bi bi-file-medical"></i> Medical Record - {{ $record->created_at->format('d M Y') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Doctor Information</h6>
                                <p>
                                    <strong>{{ $record->doctor->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $record->doctor->specialty ?? '' }}</small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Appointment</h6>
                                @if($record->appointment)
                                    <p>
                                        <strong>{{ $record->appointment->appointment_date->format('d M Y H:i') }}</strong><br>
                                        <small class="text-muted">{{ $record->appointment->type }}</small>
                                    </p>
                                @else
                                    <p class="text-muted">-</p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6>Diagnosis</h6>
                            <p>{{ $record->diagnosis ?? '-' }}</p>
                        </div>

                        <div class="mb-3">
                            <h6>Treatment</h6>
                            <p>{{ $record->treatment ?? '-' }}</p>
                        </div>

                        @if($record->lab_results)
                            <div class="mb-3">
                                <h6>Lab Results</h6>
                                <p>{{ $record->lab_results }}</p>
                            </div>
                        @endif

                        @if($record->medications)
                            <div class="mb-3">
                                <h6>Medications</h6>
                                <p>{{ $record->medications }}</p>
                            </div>
                        @endif

                        @if($record->follow_up_date)
                            <div class="mb-3">
                                <h6>Follow-up Date</h6>
                                <p>{{ \Carbon\Carbon::parse($record->follow_up_date)->format('d M Y') }}</p>
                            </div>
                        @endif

                        <small class="text-muted">
                            Recorded on: {{ $record->created_at->format('d M Y H:i') }}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="printRecord({{ $record->id }})">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="bi bi-download"></i> Export Medical Records
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Export form commented out - to be implemented via API --}}
            {{-- <form method="GET" action="{{ route('patient.medical-records.export') }}"> --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Records</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="records" id="allRecords" value="all" checked>
                            <label class="form-check-label" for="allRecords">All Records</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="records" id="recentRecords" value="recent">
                            <label class="form-check-label" for="recentRecords">Last 3 Months</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <!-- <button type=\"submit\" class=\"btn btn-success\">
                        <i class=\"bi bi-download\"></i> Export
                    </button>
                </div>
            </form> -->
        </div>
    </div>
</div>

<script>
function printRecord(recordId) {
    const modal = document.getElementById('recordModal' + recordId);
    const content = modal.querySelector('.modal-body').innerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Medical Record</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Simple search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchRecords');
    const table = document.getElementById('recordsTable');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    }
});
</script>
@endsection
