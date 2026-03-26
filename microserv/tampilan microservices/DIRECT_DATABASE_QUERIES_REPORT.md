# Direct Database Queries Report - Microservices Architecture Violation

**Generated:** March 26, 2026  
**Status:** ❌ CRITICAL - Multiple violations found (should use ApiClient instead)

---

## Summary
- **Total Files with Direct Queries:** 10
- **Total Query Instances:** 50+
- **Critical Issue:** Direct database access violates microservices architecture

---

## 1. API Controllers (CRITICAL)

### ❌ [app/Http/Controllers/UserController.php](app/Http/Controllers/UserController.php)
- **Line 19** - `User::query()` - Direct Eloquent query
- **Line 23** - `$query->where('role', $request->role)` - Query filtering
- **Line 28** - `$query->where('status', $request->status)` - Query filtering
- **Line 34-35** - `$query->where(function ($q) use ($search) { ... })` - Complex query with search
- **Line 56** - `User::create($validated)` - Direct record creation
- **Line 88** - `$user->update($validated)` - Direct model update
- **Line 102** - `$user->delete()` - Direct model deletion
- **Line 115-117** - `User::where('role', 'doctor')->where('status', 'active')->get()` - Direct doctor query
- **Line 130-132** - `User::where('role', 'patient')->where('status', 'active')->get()` - Direct patient query
- **Line 145-147** - `User::where('role', 'pharmacist')->where('status', 'active')->get()` - Direct pharmacist query

**Status:** Old controller - should be replaced with `UserControllerMicroservices` using ApiClient

---

## 2. Service Layer (CRITICAL - WRONG ARCHITECTURE)

### ❌ [app/Services/PrescriptionService.php](app/Services/PrescriptionService.php)
- **Line 17** - `Prescription::create([...])` - Direct Eloquent create
- **Line 41-44** - `DrugStock::where('pharmacy_id', $pharmacy->id)->where('drug_name', ...)->first()` - Direct query
- **Line 56** - `PrescriptionOrder::create([...])` - Direct create
- **Line 75-81** - `Pharmacy::where('status', 'active')->whereHas('drugStock', ...)->get()` - Complex relationship query
- **Line 95-98** - `DrugStock::where('pharmacy_id', ...)->where('drug_name', ...)->first()` - Direct query
- **Line 108** - `$prescription->update(['status' => 'completed'])` - Direct update
- **Line 110** - Loop through `$prescription->prescriptionOrders` - Relationship query
- **Line 112** - `$order->update(['status' => 'cancelled'])` - Direct update

**Issue:** Should use ApiClient for all operations, not Eloquent

---

### ❌ [app/Services/MedicalRecordService.php](app/Services/MedicalRecordService.php)
- **Line 16** - `MedicalRecord::create([...])` - Direct Eloquent create
- **Line 23** - `$appointment->update(['status' => 'completed'])` - Direct update
- **Line 39-42** - `MedicalRecord::where('patient_id', $patientId)->with(['doctor', 'appointment'])->orderBy('created_at', 'desc')->get()` - Direct relationship query
- **Line 50** - `MedicalRecord::where('patient_id', $patientId)->get()` - Direct query
- **Line 51-53** - `Prescription::where('patient_id', $patientId)->where('status', 'active')->get()` - Direct query
- **Line 69-72** - `MedicalRecord::where('patient_id', $patientId)->with(['doctor', 'appointment'])->get()` - Direct query with relationships
- **Line 86-88** - `MedicalRecord::where('patient_id', $patientId)->where('diagnosis', $diagnosis)->where('created_at', '>=', now()->subDays($daysBack))->exists()` - Direct query

**Issue:** Should be entirely ApiClient-based

---

## 3. Web Controllers (LEGACY - Should Not Be Used)

### ❌ [app/Http/Controllers/Web/AdminController.php](app/Http/Controllers/Web/AdminController.php)
- **Line 38** - `User::all()` - Direct query for all users
- **Line 39** - `User::where('role', 'doctor')->get()` - Direct doctor query
- **Line 40** - `User::where('role', 'patient')->get()` - Direct patient query
- **Line 41** - `User::where('role', 'pharmacist')->get()` - Direct pharmacist query
- **Line 122** - `$user->update($validated)` - Direct update
- **Line 132** - `$user->delete()` - Direct deletion
- **Line 154** - `User::where('role', 'pharmacist')->get()` - Direct query
- **Line 190** - `User::where('role', 'pharmacist')->get()` - Direct query
- **Line 208** - `$pharmacy->update($validated)` - Direct update
- **Line 218** - `$pharmacy->delete()` - Direct deletion

**Issues:** 
- Using direct Eloquent instead of services
- Uses Laravel's Auth::check() and Auth::user()->role which trigger database lookups

---

### ❌ [app/Http/Controllers/Web/PharmacistController.php](app/Http/Controllers/Web/PharmacistController.php)
- **Line 22-24** - `DrugStock::where('quantity', '<', $this->getReorderLevel())->limit(5)->get()` - Direct query
- **Line 26-28** - `PrescriptionOrder::where('status', 'pending')->latest()->limit(5)->get()` - Direct query
- **Line 29** - `DrugStock::sum(DB::raw('quantity * price'))` - Direct SQL aggregation
- **Line 36** - `DrugStock::paginate(15)` - Direct pagination query
- **Line 37** - `DrugStock::where('quantity', '<', 10)->count()` - Direct count query
- **Line 57** - `$drug->update($validated)` - Direct update
- **Line 67** - `$drug->delete()` - Direct deletion
- **Line 74-75** - `PrescriptionOrder::paginate(15)` - Direct pagination
- **Line 76** - `PrescriptionOrder::where('status', 'pending')->count()` - Direct count
- **Line 77** - `PrescriptionOrder::where('status', 'ready')->count()` - Direct count

**Issues:**
- Uses direct Eloquent instead of ApiClient
- No service layer abstraction
- Multiple database queries in dashboard

---

### ❌ [app/Http/Controllers/Web/DoctorController.php](app/Http/Controllers/Web/DoctorController.php)
- **Line 24** - `$doctor->doctorAppointments()->whereDate('appointment_date', today())->latest()->get()` - Direct relationship query
- **Line 28-31** - `$doctor->doctorAppointments()->where('appointment_date', '>', now())->latest()->limit(5)->get()` - Direct query
- **Line 32** - `$doctor->doctorMedicalRecords()->latest()->limit(5)->get()` - Direct relationship query
- **Line 33** - `$doctor->patients()->limit(10)->get()` - Direct relationship query
- **Line 43** - `$doctor->doctorAppointments()->latest()->paginate(10)` - Direct pagination

**Issues:**
- Uses Auth::user() which is a database lookup
- Uses Eloquent relationships directly on the user model

---

### ❌ [app/Http/Controllers/Web/PatientController.php](app/Http/Controllers/Web/PatientController.php)
Similar issues - using Eloquent relationships directly (not shown in full detail)

---

## 4. Middleware (CRITICAL)

### ⚠️ [app/Http/Middleware/CheckRole.php](app/Http/Middleware/CheckRole.php)
- **Line 14** - `Auth::check()` - **Triggers database query for user session**
- **Line 18** - `Auth::user()->role` - **Triggers database query for user data**

**Issue:** Every request through this middleware triggers 2 database queries to verify user authentication

---

## 5. Database Seeders (DEAD CODE - Should Not Have Direct Queries)

### ❌ [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php)
Contains old seeding code (lines 24-144) mixing legacy Eloquent queries:
- `User::create()` - Direct creation
- `Pharmacy::create()` - Direct creation
- `DrugStock::create()` - Direct creation
- `Appointment::create()` - Direct creation
- `Appointment::where('status', 'completed')->get()` - Direct query
- `MedicalRecord::create()` - Direct creation
- `MedicalRecord::all()` - Direct query
- `Prescription::create()` - Direct creation

**Note:** Currently this file delegates to `MicroservicesSeeder` (Line 17), but contains legacy code that should be removed

---

## 6. Models (No Direct Issues Found)

All models only have `$casts` properties, no boot methods, observers, or accessor queries detected.

---

## 7. Config & Bootstrap (CLEAN)

✅ **bootstrap/app.php** - No database queries  
✅ **config/** - No database queries

---

## 8. Routes (CLEAN)

✅ **routes/api.php** - No closure-based queries  
✅ **routes/web.php** - No closure-based queries

---

## Summary by Severity

### 🔴 CRITICAL (Blocks microservices migration):
1. **UserController.php** - 10+ direct Eloquent queries (should be deleted/delegated to UserControllerMicroservices)
2. **PrescriptionService.php** - 8+ direct Eloquent queries (should use ApiClient)
3. **MedicalRecordService.php** - 7+ direct Eloquent queries (should use ApiClient)
4. **CheckRole.php middleware** - Database queries on every protected route

### 🟠 HIGH (Legacy code still active):
1. **AdminController.php (Web)** - 10+ direct queries
2. **PharmacistController.php (Web)** - 10+ direct queries
3. **DoctorController.php (Web)** - 5+ direct relationship queries
4. **PatientController.php (Web)** - Unknown count (not analyzed)

### 🟡 MEDIUM (Dead code):
1. **DatabaseSeeder.php** - Legacy seeding code that should be removed

---

## Required Actions

### Priority 1 (Do First):
- [ ] Fix **CheckRole middleware** to use token-based verification instead of `Auth::user()`
- [ ] Rewrite **PrescriptionService.php** to use ApiClient only
- [ ] Rewrite **MedicalRecordService.php** to use ApiClient only
- [ ] Delete **UserController.php** (use UserControllerMicroservices instead)

### Priority 2 (Do Second):
- [ ] Fix **AdminController.php** to use services with ApiClient
- [ ] Fix **PharmacistController.php** to use services with ApiClient
- [ ] Fix **DoctorController.php** to remove Auth::user() dependency

### Priority 3 (Do Third):
- [ ] Remove legacy seeding code from **DatabaseSeeder.php**
- [ ] Remove **PatientController.php** or migrate it

### Priority 4 (Verify):
- [ ] Check all Microservices controllers are using ApiClient correctly
- [ ] Verify all Go microservices are running
- [ ] Test that no database queries go directly from Laravel to PostgreSQL
