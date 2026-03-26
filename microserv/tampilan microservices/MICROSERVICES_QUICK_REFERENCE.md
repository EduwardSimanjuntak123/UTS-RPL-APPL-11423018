# QUICK REFERENCE: MICROSERVICES-ONLY MODE

## TL;DR (Too Long; Didn't Read)

**Laravel can NO LONGER access the database directly.**

✅ **MUST USE**: Service wrappers through HTTP APIs
❌ **CANNOT USE**: Eloquent models or direct queries

---

## Before vs After

### Before (OLD - Monolithic) ❌
```php
// User::create() - DIRECT DATABASE
User::create(['name' => 'John', 'email' => 'john@example.com']);

$user = User::find(1);
$user->update(['name' => 'Jane']);
$user->delete();
```

### After (NOW - Microservices) ✅
```php
// $userService->createUser() - HTTP API
$userService->createUser(['name' => 'John', 'email' => 'john@example.com']);

$user = $userService->getUser(1);
$userService->updateUser(1, ['name' => 'Jane']);
$userService->deleteUser(1);
```

---

## Configuration Changes in Your Machine

### .env File
```diff
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3307
- DB_DATABASE=meditrack
- DB_USERNAME=root
- DB_PASSWORD=

- SESSION_DRIVER=database
+ SESSION_DRIVER=file

- CACHE_STORE=database
+ CACHE_STORE=array

- QUEUE_CONNECTION=database
+ QUEUE_CONNECTION=sync
```

### Database config/database.php
```php
'default' => null,  // NOW DISABLED!
```

---

## Services Available

Use these service classes for data access:

| Service | Purpose | Import |
|---------|---------|--------|
| UserService | User management | `use App\Services\UserService;` |
| AppointmentService | Appointments | `use App\Services\AppointmentService;` |
| MedicalRecordService | Medical data | `use App\Services\MedicalRecordService;` |
| PharmacyService | Pharmacy data | `use App\Services\PharmacyService;` |
| PaymentService | Payments | `use App\Services\PaymentService;` |
| PrescriptionService | Prescriptions | `use App\Services\PrescriptionService;` |

---

## Usage Examples

### ✅ Create User (CORRECT)
```php
namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}
    
    public function store(Request $request)
    {
        $user = $this->userService->createUser(
            $request->validated()
        );
        return response()->json($user, 201);
    }
}
```

### ❌ Create User (WRONG)
```php
namespace App\Http\Controllers;

use App\Models\User;  // ❌ NO!

class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = User::create(        // ❌ NO!
            $request->validated()
        );
        return response()->json($user, 201);
    }
}
```

---

## Common Operations

### Get all users
```php
// ✅ Correct
$users = $this->userService->getAllUsers();

// ❌ Wrong
$users = User::all();
```

### Get single user
```php
// ✅ Correct
$user = $this->userService->getUser($id);

// ❌ Wrong
$user = User::find($id);
```

### Update user
```php
// ✅ Correct
$user = $this->userService->updateUser($id, $data);

// ❌ Wrong
$user = User::find($id);
$user->update($data);
```

### Delete user
```php
// ✅ Correct
$this->userService->deleteUser($id);

// ❌ Wrong
User::destroy($id);
```

### Create appointment
```php
// ✅ Correct
$appointment = $this->appointmentService->createAppointment($appointmentData);

// ❌ Wrong
$appointment = Appointment::create($appointmentData);
```

---

## Error Handling

### ✅ Correct Pattern
```php
try {
    $user = $this->userService->getUser($id);
    return response()->json($user);
} catch (ApiException $e) {
    Log::error('API Error', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Service unavailable'], 503);
} catch (\Exception $e) {
    Log::error('Unexpected Error', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'An error occurred'], 500);
}
```

### ❌ Wrong Pattern
```php
// Don't rely on database exceptions
$user = User::find($id);  // ❌
// This won't work!
```

---

## Rules

### 🚫 Forbidden
- ❌ `import App\Models\*`
- ❌ `Model::create()`
- ❌ `Model::find()`
- ❌ `Model::where()`
- ❌ `Model::update()`
- ❌ `DB::table()`
- ❌ `DB::raw()`

### ✅ Allowed
- ✅ `use App\Services\*Service`
- ✅ `$this->service->createX()`
- ✅ `$this->service->getX()`
- ✅ `$this->service->updateX()`
- ✅ `$this->service->deleteX()`
- ✅ All HTTP-based operations

---

## How to Debug

### Check if microservices are running
```bash
curl http://localhost:3000/health
curl http://localhost:3001/health
# Should return: {"status":"OK"}
```

### Check Laravel API
```bash
curl http://localhost:8000/api/users
# Should return JSON from microservice
```

### Check logs
```bash
# View Laravel logs
tail storage/logs/laravel.log
```

### If getting "Service unavailable" (503)
1. Check if Go microservices are running
2. Check if API Gateway is up: `curl http://localhost:3000/health`
3. Look at service logs for errors
4. Check Laravel logs in `storage/logs/`

---

## Running the System

### Prerequisites
- ✅ Go 1.21+ installed
- ✅ MySQL running on localhost:3307
- ✅ PHP 8.1+ with Laravel installed
- ✅ 7 terminal windows

### Start Services (In Order!)

```bash
# Terminal 1: User Service
cd microservices/user-service
go run main.go

# Terminal 2: Appointment Service
cd microservices/appointment-service
go run main.go

# Terminal 3: Medical Service
cd microservices/medical-service
go run main.go

# Terminal 4: Pharmacy Service
cd microservices/pharmacy-service
go run main.go

# Terminal 5: Payment Service
cd microservices/payment-service
go run main.go

# Terminal 6: Prescription Service
cd microservices/prescription-service
go run main.go

# Terminal 7: Analytics Service
cd microservices/analytics-service
go run main.go

# Terminal 8: API Gateway (START LAST!)
cd microservices/api-gateway
go run cmd/main.go

# Terminal 9: Laravel
php artisan serve
```

### Check Everything Works
```bash
# Health checks
curl http://localhost:3000/health
curl http://localhost:8000/api/health

# Try creating a user
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "phone": "08123456789",
    "role": "patient"
  }'
```

---

## Checklists

### Before Starting
- [ ] .env updated (DB connections disabled)
- [ ] config/database.php default set to null
- [ ] AppServiceProvider has DB listener
- [ ] All controllers use service wrappers only
- [ ] No model imports in any controller

### When Starting Services
- [ ] All 7 Go services started
- [ ] API Gateway started LAST
- [ ] Health endpoints responding
- [ ] Laravel server started
- [ ] Logs showing no connection errors

### When Running Tests
- [ ] All microservices running
- [ ] Laravel serving
- [ ] API endpoints accessible
- [ ] No direct database queries in logs

---

## Support

**Q: Can I still use models?**
A: No. Models are disabled. Use services only.

**Q: What if a service crashes?**
A: ApiClient retries 3x, then returns 503 error gracefully.

**Q: How do I seed data?**
A: `php artisan db:seed` - but services must be running first!

**Q: Can I access database directly?**
A: No. It's not allowed. Use services instead.

**Q: Why this change?**
A: Better scalability, security, and separation of concerns.

---

## Files Changed

- ✅ `.env` - Configuration disabled
- ✅ `config/database.php` - Default set to null
- ✅ `app/Providers/AppServiceProvider.php` - DB listener added
- ✅ `database/seeders/DatabaseSeeder.php` - Now API-based
- ✅ `database/seeders/MicroservicesSeeder.php` - Created (NEW)
- ✅ All 7 controllers - Use services only
- ✅ All 6 services - Use ApiClient

---

**Ready to go!** 🚀

Start your microservices and Laravel, then test the endpoints.

