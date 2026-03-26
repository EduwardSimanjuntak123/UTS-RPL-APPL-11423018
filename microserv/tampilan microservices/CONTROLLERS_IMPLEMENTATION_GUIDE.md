# Laravel Microservices Controllers - Implementation Examples

## 📋 Overview

Dokumentasi ini menunjukkan bagaimana mengimplementasikan Laravel controllers yang menggunakan microservices API melalui Service wrapper classes.

**3 Controllers Example dibuat:**
1. `UserControllerMicroservices` - User management, authentication, roles, audit logs
2. `AppointmentControllerMicroservices` - Appointment scheduling, confirmation, cancellation
3. `MedicalRecordControllerMicroservices` - Medical records, prescriptions, lab results, clinical notes

---

## 🔄 Architecture Pattern

### Traditional Laravel (Monolithic)
```
Controller → Model → Database
  ↓
Get User from DB → Process → Return Response
```

### New Microservices Pattern
```
Controller → Service Class → ApiClient → API Gateway → Microservice → Database
  ↓
Get User from API Gateway → Process Response → Return Response
```

---

## 📝 Implementation Pattern

### 1. Dependency Injection di Constructor

```php
<?php
namespace App\Http\Controllers;

use App\Services\UserService;

class UserControllerMicroservices extends Controller
{
    // Service di-inject melalui constructor
    public function __construct(private UserService $userService)
    {
    }

    public function index()
    {
        // Gunakan $this->userService untuk semua operations
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }
}
```

### 2. Error Handling & Logging

Semua methods mengikuti pattern yang sama:

```php
public function index(Request $request): JsonResponse
{
    try {
        // Build filters dari request
        $filters = [];
        if ($request->has('status')) {
            $filters['status'] = $request->status;
        }

        // Call service method
        $data = $this->userService->getAllUsers($filters);

        // Return success response
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => 'Data fetched successfully',
        ]);
    } catch (\Exception $e) {
        // Log error untuk debugging
        \Log::error('Failed to fetch users: ' . $e->getMessage());

        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch data',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

### 3. Request Validation

Validasi dilakukan di controller sebelum mengirim ke service:

```php
public function store(Request $request): JsonResponse
{
    try {
        // Validasi request dengan Laravel rules
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',  // Note: ini untuk Laravel validation
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Kirim ke service
        $result = $this->userService->createUser($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Created successfully',
            'data' => $result,
        ], 201);
    } catch (\Exception $e) {
        // ...
    }
}
```

---

## 🔐 Authentication Flow

### Login Flow

```php
public function login(Request $request): JsonResponse
{
    try {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Service call menggunakan ApiClient yang akan:
        // 1. POST ke /auth/login di API Gateway
        // 2. Menerima JWT token
        // 3. Menyimpan token ke session: session(['api_token' => $token])
        $result = $this->userService->login($validated['email'], $validated['password']);

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    } catch (\Exception $e) {
        // ...
    }
}
```

### Protected Routes

Untuk routes yang memerlukan autentikasi:

**routes/api.php**:
```php
Route::middleware(['auth:sanctum', 'microservice.token'])->group(function () {
    // Routes yang memerlukan login
    Route::get('/users', [UserControllerMicroservices::class, 'index']);
    Route::post('/appointments', [AppointmentControllerMicroservices::class, 'store']);
});
```

**Token Middleware** (sudah dibuat):
```php
// app/Http/Middleware/MicroserviceTokenMiddleware.php
// Middleware ini:
// 1. Mengambil token dari Authorization header
// 2. Menyimpan token ke session
// 3. ApiClient otomatis menggunakan token dari session
```

---

## 🛣️ Routing Setup

### API Routes dengan Resource Pattern

**routes/api.php**:
```php
<?php

use App\Http\Controllers\UserControllerMicroservices;
use App\Http\Controllers\AppointmentControllerMicroservices;
use App\Http\Controllers\MedicalRecordControllerMicroservices;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('/auth/login', [UserControllerMicroservices::class, 'login']);
Route::post('/auth/register', [UserControllerMicroservices::class, 'register']);

// Protected routes
Route::middleware(['auth:sanctum', 'microservice.token'])->group(function () {
    // User routes
    Route::apiResource('users', UserControllerMicroservices::class);
    Route::get('/users/{id}/audit-logs', [UserControllerMicroservices::class, 'auditLogs']);
    Route::get('/roles', [UserControllerMicroservices::class, 'getRoles']);
    Route::post('/auth/logout', [UserControllerMicroservices::class, 'logout']);

    // Appointment routes
    Route::apiResource('appointments', AppointmentControllerMicroservices::class);
    Route::post('/appointments/{id}/confirm', [AppointmentControllerMicroservices::class, 'confirm']);
    Route::post('/appointments/{id}/cancel', [AppointmentControllerMicroservices::class, 'cancel']);
    Route::post('/appointments/{id}/complete', [AppointmentControllerMicroservices::class, 'complete']);
    Route::put('/appointments/{id}/reschedule', [AppointmentControllerMicroservices::class, 'reschedule']);
    Route::get('/doctors/{doctorId}/available-slots', [AppointmentControllerMicroservices::class, 'getAvailableSlots']);
    Route::get('/patients/{patientId}/appointments', [AppointmentControllerMicroservices::class, 'getPatientAppointments']);
    Route::get('/doctors/{doctorId}/schedule', [AppointmentControllerMicroservices::class, 'getDoctorSchedule']);
    Route::get('/appointments/today', [AppointmentControllerMicroservices::class, 'getTodayAppointments']);

    // Medical record routes
    Route::apiResource('medical-records', MedicalRecordControllerMicroservices::class, [
        'names' => [
            'index' => 'indexMedicalRecords',
            'show' => 'showMedicalRecord',
            'store' => 'storeMedicalRecord',
            'update' => 'updateMedicalRecord',
            'destroy' => 'destroyMedicalRecord',
        ]
    ]);
    Route::get('/patients/{patientId}/medical-records', [MedicalRecordControllerMicroservices::class, 'getPatientMedicalRecords']);

    // Prescription routes
    Route::get('/prescriptions', [MedicalRecordControllerMicroservices::class, 'indexPrescriptions']);
    Route::get('/prescriptions/{id}', [MedicalRecordControllerMicroservices::class, 'showPrescription']);
    Route::post('/prescriptions', [MedicalRecordControllerMicroservices::class, 'storePrescription']);
    Route::put('/prescriptions/{id}', [MedicalRecordControllerMicroservices::class, 'updatePrescription']);
    Route::get('/patients/{patientId}/prescriptions', [MedicalRecordControllerMicroservices::class, 'getPatientPrescriptions']);

    // Lab results routes
    Route::get('/lab-results', [MedicalRecordControllerMicroservices::class, 'indexLabResults']);
    Route::post('/lab-results', [MedicalRecordControllerMicroservices::class, 'storeLabResult']);

    // Clinical notes routes
    Route::get('/clinical-notes', [MedicalRecordControllerMicroservices::class, 'indexClinicalNotes']);
    Route::post('/clinical-notes', [MedicalRecordControllerMicroservices::class, 'storeClinicalNote']);
    Route::put('/clinical-notes/{id}', [MedicalRecordControllerMicroservices::class, 'updateClinicalNote']);
    Route::get('/patients/{patientId}/clinical-notes', [MedicalRecordControllerMicroservices::class, 'getPatientClinicalNotes']);
});
```

---

## 🧪 Testing dengan curl

### 1. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "doctor@meditrack.com",
    "password": "password123"
  }'

# Response:
{
  "status": "success",
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "user": {
    "id": "uuid",
    "name": "Dr. John",
    "email": "doctor@meditrack.com",
    "role": "doctor"
  }
}
```

### 2. Get Users (dengan token)
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIs..." \
  -H "Content-Type: application/json"

# Response:
{
  "status": "success",
  "data": [
    {
      "id": "uuid",
      "name": "Patient 1",
      "email": "patient1@example.com",
      "role": "patient"
    },
    ...
  ],
  "message": "Users fetched successfully"
}
```

### 3. Create Appointment
```bash
curl -X POST http://localhost:8000/api/appointments \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIs..." \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "uuid",
    "doctor_id": "uuid",
    "appointment_date": "2024-02-15",
    "appointment_time": "14:30",
    "reason": "Regular checkup",
    "appointment_type": "checkup",
    "duration_minutes": 30
  }'

# Response:
{
  "status": "success",
  "message": "Appointment created successfully",
  "data": {
    "id": "uuid",
    "patient_id": "uuid",
    "doctor_id": "uuid",
    "appointment_date": "2024-02-15",
    "appointment_time": "14:30",
    "status": "scheduled",
    "created_at": "2024-02-01T10:00:00Z"
  }
}
```

### 4. Get Patient Appointments
```bash
curl -X GET http://localhost:8000/api/patients/uuid/appointments \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIs..." \
  -H "Content-Type: application/json"

# Response:
{
  "status": "success",
  "data": [
    {
      "id": "uuid",
      "appointment_date": "2024-02-15",
      "appointment_time": "14:30",
      "status": "scheduled",
      "doctor": {...},
      "reason": "Regular checkup"
    },
    ...
  ]
}
```

### 5. Create Medical Record
```bash
curl -X POST http://localhost:8000/api/medical-records \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIs..." \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "uuid",
    "doctor_id": "uuid",
    "diagnosis": "Type 2 Diabetes",
    "symptoms": ["Excessive thirst", "Frequent urination"],
    "examination_findings": "Blood sugar level elevated",
    "treatment_plan": "Start metformin 500mg twice daily",
    "medication_prescribed": ["Metformin"],
    "notes": "Patient advised to maintain diet and exercise"
  }'

# Response:
{
  "status": "success",
  "message": "Medical record created successfully",
  "data": {
    "id": "uuid",
    "patient_id": "uuid",
    "diagnosis": "Type 2 Diabetes",
    "treatment_plan": "Start metformin 500mg twice daily",
    "created_at": "2024-02-01T10:00:00Z"
  }
}
```

---

## 📊 Response Format

Semua responses mengikuti format yang konsisten:

### Success Response
```json
{
  "status": "success",
  "data": { /* atau array */ },
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Error description",
  "error": "Detailed error message"
}
```

### List Response with Pagination (jika supported)
```json
{
  "status": "success",
  "data": [
    { /* item 1 */ },
    { /* item 2 */ }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 100,
    "last_page": 10
  }
}
```

---

## 🔄 Data Flow Contoh

### Scenario: Create Appointment

**Step 1: Frontend/Postman**
```
POST /api/appointments
Authorization: Bearer token
Body: { patient_id, doctor_id, appointment_date, ... }
```

**Step 2: Laravel Controller**
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $result = $this->appointmentService->createAppointment($validated);
    return response()->json([...], 201);
}
```

**Step 3: AppointmentService**
```php
public function createAppointment($data)
{
    return $this->apiClient->post(
        config('microservices.services.appointment') . '/appointments',
        $data
    );
}
```

**Step 4: ApiClient**
```php
public function post($endpoint, $data)
{
    // Ambil token dari session
    $token = session('api_token');
    
    // Kirim HTTP POST dengan retry + caching
    // Headers: Authorization: Bearer $token
    // Body: $data
}
```

**Step 5: API Gateway**
```
Route POST /appointments → appointment-service:3002
```

**Step 6: Appointment Service (Go)**
```go
func CreateAppointment(c *gin.Context) {
    // Validate data
    // Create in database
    // Return JSON response
}
```

**Step 7: Response back to Frontend**
```json
{
  "id": "uuid",
  "status": "success",
  "created_at": "..."
}
```

---

## ⚙️ Konfigurasi yang Diperlukan

### 1. Register Service Providers (jika belum)

**config/app.php**:
```php
'providers' => [
    // ...
    App\Providers\AppServiceProvider::class,
],
```

### 2. Enable Middleware

**app/Http/Kernel.php**:
```php
protected $routeMiddleware = [
    // ...
    'microservice.token' => \App\Http\Middleware\MicroserviceTokenMiddleware::class,
];
```

### 3. .env Configuration

```
MICROSERVICES_API_URL=http://localhost:3000
API_TIMEOUT=30
API_RETRY_ENABLED=true
API_RETRY_TIMES=3
API_RETRY_DELAY=100
API_CACHE_ENABLED=true
API_CACHE_TTL=3600
API_DEBUG=true
```

---

## 📚 Implementasi Checklist

- [ ] Copy 3 controller files ke `app/Http/Controllers/`
- [ ] Update `routes/api.php` dengan routes di atas
- [ ] Verify `.env` memiliki semua microservices URLs
- [ ] Test login endpoint terlebih dahulu
- [ ] Test CRUD operations dengan curl
- [ ] Update existing controllers satu per satu
- [ ] Test end-to-end dengan Postman atau curl
- [ ] Verify microservices running di ports 3001-3006
- [ ] Verify API Gateway running di port 3000
- [ ] Verify databases ready di port 3307

---

## 🚀 Next Steps

1. **Update Remaining Controllers**
   - PaymentController → PaymentService
   - PharmacyController → PharmacyService
   - Ikuti pattern yang sama

2. **Update Blade Views**
   - Adjust templates untuk API responses
   - Update form handlers
   - Implement error states

3. **Add Service Usage in Views**
   ```blade
   @foreach($users as $user)
     <tr>
       <td>{{ $user['name'] }}</td>
       <td>{{ $user['email'] }}</td>
       <td>{{ $user['role'] }}</td>
     </tr>
   @endforeach
   ```

4. **Testing**
   - Unit test untuk service calls
   - Integration test untuk controller + service
   - End-to-end test dengan real API

---

## 📞 Troubleshooting

### Issue: "Connection refused" to API
**Solution**: Verify microservices running: `curl http://localhost:3000/health`

### Issue: "401 Unauthorized"
**Solution**: Check token in session: Check logs di `storage/logs/laravel.log`

### Issue: "Token not in session"
**Solution**: Verify middleware properly handling Authorization header

### Issue: "API timeout"
**Solution**: Increase `API_TIMEOUT` di .env atau reduce microservices load
