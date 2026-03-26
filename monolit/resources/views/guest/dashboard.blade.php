<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Digital Healthcare Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-bg: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        /* Navigation */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Hero Section */
        .hero-section {
            color: white;
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .btn-hero {
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .btn-hero-primary {
            background: white;
            color: #667eea;
            border: none;
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-hero-secondary:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 60px 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 36px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Features Section */
        .features-section {
            background: var(--light-bg);
            padding: 60px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 50px;
            color: #333;
        }

        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 20px;
        }

        .feature-card h4 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }

        /* Roles Section */
        .roles-section {
            background: white;
            padding: 60px 20px;
        }

        .role-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 40px 30px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
        }

        .role-card:hover {
            transform: translateY(-5px);
            border-color: #667eea;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
        }

        .role-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .role-card h5 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 15px;
            color: #333;
        }

        .role-card ul {
            text-align: left;
            margin-bottom: 25px;
            list-style: none;
            color: #666;
        }

        .role-card li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }

        .role-card li:before {
            content: '\f058';
            font-family: 'bootstrap-icons';
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: 700;
        }

        /* Demo Credentials */
        .credentials-section {
            background: linear-gradient(135deg, #fffacd 0%, #ffe4b5 100%);
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 5px solid #ffc107;
        }

        .credentials-section h4 {
            color: #d39e00;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cred-item {
            background: white;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 13px;
            border-left: 3px solid #667eea;
        }

        .cred-label {
            font-weight: 600;
            color: #667eea;
        }

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .footer p {
            margin: 0;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 32px;
            }

            .hero-section p {
                font-size: 16px;
            }

            .stat-number {
                font-size: 24px;
            }

            .section-title {
                font-size: 28px;
            }

            .btn-hero {
                display: block;
                margin: 10px 0;
                width: 100%;
            }
        }

        .btn-login-role {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-login-patient {
            background: #198754;
            color: white;
        }

        .btn-login-patient:hover {
            background: #157347;
            transform: translateY(-2px);
            color: white;
        }

        .btn-login-doctor {
            background: #0d6efd;
            color: white;
        }

        .btn-login-doctor:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            color: white;
        }

        .btn-login-pharmacist {
            background: #fd7e14;
            color: white;
        }

        .btn-login-pharmacist:hover {
            background: #e76d00;
            transform: translateY(-2px);
            color: white;
        }

        .btn-login-admin {
            background: #dc3545;
            color: white;
        }

        .btn-login-admin:hover {
            background: #bb2d3b;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-custom sticky-top">
        <div class="container-lg">
            <a class="navbar-brand" href="/">
                <i class="bi bi-hospital"></i> MediTrack
            </a>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-lg">
            <h1><i class="bi bi-heart-pulse"></i> Welcome to MediTrack</h1>
            <p>Digital Healthcare Platform for Modern Medical Management</p>
            <div>
                <a href="{{ route('login') }}" class="btn btn-hero btn-hero-primary">Get Started</a>
                <a href="#features" class="btn btn-hero btn-hero-secondary">Learn More</a>
            </div>
        </div>
    </section>


    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container-lg">
            <h2 class="section-title"><i class="bi bi-star"></i> Key Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-calendar-check"></i></div>
                        <h4>Appointment Scheduling</h4>
                        <p>Easy-to-use appointment booking system with automatic reminders and rescheduling options.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-file-text"></i></div>
                        <h4>Electronic Health Records</h4>
                        <p>Comprehensive EHR system securely storing patient medical history and treatment records.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-prescription2"></i></div>
                        <h4>Prescription Management</h4>
                        <p>Digital prescriptions with pharmacy integration for seamless medication fulfillment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-capsule"></i></div>
                        <h4>Pharmacy Network</h4>
                        <p>GPS-based pharmacy search to find nearby pharmacies with real-time inventory tracking.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-credit-card"></i></div>
                        <h4>Payment & Insurance</h4>
                        <p>Secure payment processing with integrated insurance claims management system.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-graph-up"></i></div>
                        <h4>Analytics Dashboard</h4>
                        <p>Real-time analytics and reporting for administrators to monitor system performance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="roles-section">
        <div class="container-lg">
            <h2 class="section-title"><i class="bi bi-lock"></i> User Roles</h2>
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="role-icon" style="color: #198754;"><i class="bi bi-person-heart"></i></div>
                        <h5>Patient</h5>
                        <ul>
                            <li>Schedule appointments</li>
                            <li>View medical records</li>
                            <li>Manage prescriptions</li>
                            <li>Track payments</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn-login-role btn-login-patient">Login as Patient</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="role-icon" style="color: #0d6efd;"><i class="bi bi-stethoscope"></i></div>
                        <h5>Doctor</h5>
                        <ul>
                            <li>Manage appointments</li>
                            <li>Create medical records</li>
                            <li>Issue prescriptions</li>
                            <li>Track patient progress</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn-login-role btn-login-doctor">Login as Doctor</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="role-icon" style="color: #fd7e14;"><i class="bi bi-capsule"></i></div>
                        <h5>Pharmacist</h5>
                        <ul>
                            <li>Manage drug inventory</li>
                            <li>Fulfill prescriptions</li>
                            <li>Track stock levels</li>
                            <li>Process orders</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn-login-role btn-login-pharmacist">Login as Pharmacist</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="role-icon" style="color: #dc3545;"><i class="bi bi-shield-check"></i></div>
                        <h5>Administrator</h5>
                        <ul>
                            <li>User management</li>
                            <li>System analytics</li>
                            <li>Pharmacy control</li>
                            <li>System settings</li>
                        </ul>
                        <a href="{{ route('login') }}" class="btn-login-role btn-login-admin">Login as Admin</a>
                    </div>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="credentials-section">
                        <h4><i class="bi bi-info-circle"></i> Demo Credentials for Testing</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="cred-item">
                                    <div class="cred-label">👨‍⚕️ Doctor:</div>
                                    doctor1@meditrack.com<br>
                                    <small>password123</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="cred-item">
                                    <div class="cred-label">👤 Patient:</div>
                                    patient1@meditrack.com<br>
                                    <small>password123</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="cred-item">
                                    <div class="cred-label">💊 Pharmacist:</div>
                                    pharmacist@meditrack.com<br>
                                    <small>password123</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="cred-item">
                                    <div class="cred-label">🔐 Admin:</div>
                                    admin@meditrack.com<br>
                                    <small>password123</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-lg">
            <p>&copy; 2026 MediTrack Digital Healthcare Platform. All rights reserved.</p>
            <p style="font-size: 12px; margin-top: 10px; opacity: 0.6;">Built with Laravel 11 | Bootstrap 5.3 | Monolithic Architecture</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
