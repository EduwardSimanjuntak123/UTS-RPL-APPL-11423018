@extends('layouts.app')

@section('page-title', 'Admin Dashboard')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}" class="active">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users') }}">
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
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-number">{{ $totalUsers ?? 0 }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-stethoscope"></i>
            </div>
            <div class="stat-number">{{ $totalDoctors ?? 0 }}</div>
            <div class="stat-label">Doctors</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-heart-pulse"></i>
            </div>
            <div class="stat-number">{{ $totalPatients ?? 0 }}</div>
            <div class="stat-label">Patients</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-wallet"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> System Activity (Last 7 Days)
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="80"></canvas>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-list"></i> Recent Activities
            </div>
            <div class="card-body">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="activity-timeline">
                        @foreach($recentActivities as $activity)
                            <div style="display: flex; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef;">
                                <div style="font-size: 24px; color: #0d6efd;">
                                    <i class="bi bi-{{ 
                                        strpos($activity->action, 'created') ? 'plus-circle' : 
                                        (strpos($activity->action, 'updated') ? 'pencil-circle' : 
                                        (strpos($activity->action, 'deleted') ? 'dash-circle' : 'circle'))
                                    }}"></i>
                                </div>
                                <div>
                                    <strong>{{ ucfirst($activity->action) }}</strong><br>
                                    <small class="text-muted">{{ $activity->description ?? '' }}</small><br>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No recent activities.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> User Distribution
            </div>
            <div class="card-body text-center">
                <canvas id="userDistributionChart" height="150"></canvas>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-exclamation-triangle"></i> System Status
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>API Status</span>
                        <span class="badge bg-success">Healthy</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Database</span>
                        <span class="badge bg-success">Connected</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Storage</span>
                        <span class="badge bg-success">Available</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Cache</span>
                        <span class="badge bg-warning">Warming</span>
                    </div>
                </div>
                <hr>
                <a href="{{ route('admin.settings') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-gear"></i> System Settings
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Quick Actions
            </div>
            <div class="card-body">
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary w-100 mb-2">
                    <i class="bi bi-person-plus"></i> Add User
                </a>
                <a href="{{ route('admin.pharmacies.create') }}" class="btn btn-sm btn-info w-100 mb-2">
                    <i class="bi bi-plus"></i> Add Pharmacy
                </a>
                <a href="{{ route('admin.analytics') }}" class="btn btn-sm btn-success w-100">
                    <i class="bi bi-file-earmark-pdf"></i> Export Report
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Appointments',
                    data: [12, 19, 3, 5, 2, 3, 18],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Payments',
                    data: [8, 15, 7, 12, 5, 9, 14],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // User Distribution Chart
    const distributionCtx = document.getElementById('userDistributionChart');
    if (distributionCtx) {
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Patients', 'Doctors', 'Pharmacists'],
                datasets: [{
                    data: [{{ $totalPatients ?? 0 }}, {{ $totalDoctors ?? 0 }}, {{ $totalPharmacists ?? 0 }}],
                    backgroundColor: ['#0d6efd', '#198754', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endsection
