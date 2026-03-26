<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .login-header h1 {
            margin: 0;
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            border-radius: 6px;
            color: white;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8, #4338ca);
            color: white;
        }

        .demo-credentials {
            background: #f0f4ff;
            border-left: 3px solid #2563eb;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
        }

        .demo-credentials h6 {
            margin-top: 0;
            color: #2563eb;
            font-weight: 600;
        }

        .demo-credentials p {
            margin: 5px 0;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .role-info {
            margin-top: 15px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 6px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="bi bi-hospital"></i> MediTrack</h1>
            <p>Digital Healthcare Platform</p>
        </div>

        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email"><i class="bi bi-envelope"></i> Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password"><i class="bi bi-lock"></i> Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>

            <div class="demo-credentials">
                <h6><i class="bi bi-info-circle"></i> Demo Credentials</h6>
                
                <p>
                    <strong>Admin:</strong><br>
                    📧 admin@meditrack.com<br>
                    🔐 password123
                </p>

                <p>
                    <strong>Doctor:</strong><br>
                    📧 doctor1@meditrack.com<br>
                    🔐 password123
                </p>

                <p>
                    <strong>Patient:</strong><br>
                    📧 patient1@meditrack.com<br>
                    🔐 password123
                </p>

                <p>
                    <strong>Pharmacist:</strong><br>
                    📧 pharmacist@meditrack.com<br>
                    🔐 password123
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
