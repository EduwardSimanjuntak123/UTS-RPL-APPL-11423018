@extends('layouts.app')

@section('page-title', 'Users Management')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users') }}" class="active">
            <i class="bi bi-people nav-icon"></i>
            Users Management
        </a>
    </li>
    <li>
        <a href="{{ route('admin.analytics') }}">
            <i class="bi bi-graph-up nav-icon"></i>
            Analytics
        </a>
    </li>
    <li>
        <a href="{{ route('admin.pharmacies') }}">
            <i class="bi bi-building nav-icon"></i>
            Pharmacies
        </a>
    </li>
    <li>
        <a href="{{ route('admin.settings') }}">
            <i class="bi bi-gear nav-icon"></i>
            Settings
        </a>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-people"></i> Users Management
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus"></i> Add User
            </a>
        </div>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                    All ({{ $totalUsers ?? 0 }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="doctors-tab" data-bs-toggle="tab" data-bs-target="#doctors" type="button" role="tab">
                    Doctors ({{ $totalDoctors ?? 0 }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="patients-tab" data-bs-toggle="tab" data-bs-target="#patients" type="button" role="tab">
                    Patients ({{ $totalPatients ?? 0 }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="pharmacists-tab" data-bs-toggle="tab" data-bs-target="#pharmacists" type="button" role="tab">
                    Pharmacists ({{ $totalPharmacists ?? 0 }})
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                @include('admin.partials.users-table', ['users' => $allUsers ?? []])
            </div>
            <div class="tab-pane fade" id="doctors" role="tabpanel" aria-labelledby="doctors-tab">
                @include('admin.partials.users-table', ['users' => $doctors ?? []])
            </div>
            <div class="tab-pane fade" id="patients" role="tabpanel" aria-labelledby="patients-tab">
                @include('admin.partials.users-table', ['users' => $patients ?? []])
            </div>
            <div class="tab-pane fade" id="pharmacists" role="tabpanel" aria-labelledby="pharmacists-tab">
                @include('admin.partials.users-table', ['users' => $pharmacists ?? []])
            </div>
        </div>
    </div>
</div>
@endsection
