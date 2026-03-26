@extends('layouts.app')
@section('page-title', 'Medical Record Details')

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
        <div class="col-md-10">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('doctor.medical-records') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chevron-left"></i> Back
                    </a>
                    <h2>Medical Record Details</h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('doctor.medical-records.edit', 1) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Record Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Patient</small>
                            <h6>Patient Name</h6>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Date</small>
                            <h6>Jan 15, 2025</h6>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted">Diagnosis</small>
                        <p>This is the diagnosis information about the patient's condition.</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Treatment Plan</small>
                        <p>The recommended treatment plan for addressing the diagnosis.</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Lab Results</small>
                        <p>Lab test results and findings relevant to the medical record.</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Medications</small>
                        <p>Currently prescribed medications for this condition.</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Follow-up Date</small>
                        <h6>Feb 15, 2025</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('doctor.medical-records.edit', 1) }}" class="btn btn-sm btn-outline-primary">
                            Edit Record
                        </a>
                        <a href="{{ route('doctor.prescriptions') }}" class="btn btn-sm btn-outline-primary">
                            Create Prescription
                        </a>
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Medical Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this medical record? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('doctor.medical-records.destroy', 1) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
