# Laravel Microservices Integration Guide

## Arsitektur UI + Microservices

Laravel sekarang berfungsi **HANYA sebagai UI (User Interface)**, sementara semua data dan business logic dihandle oleh Microservices Golang.

```
┌─────────────────────────────────────────┐
│        Laravel Application              │
│  (Views, Controllers, Sessions)         │
│  hanya berinteraksi via REST API        │
└─────────────────┬───────────────────────┘
                  │ HTTP Requests
                  │ (dengan JWT Token)
                  ▼
┌─────────────────────────────────────────┐
│       API Gateway (Port 3000)           │
│  - JWT Authentication                   │
│  - Rate Limiting                        │
│  - Request Routing                      │
└──────────────────┬──────────────────────┘
                   │ Routes
    ┌──────────────┼──────────────┐
    │              │              │
    ▼              ▼              ▼
User Service   Appt Service   Medical Svc  ...
(3001)         (3002)         (3003)
```

## Struktur File Laravel untuk Microservices

```
app/
├── Services/
│   ├── Api/
│   │   └── ApiClient.php          # Base HTTP client untuk semua API calls
│   ├── UserService.php            # User service wrapper
│   ├── AppointmentService.php      # Appointment service wrapper
│   ├── MedicalService.php         # Medical service wrapper
│   ├── PharmacyService.php        # Pharmacy service wrapper
│   ├── PaymentService.php         # Payment service wrapper
│   └── AnalyticsService.php       # Analytics service wrapper
│
├── Http/
│   ├── Controllers/
│   │   ├── UserController.php     # Updated to use UserService
│   │   ├── AppointmentController.php  # Updated to use AppointmentService
│   │   ├── MedicalRecordController.php
│   │   ├── PaymentController.php
│   │   ├── PharmacyController.php
│   │   └── ... (Controllers updated to use Services)
│   │
│   └── Middleware/
│       └── MicroserviceTokenMiddleware.php  # JWT token handling
│
config/
└── microservices.php              # Configuration untuk API Gateway URLs
```

## Cara Menggunakan Services di Laravel

### 1. Dependency Injection (Recommended)

```php
<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {}

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('users.index', ['users' => $users]);
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return view('users.show', ['user' => $user]);
    }
}
```

### 2. Manual Instantiation

```php
<?php

use App\Services\UserService;

$userService = new UserService();
$users = $userService->getAllUsers();
```

### 3. Facade (Optional - Create if needed)

```php
<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-service';
    }
}

// Usage:
// UserApi::getAllUsers()
// UserApi::getUserById(1)
```

## Services API Reference

### UserService

```php
// CRUD Operations
$users = $userService->getAllUsers(['role' => 'doctor']); // Get all users with filters
$user = $userService->getUserById(1); // Get user by ID
$userService->createUser($data); // Create new user
$userService->updateUser($id, $data); // Update user
$userService->deleteUser($id); // Delete user

// Authentication
$userService->login('email@example.com', 'password'); // Login & get token
$userService->register($data); // Register new user

// Roles & Permissions
$roles = $userService->getAllRoles(); // Get all roles
$auditLogs = $userService->getAuditLogs(); // Get audit logs
$userLogs = $userService->getUserAuditLogs($userId); // Get user-specific audit logs
```

### AppointmentService

```php
// CRUD Operations
$appointments = $appointmentService->getAllAppointments();
$appointment = $appointmentService->getAppointmentById($id);
$appointmentService->createAppointment($data);
$appointmentService->updateAppointment($id, $data);
$appointmentService->deleteAppointment($id);

// Patient Appointments
$patientAppointments = $appointmentService->getPatientAppointments($patientId);

// Appointment Status Management
$appointmentService->confirmAppointment($id); // Confirm appointment
$appointmentService->cancelAppointment($id, 'reason'); // Cancel appointment

// Doctor Availability Slots
$slots = $appointmentService->getAvailableSlots(['doctorId' => '1']);
$appointmentService->createSlot($slotData);

// Notifications
$notifications = $appointmentService->getNotifications();
```

### MedicalService

```php
// Medical Records
$records = $medicalService->getAllMedicalRecords();
$record = $medicalService->getMedicalRecordById($id);
$medicalService->createMedicalRecord($data);
$medicalService->updateMedicalRecord($id, $data);

// Patient Records
$patientRecords = $medicalService->getPatientMedicalRecords($patientId);

// Prescriptions
$prescriptions = $medicalService->getAllPrescriptions();
$prescription = $medicalService->getPrescriptionById($id);
$medicalService->createPrescription($data);
$patientRx = $medicalService->getPatientPrescriptions($patientId);

// Lab Results
$labs = $medicalService->getAllLabResults();
$labResult = $medicalService->getLabResultById($id);
$medicalService->createLabResult($data);

// Clinical Notes
$notes = $medicalService->getClinicalNotes();
```

### PharmacyService

```php
// Drugs
$drugs = $pharmacyService->getAllDrugs();
$drug = $pharmacyService->getDrugById($id);
$pharmacyService->createDrug($data);
$pharmacyService->updateDrug($id, $data);

// Drug Stocks
$stocks = $pharmacyService->getDrugStocks();
$lowStock = $pharmacyService->getLowStockDrugs(); // Get low stock items

// Orders
$orders = $pharmacyService->getAllOrders();
$order = $pharmacyService->getOrderById($id);
$pharmacyService->createOrder($data);
$pharmacyService->updateOrder($id, $data);
$pharmacyService->confirmOrder($orderId);

// Patient Orders
$patientOrders = $pharmacyService->getPatientOrders($patientId);

// Inventory
$inventory = $pharmacyService->getInventoryLog();
```

### PaymentService

```php
// Invoices
$invoices = $paymentService->getAllInvoices();
$invoice = $paymentService->getInvoiceById($id);
$paymentService->createInvoice($data);
$paymentService->updateInvoice($id, $data);

// Patient Invoices
$patientInvoices = $paymentService->getPatientInvoices($patientId);

// Payments
$payments = $paymentService->getAllPayments();
$payment = $paymentService->getPaymentById($id);
$paymentService->createPayment($data);
$paymentService->confirmPayment($paymentId);

// Insurance Claims
$claims = $paymentService->getAllInsuranceClaims();
$claim = $paymentService->getInsuranceClaimById($id);
$paymentService->createInsuranceClaim($data);

// Reports
$revenueReport = $paymentService->getRevenueReport();
$pending = $paymentService->getPendingPayments();
```

### AnalyticsService

```php
// Metrics
$metrics = $analyticsService->getServiceMetrics();
$analyticsService->recordServiceMetric($data);

// Health Indicators
$health = $analyticsService->getHealthIndicators();
$serviceHealth = $analyticsService->getServiceHealth('user-service');

// Alerts
$alerts = $analyticsService->getSystemAlerts();
$alert = $analyticsService->getAlertById($id);
$analyticsService->createAlert($data);
$analyticsService->resolveAlert($alertId);

// Dashboard & Reports
$dashboard = $analyticsService->getDashboardSummary();
$userAnalytics = $analyticsService->getUserAnalytics();
$appointmentAnalytics = $analyticsService->getAppointmentAnalytics();
$revenueAnalytics = $analyticsService->getRevenueAnalytics();

// Reports
$daily = $analyticsService->getDailyReport();
$weekly = $analyticsService->getWeeklyReport();
$monthly = $analyticsService->getMonthlyReport();
$custom = $analyticsService->getCustomReport($filters);
```

## Configuration (.env)

```env
# Microservices API URLs
MICROSERVICES_API_URL=http://localhost:3000
GATEWAY_URL=http://localhost:3000

# Individual Service URLs (Optional - jika langsung call service)
USER_SERVICE_URL=http://localhost:3001
APPOINTMENT_SERVICE_URL=http://localhost:3002
MEDICAL_SERVICE_URL=http://localhost:3003
PHARMACY_SERVICE_URL=http://localhost:3004
PAYMENT_SERVICE_URL=http://localhost:3005
ANALYTICS_SERVICE_URL=http://localhost:3006

# API Settings
API_TIMEOUT=30
API_DEBUG=true

# Retry Configuration
API_RETRY_ENABLED=true
API_RETRY_TIMES=3
API_RETRY_DELAY=100

# Cache Configuration
API_CACHE_ENABLED=true
API_CACHE_TTL=3600
```

## Example: Update UserController

### Before (Direct DB Access)

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Direct database query - NO!
        $users = User::paginate(15);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        // Direct database save - NO!
        $user = User::create($request->validated());
        return response()->json($user);
    }
}
```

### After (API Calls via Service)

```php
<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {}

    public function index(Request $request)
    {
        $filters = [
            'role' => $request->get('role'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ];

        try {
            $users = $this->userService->getAllUsers($filters);
            return response()->json([
                'status' => 'success',
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:patient,doctor,pharmacist,admin',
        ]);

        try {
            $result = $this->userService->createUser($validated);
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $result = $this->userService->updateUser($id, $request->validated());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->userService->deleteUser($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->userService->login($validated['email'], $validated['password']);
            session(['api_token' => $result['token']]);
            session(['user' => $result['user']]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
```

## Views Integration

### Blade Template Example

```blade
<!-- resources/views/users/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Users Management</h1>

    @if(session('api_token'))
        <p>Logged in as: {{ session('user.email') }}</p>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user['id'] ?? $user['ID'] ?? '-' }}</td>
                    <td>{{ $user['name'] ?? $user['Name'] ?? '-' }}</td>
                    <td>{{ $user['email'] ?? $user['Email'] ?? '-' }}</td>
                    <td>{{ $user['role'] ?? $user['Role'] ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $user['status'] ? 'success' : 'danger' }}">
                            {{ $user['status'] ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user['id']) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('users.destroy', $user['id']) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
```

## Error Handling

ApiClient sudah meng-handle:
- ✅ Connection timeouts (dengan retry)
- ✅ Failed requests (dengan logging)
- ✅ Caching untuk GET requests
- ✅ JWT token management

Gunakan try-catch di controller:

```php
try {
    $data = $this->userService->getAllUsers();
} catch (\Exception $e) {
    \Log::error('API Error: ' . $e->getMessage());
    return response()->json([
        'error' => 'Failed to fetch data',
        'message' => $e->getMessage(),
    ], 500);
}
```

## Session & Authentication

Token disimpan di session setelah login:

```php
// Token otomatis dikirim ke setiap request
session(['api_token' => 'token_xxx']);
session(['user' => ['id' => 1, 'email' => 'user@example.com']]);
```

## Database Models Masih Diperlukan?

**Tidak untuk business logic data**, tapi mungkin diperlukan untuk:
- ✅ Laravel sessions (jika DB driver)
- ✅ Jobs queue (jika DB driver)
- ✅ Caching (jika DB cache driver)
- ✅ Local temporary data

**Hapus** Models yang mewakili microservices entities:
- User - ❌ Delete (use UserService)
- Appointment - ❌ Delete (use AppointmentService)
- MedicalRecord - ❌ Delete (use MedicalService)
- Pharmacy - ❌ Delete (use PharmacyService)
- Payment - ❌ Delete (use PaymentService)

## Migration & Seeding

**Tidak diperlukan** untuk data aplikasi (dihandle microservices), tapi tetap buat untuk Laravel internals:

```php
// database/migrations/xxxx_create_sessions_table.php (untuk session storage)
// database/migrations/xxxx_create_jobs_table.php (untuk job queue)
// database/migrations/xxxx_create_failed_jobs_table.php (untuk failed jobs)
```

## Monitoring & Logging

Semua API calls di-log secara otomatis:

```
storage/logs/laravel.log

[2026-03-25 10:15:30] local.INFO: API Request: GET /api/v1/users
[2026-03-25 10:15:30] local.INFO: API Cache HIT: /api/v1/users
```

Untuk debug, set di .env:
```env
API_DEBUG=true
LOG_LEVEL=debug
```

## Performance Tips

1. **Caching** - Enabled by default untuk GET requests (3600 detik)
   ```env
   API_CACHE_ENABLED=true
   API_CACHE_TTL=3600
   ```

2. **Retry** - Automatic retry untuk failed requests
   ```env
   API_RETRY_ENABLED=true
   API_RETRY_TIMES=3
   ```

3. **Lazy Loading** - Services hanya load when needed via DI

4. **Query Filtering** - Push filters ke API, jangan di Laravel

## Troubleshooting

**Error: "Failed to connect to localhost:3000"**
- Pastikan semua microservices running
- Check URL di .env `MICROSERVICES_API_URL`
- Test: `curl http://localhost:3000/health`

**Error: "Unauthorized - No token provided"**
- User harus login dulu via `/auth/login`
- Token disimpan di session otomatis

**Error: "API Connection Timeout"**
- Increase `API_TIMEOUT` di .env
- Check network connection ke microservices

**Cache Issues**
- Clear cache: `php artisan cache:clear`
- Disable cache untuk testing: `API_CACHE_ENABLED=false`

## Summary

✅ **Laravel sekarang:**
- Hanya render views/UI
- Tidak ada direct database access untuk business entities
- Semua data via microservices APIs
- Sessions & auth dihandle via JWT tokens
- Automatic retry & caching untuk resilience

✅ **Microservices handle:**
- Data persistence
- Business logic
- Database operations
- Cross-service communication

Ini adalah **true separation of concerns**! 🎉

---

**Last Updated**: March 25, 2026
**Version**: 1.0 - Laravel as Pure UI Layer
