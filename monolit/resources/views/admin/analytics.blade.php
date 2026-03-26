@extends('layouts.app')

@section('page-title', 'Analytics & Reports')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}">
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
        <a href="{{ route('admin.analytics') }}" class="active">
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
                <i class="bi bi-calendar2-check"></i>
            </div>
            <div class="stat-number">{{ $totalAppointments ?? 0 }}</div>
            <div class="stat-label">Total Appointments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-file-medical"></i>
            </div>
            <div class="stat-number">{{ $totalMedicalRecords ?? 0 }}</div>
            <div class="stat-label">Medical Records</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="stat-number">Rp {{ number_format($totalTransactions ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Total Transactions</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-number">{{ $appointmentCompletionRate ?? 0 }}%</div>
            <div class="stat-label">Completion Rate</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Revenue Trend
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> Appointment Status Distribution
            </div>
            <div class="card-body">
                <canvas id="appointmentStatusChart" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Top Doctors by Appointments
            </div>
            <div class="card-body">
                @if(isset($topDoctors) && $topDoctors->count() > 0)
                    <div style="height: 200px; overflow-y: auto;">
                        @foreach($topDoctors as $doctor)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e9ecef;">
                                <strong>{{ $doctor->name }}</strong>
                                <span class="badge bg-primary">{{ $doctor->appointments_count ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">No data available</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <i class="bi bi-download"></i> Reports
                    </div>
                    <button class="btn btn-sm btn-success" onclick="exportReport()">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Report</th>
                                <th>Period</th>
                                <th>Generated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Revenue Report</strong></td>
                                <td>This Month</td>
                                <td>{{ now()->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Patient Report</strong></td>
                                <td>This Month</td>
                                <td>{{ now()->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Doctor Performance</strong></td>
                                <td>This Month</td>
                                <td>{{ now()->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: [1200000, 1900000, 1300000, 2100000],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Appointment Status Chart
    const statusCtx = document.getElementById('appointmentStatusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Scheduled', 'Cancelled'],
                datasets: [{
                    data: [{{ $completedAppointments ?? 0 }}, {{ $scheduledAppointments ?? 0 }}, {{ $cancelledAppointments ?? 0 }}],
                    backgroundColor: ['#198754', '#0d6efd', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});

function exportReport() {
    alert('Exporting report... This feature will be available soon.');
}
</script>
@endsection
