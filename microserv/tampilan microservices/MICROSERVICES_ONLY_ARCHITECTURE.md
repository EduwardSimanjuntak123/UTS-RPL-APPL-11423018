# MICROSERVICES-ONLY ARCHITECTURE

## Overview

**Laravel is now a pure API consumer and UI layer.**

- ❌ **NO direct database access from Laravel**
- ❌ **NO Eloquent ORM queries**
- ❌ **NO direct model manipulation**
- ✅ **ALL data operations through Go microservices APIs**
- ✅ **ALL controllers use service wrappers**
- ✅ **ALL HTTP requests are API mediated**

---

## Configuration Changes

### 1. Environment Variables (.env)

**Database Access: DISABLED**
```env
# ========================================================
# MICROSERVICES MODE - No Direct Database Access
# All database operations handled by Go microservices
# ========================================================
# DB_CONNECTION=mysql          ← DISABLED
# DB_HOST=127.0.0.1           ← DISABLED
# DB_PORT=3307                ← DISABLED
# DB_DATABASE=meditrack       ← DISABLED
# DB_USERNAME=root            ← DISABLED
# DB_PASSWORD=                ← DISABLED
```

**Session/Cache/Queue: Changed from Database to File**
```env
SESSION_DRIVER=file              # Was: database
CACHE_STORE=array                # Was: database  
QUEUE_CONNECTION=sync            # Was: database
```

### 2. Database Configuration (config/database.php)

```php
'default' => null,  // Disabled - Use microservices APIs instead
```

---

## Architecture Flow

```
User Browser/API Client
        ↓
  Laravel Routes (API layer)
        ↓
  Microservices Controllers
        ↓
  Service Wrappers
  (UserService, AppointmentService, etc.)
        ↓
  ApiClient (HTTP requests)
        ↓
  API Gateway (Port 3000)
        ↓
  Go Microservices (Ports 3001-3006)
        ↓
  MySQL Databases (Port 3307)
```

**Result**: Laravel never touches the database directly.

---

## Data Seeding

### Old Way (DEPRECATED) ❌
```php
// DON'T DO THIS ANYMORE!
User::create([...]);
Appointment::create([...]);
```

### New Way (CURRENT) ✅
```php
// Use service wrappers via microservices APIs
$this->userService->createUser($userData);
$this->appointmentService->createAppointment($appointmentData);
```

**Seeder Location**: `database/seeders/MicroservicesSeeder.php`

**Run Seeders**:
```bash
# Start all microservices first!
# Then run:
php artisan db:seed
```

---

## Controller Guidelines

### ✅ CORRECT Pattern

```php
<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserControllerMicroservices extends Controller
{
    public function __construct(private UserService $userService) {}
    
    public function store(Request $request): JsonResponse
    {
        try {
            // Call microservice through wrapper
            $user = $this->userService->createUser($request->validated());
            return response()->json($user, 201);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

### ❌ WRONG Pattern (Will Cause Errors)

```php
// DON'T USE ELOQUENT!
User::create($data);              // ❌ NO
User::find($id);                  // ❌ NO
User::delete();                   // ❌ NO
DB::table('users')->insert();     // ❌ NO
```

---

## Service Wrappers (Data Access Layer)

These wrappers handle all API communication:

- `App\Services\UserService` - User management
- `App\Services\AppointmentService` - Appointment handling
- `App\Services\MedicalRecordService` - Medical data
- `App\Services\PharmacyService` - Pharmacy operations
- `App\Services\PaymentService` - Payment processing
- `App\Services\PrescriptionService` - Prescription lifecycle

**All wrappers**:
- ✅ Use ApiClient for HTTP requests
- ✅ Implement retry logic (3x) 
- ✅ Support response caching (3600s)
- ✅ Include proper error handling
- ✅ Log all operations

---

## Rules for Laravel Development

### 1. **NO Models, NO Direct Database Access**
```php
// ❌ NEVER DO THIS
User::where('email', $email)->first();
Appointment::create($data);
```

### 2. **ALWAYS Use Service Wrappers**
```php
// ✅ ALWAYS DO THIS
$user = $this->userService->getUserByEmail($email);
$appointment = $this->appointmentService->createAppointment($data);
```

### 3. **ALL Data Flows Through APIs**
```
Laravel Controller
    ↓
Service Wrapper (ApiClient)
    ↓
Go Microservice API
    ↓
Database
```

### 4. **Handle API Failures Gracefully**
```php
try {
    $data = $this->userService->getUser($id);
} catch (ApiException $e) {
    Log::error('API call failed', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Service unavailable'], 503);
}
```

### 5. **No Model Migrations in Laravel**
- Laravel migrations are DISABLED
- Database schema is managed by Go microservices only
- Migrations exist for reference, but should not be run from Laravel

---

## What Changes for Developers

### Before (Monolithic) ❌
```php
// Could access database directly
$users = User::all();
$user->update($data);
```

### After (Microservices) ✅
```php
// Must use service wrappers
$users = $this->userService->getAllUsers();
$user = $this->userService->updateUser($id, $data);
```

---

## Deployment Checklist

Before deploying, ensure:

- [ ] All microservices (7 services) are running
- [ ] API Gateway is accessible at `http://localhost:3000`
- [ ] All service wrappers are configured
- [ ] `.env` has database connections DISABLED
- [ ] ConfigModel database.php default is `null`
- [ ] No direct database queries in any controller
- [ ] `php artisan serve` starts successfully
- [ ] Test endpoint: `curl http://localhost:8000/api/health`

---

## Testing Data/API

### Check Microservices Health
```bash
# API Gateway
curl http://localhost:3000/health

# Individual services
curl http://localhost:3001/health  # User Service
curl http://localhost:3002/health  # Appointment Service
curl http://localhost:3003/health  # Medical Service
curl http://localhost:3004/health  # Pharmacy Service
curl http://localhost:3005/health  # Payment Service
curl http://localhost:3006/health  # Prescription Service
```

### Seed Data (via Microservices)
```bash
# Make sure Go microservices are running first!
php artisan db:seed

# Specific seeder
php artisan db:seed --class=MicroservicesSeeder
```

---

## FAQs

**Q: Can I use Eloquent models?**
A: No. All Eloquent models must be removed. Use service wrappers instead.

**Q: What if a microservice is down?**
A: ApiClient implements retry logic (3x). If service is down:
- Request fails after 3 retries
- Exception is caught and logged
- HTTP 503 response is returned to client

**Q: Can I query the database directly?**
A: No. Laravel database connection is disabled. All queries must go through microservices.

**Q: How do I add new data?**
A: Create method in appropriate service wrapper that calls the microservice API endpoint.

**Q: What about views/UI?**
A: Views remain unchanged. They receive data from controllers, which get it from microservices.

---

## Migration Path (Already Completed)

✅ Phase 1: Create Go microservices (7 services)
✅ Phase 2: Create service wrappers (6 services)
✅ Phase 3: Delete old Eloquent-based controllers
✅ Phase 4: Create new Microservices controllers
✅ Phase 5: Disable database access in Laravel
✅ Phase 6: Update Seeders to use APIs

**Status**: 🟢 Complete - System is now 100% microservices-driven

---

## Support

**Issue**: "Laravel can't connect to database"
**Solution**: This is expected. Laravel is now an API consumer. Use service wrappers.

**Issue**: "Model class not found"
**Solution**: Models are no longer used. Import service instead.

**Issue**: "Seeding fails"
**Solution**: Ensure all Go microservices are running, then run `php artisan db:seed`.

---

## Summary

| Aspect | Before | After |
|--------|--------|-------|
| Database Access | Direct (Eloquent) | API only (Microservices) |
| Data Queries | `User::find($id)` | `$userService->getUser($id)` |
| Seeding | Model factories | API seeder |
| Models Used | Yes (Eloquent) | No (API contracts only) |
| Configuration | `DB_*` env vars | Service wrapper configs |
| Failure Handling | Exceptions | Retry + Log + Graceful errors |
| Network Layer | N/A (same server) | HTTP APIs + JSON responses |

---

**🎉 Laravel is now a pure API client, microservices layer handles ALL data operations! 🎉**
