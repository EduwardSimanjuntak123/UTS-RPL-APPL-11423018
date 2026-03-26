@extends('layouts.app')

@section('page-title', 'System Settings')

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
        <a href="{{ route('admin.settings') }}" class="active">
            <i class="bi bi-gear nav-icon"></i>
            Settings
        </a>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                <i class="bi bi-gear"></i> General Settings
            </a>
            <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-envelope"></i> Email Configuration
            </a>
            <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-lock"></i> Security
            </a>
            <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-arrow-repeat"></i> Backup
            </a>
        </div>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <!-- General Settings -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-gear"></i> General Settings
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="app_name" class="form-label">Application Name</label>
                                <input type="text" class="form-control" id="app_name" name="app_name" value="MediTrack" required>
                            </div>
                            <div class="mb-3">
                                <label for="app_url" class="form-label">Application URL</label>
                                <input type="url" class="form-control" id="app_url" name="app_url" value="{{ url('/') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone" required>
                                    <option value="UTC">UTC</option>
                                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Bangkok">Asia/Bangkok</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="IDR">IDR (Rupiah)</option>
                                    <option value="USD">USD (Dollar)</option>
                                    <option value="THB">THB (Baht)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Email Configuration -->
            <div class="tab-pane fade" id="email" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-envelope"></i> Email Configuration
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.settings.update-email') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="mail_driver" class="form-label">Mail Driver</label>
                                <select class="form-select" id="mail_driver" name="mail_driver">
                                    <option value="smtp">SMTP</option>
                                    <option value="mailgun">Mailgun</option>
                                    <option value="sendmail">Sendmail</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="mail_host" class="form-label">Mail Host</label>
                                <input type="text" class="form-control" id="mail_host" name="mail_host" value="smtp.mailtrap.io">
                            </div>
                            <div class="mb-3">
                                <label for="mail_port" class="form-label">Mail Port</label>
                                <input type="number" class="form-control" id="mail_port" name="mail_port" value="2525">
                            </div>
                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label">From Address</label>
                                <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="noreply@meditrack.local">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Save Email Configuration
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-lock"></i> Security Settings
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Two-Factor Authentication</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="twofa" checked>
                                <label class="form-check-label" for="twofa">
                                    Enable Two-Factor Authentication for admin accounts
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                            <input type="number" class="form-control" id="session_timeout" value="30" min="5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Security</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rate_limiting" checked>
                                <label class="form-check-label" for="rate_limiting">
                                    Enable API Rate Limiting
                                </label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary">
                            <i class="bi bi-check"></i> Update Security Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- Backup -->
            <div class="tab-pane fade" id="backup" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-arrow-repeat"></i> Database Backup
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Backup Options</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_backup" checked>
                                <label class="form-check-label" for="auto_backup">
                                    Enable automatic daily backups
                                </label>
                            </div>
                        </div>

                        <h6 class="mt-4">Recent Backups</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Size</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ now()->format('d M Y H:i') }}</td>
                                        <td>245 MB</td>
                                        <td>
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-warning mt-3">
                            <i class="bi bi-arrow-clockwise"></i> Create Backup Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
