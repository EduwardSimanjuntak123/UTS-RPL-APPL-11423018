# Testing Guide - Microservices API dengan curl & Postman

## 📋 Setup

Sebelum testing, pastikan:

1. **Semua Microservices Running**
   ```bash
   # Cek kesehatan API Gateway
   curl http://localhost:3000/health
   
   # Expected response:
   {"status": "healthy", "timestamp": "2024-02-01T10:00:00Z"}
   ```

2. **Database Migrations Sudah Dijalankan**
   ```bash
   # Untuk setiap service, run migrations
   # Di user-service: migrate up
   # Di appointment-service: migrate up
   # etc.
   ```

3. **Laravel Running**
   ```bash
   cd /d:/semester\ 6/APPL/UTS-RPL-APPL-11423018/uts
   php artisan serve
   # Server akan jalan di http://localhost:8000
   ```

---

## 🔐 Authentication Test

### 1. Login User

**Endpoint**: `POST /api/auth/login`

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "doctor@meditrack.com",
    "password": "password123"
  }'
```

**Expected Response (201)**:
```json
{
  "status": "success",
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Dr. John Doe",
    "email": "doctor@meditrack.com",
    "role": "doctor",
    "phone": "08123456789",
    "created_at": "2024-01-15T10:00:00Z"
  }
}
```

**Save Token untuk Testing Lanjutan**:
```bash
# PowerShell
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."

# Gunakan di command berikutnya
curl -H "Authorization: Bearer $token" ...
```

---

## 👤 User Management Tests

**Prerequisites**: Token sudah dapat dari Login test

### 1. Get All Users

**curl Command**:
```bash
$token = "YOUR_TOKEN_HERE"
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (200)**:
```json
{
  "status": "success",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "name": "Dr. John Doe",
      "email": "doctor@meditrack.com",
      "role": "doctor",
      "phone": "08123456789",
      "created_at": "2024-01-15T10:00:00Z"
    },
    {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "name": "Patient Ahmad",
      "email": "ahmad@example.com",
      "role": "patient",
      "phone": "08198765432",
      "created_at": "2024-01-20T10:00:00Z"
    }
  ],
  "message": "Users fetched successfully"
}
```

### 2. Get All Users dengan Filter

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/users?role=doctor&status=active" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 3. Get Single User

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/users/550e8400-e29b-41d4-a716-446655440000 \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (200)**:
```json
{
  "status": "success",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Dr. John Doe",
    "email": "doctor@meditrack.com",
    "role": "doctor",
    "phone": "08123456789",
    "specialty": "General Medicine",
    "created_at": "2024-01-15T10:00:00Z"
  }
}
```

### 4. Create New User (Register)

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Patient Budi",
    "email": "budi@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "08567890123",
    "address": "Jl. Merdeka No. 123",
    "role": "patient"
  }'
```

**Expected Response (201)**:
```json
{
  "status": "success",
  "message": "Registration successful",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440002",
    "name": "Patient Budi",
    "email": "budi@example.com",
    "role": "patient",
    "phone": "08567890123",
    "created_at": "2024-02-01T10:00:00Z"
  }
}
```

### 5. Update User

**curl Command**:
```bash
curl -X PUT http://localhost:8000/api/users/550e8400-e29b-41d4-a716-446655440002 \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Patient Budi Updated",
    "phone": "08567890999",
    "address": "Jl. Sudirman No. 456"
  }'
```

### 6. Delete User

**curl Command**:
```bash
curl -X DELETE http://localhost:8000/api/users/550e8400-e29b-41d4-a716-446655440002 \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 7. Get All Roles

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/roles \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (200)**:
```json
{
  "status": "success",
  "data": [
    {
      "id": "1",
      "role_name": "patient",
      "description": "Patient role for medical consultations"
    },
    {
      "id": "2",
      "role_name": "doctor",
      "description": "Doctor role for providing medical services"
    },
    {
      "id": "3",
      "role_name": "pharmacist",
      "description": "Pharmacist role for drug management"
    },
    {
      "id": "4",
      "role_name": "admin",
      "description": "Admin role for system management"
    }
  ]
}
```

### 8. Get Audit Logs

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/audit-logs" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (200)**:
```json
{
  "status": "success",
  "data": [
    {
      "id": "1",
      "user_id": "550e8400-e29b-41d4-a716-446655440000",
      "action": "login",
      "resource": "user",
      "details": "User logged in from 192.168.1.1",
      "timestamp": "2024-02-01T10:00:00Z"
    },
    {
      "id": "2",
      "user_id": "550e8400-e29b-41d4-a716-446655440000",
      "action": "create",
      "resource": "medical_record",
      "details": "Created medical record for patient ID xxx",
      "timestamp": "2024-02-01T10:05:00Z"
    }
  ]
}
```

---

## 📅 Appointment Tests

### 1. Create Appointment

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/appointments \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "doctor_id": "550e8400-e29b-41d4-a716-446655440000",
    "appointment_date": "2024-02-15",
    "appointment_time": "14:30",
    "reason": "Regular checkup for diabetes follow-up",
    "appointment_type": "followup",
    "duration_minutes": 30,
    "notes": "Patient mentioned having headaches lately"
  }'
```

**Expected Response (201)**:
```json
{
  "status": "success",
  "message": "Appointment created successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440100",
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "doctor_id": "550e8400-e29b-41d4-a716-446655440000",
    "appointment_date": "2024-02-15",
    "appointment_time": "14:30",
    "status": "scheduled",
    "reason": "Regular checkup for diabetes follow-up",
    "appointment_type": "followup",
    "duration_minutes": 30,
    "created_at": "2024-02-01T10:00:00Z"
  }
}
```

### 2. Get All Appointments

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/appointments?status=scheduled" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 3. Get Available Slots untuk Doctor

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/doctors/550e8400-e29b-41d4-a716-446655440000/available-slots?date=2024-02-15" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (200)**:
```json
{
  "status": "success",
  "data": [
    {
      "time": "09:00",
      "available": true
    },
    {
      "time": "09:30",
      "available": true
    },
    {
      "time": "10:00",
      "available": false
    },
    {
      "time": "14:30",
      "available": true
    }
  ]
}
```

### 4. Confirm Appointment

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/appointments/550e8400-e29b-41d4-a716-446655440100/confirm \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (200)**:
```json
{
  "status": "success",
  "message": "Appointment confirmed successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440100",
    "status": "confirmed",
    "confirmed_at": "2024-02-01T10:10:00Z"
  }
}
```

### 5. Cancel Appointment

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/appointments/550e8400-e29b-41d4-a716-446655440100/cancel \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "reason": "Patient cannot attend due to emergency"
  }'
```

### 6. Complete Appointment

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/appointments/550e8400-e29b-41d4-a716-446655440100/complete \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "notes": "Patient is stable, advised to continue medication",
    "follow_up_required": true
  }'
```

### 7. Reschedule Appointment

**curl Command**:
```bash
curl -X PUT http://localhost:8000/api/appointments/550e8400-e29b-41d4-a716-446655440100/reschedule \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_date": "2024-02-20",
    "appointment_time": "15:00",
    "reason": "Doctor has prior commitment"
  }'
```

### 8. Get Patient Appointments

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/patients/550e8400-e29b-41d4-a716-446655440001/appointments \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 9. Get Doctor Schedule

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/doctors/550e8400-e29b-41d4-a716-446655440000/schedule?date_from=2024-02-01&date_to=2024-02-28" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

---

## 🏥 Medical Record Tests

### 1. Create Medical Record

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/medical-records \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "doctor_id": "550e8400-e29b-41d4-a716-446655440000",
    "diagnosis": "Type 2 Diabetes Mellitus",
    "symptoms": [
      "Excessive thirst",
      "Frequent urination",
      "Fatigue",
      "Blurred vision"
    ],
    "examination_findings": "Blood glucose 250 mg/dL, BMI 28.5",
    "treatment_plan": "Metformin 500mg twice daily, exercise, diet control",
    "medication_prescribed": [
      "Metformin 500mg",
      "Lisinopril 10mg"
    ],
    "notes": "Patient has family history of diabetes"
  }'
```

**Expected Response (201)**:
```json
{
  "status": "success",
  "message": "Medical record created successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440200",
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "doctor_id": "550e8400-e29b-41d4-a716-446655440000",
    "diagnosis": "Type 2 Diabetes Mellitus",
    "treatment_plan": "Metformin 500mg twice daily, exercise, diet control",
    "created_at": "2024-02-01T10:00:00Z"
  }
}
```

### 2. Get Patient Medical Records

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/patients/550e8400-e29b-41d4-a716-446655440001/medical-records \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 3. Create Prescription

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/prescriptions \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "doctor_id": "550e8400-e29b-41d4-a716-446655440000",
    "medication_name": "Metformin",
    "dosage": "500mg",
    "frequency": "Twice daily",
    "duration": "90 days",
    "instructions": "Take after meals with water",
    "is_refillable": true,
    "refill_count": 3,
    "expiry_date": "2024-05-01"
  }'
```

**Expected Response (201)**:
```json
{
  "status": "success",
  "message": "Prescription created successfully",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440300",
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "medication_name": "Metformin",
    "dosage": "500mg",
    "frequency": "Twice daily",
    "status": "active",
    "created_at": "2024-02-01T10:00:00Z"
  }
}
```

### 4. Get Patient Prescriptions

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/patients/550e8400-e29b-41d4-a716-446655440001/prescriptions \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 5. Create Lab Result

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/lab-results \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "test_type": "blood",
    "test_name": "Fasting Blood Glucose",
    "result_value": "250",
    "unit": "mg/dL",
    "normal_range": "70-100",
    "test_date": "2024-02-01",
    "notes": "Result shows hyperglycemia"
  }'
```

### 6. Create Clinical Note

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/clinical-notes \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": "550e8400-e29b-41d4-a716-446655440001",
    "doctor_id": "550e8400-e29b-41d4-a716-446655440000",
    "note_content": "Patient presents with symptoms of Type 2 Diabetes. Blood glucose level significantly elevated. Started on Metformin therapy. Patient educated on diet and exercise.",
    "note_type": "progress",
    "is_confidential": false
  }'
```

---

## 🧪 Error Handling Tests

### 1. Test Missing Authorization Header

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Content-Type: application/json"
```

**Expected Response (401)**:
```json
{
  "message": "Unauthenticated."
}
```

### 2. Test Invalid Token

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer invalid_token_xyz" \
  -H "Content-Type: application/json"
```

**Expected Response (401)**:
```json
{
  "status": "error",
  "message": "Invalid token format"
}
```

### 3. Test Invalid Data (Validation Error)

**curl Command**:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "invalid-email",
    "password": "short"
  }'
```

**Expected Response (422)**:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field must be a valid email."
    ],
    "password": [
      "The password field must be at least 8 characters."
    ],
    "password_confirmation": [
      "The password confirmation field is required."
    ]
  }
}
```

### 4. Test Not Found

**curl Command**:
```bash
curl -X GET http://localhost:8000/api/users/invalid-uuid \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (404)**:
```json
{
  "status": "error",
  "message": "User not found"
}
```

### 5. Test Connection Error (API Gateway Down)

**curl Command** (jika API Gateway down):
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

**Expected Response (500)**:
```json
{
  "status": "error",
  "message": "Failed to fetch users",
  "error": "Failed to connect to API Gateway at http://localhost:3000"
}
```

---

## 📊 Performance Testing

### 1. Get Users dengan Pagination

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/users?page=1&per_page=10" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 2. Search Users

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/users?search=john" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

### 3. Filter Appointments by Status

**curl Command**:
```bash
curl -X GET "http://localhost:8000/api/appointments?status=scheduled&doctor_id=uuid" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

---

## 🔄 Workflow Testing

### Complete User Journey Test:

```bash
# 1. Register new patient
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Patient Test","email":"test@example.com","password":"password123","password_confirmation":"password123","role":"patient"}'

# 2. Login sebagai patient
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
# Save token dari response

# 3. Get available doctors
curl -X GET "http://localhost:8000/api/users?role=doctor" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"

# 4. Get available slots for doctor
curl -X GET "http://localhost:8000/api/doctors/{doctor_id}/available-slots?date=2024-02-15" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"

# 5. Create appointment
curl -X POST http://localhost:8000/api/appointments \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{"patient_id":"...","doctor_id":"...","appointment_date":"2024-02-15","appointment_time":"14:30","reason":"Regular checkup","appointment_type":"checkup","duration_minutes":30}'

# 6. Login as doctor
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"doctor@meditrack.com","password":"password123"}'
# Save doctor token

# 7. Get today appointments for doctor
curl -X GET "http://localhost:8000/api/appointments/today" \
  -H "Authorization: Bearer $doctor_token" \
  -H "Content-Type: application/json"

# 8. Create medical record
curl -X POST http://localhost:8000/api/medical-records \
  -H "Authorization: Bearer $doctor_token" \
  -H "Content-Type: application/json" \
  -d '{"patient_id":"...","doctor_id":"...","diagnosis":"Healthy","symptoms":[],"treatment_plan":"Follow-up in 3 months"}'

# 9. Create prescription
curl -X POST http://localhost:8000/api/prescriptions \
  -H "Authorization: Bearer $doctor_token" \
  -H "Content-Type: application/json" \
  -d '{"patient_id":"...","doctor_id":"...","medication_name":"Vitamin C","dosage":"500mg","frequency":"Once daily","duration":"30 days"}'

# 10. Complete appointment
curl -X POST http://localhost:8000/api/appointments/{appointment_id}/complete \
  -H "Authorization: Bearer $doctor_token" \
  -H "Content-Type: application/json" \
  -d '{"notes":"Appointment completed successfully","follow_up_required":false}'

# 11. Pat...

 patient melihat medical records
curl -X GET "http://localhost:8000/api/patients/{patient_id}/medical-records" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"

# 12. Patient melihat prescriptions
curl -X GET "http://localhost:8000/api/patients/{patient_id}/prescriptions" \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"

# 13. Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json"
```

---

## 📝 Notes untuk Testing

1. **Token Expiry**: Token mungkin expire, perlu login ulang
2. **Time Zone**: Pastikan time zone server dan client sama
3. **UUIDs**: Generate UUIDs untuk patient_id, doctor_id, dll
4. **Dates**: Format dates sebagai ISO 8601 (YYYY-MM-DD)
5. **Concurrent Requests**: Test dengan banyak requests untuk check concurrency handling

---

## 🐛 Debugging

### 1. Enable Debug Mode

**Update .env**:
```
API_DEBUG=true
```

### 2. Check Logs

```bash
# Laravel logs
tail -f /d:/semester\ 6/APPL/UTS-RPL-APPL-11423018/uts/storage/logs/laravel.log
```

### 3. Check Microservices Logs

```bash
# Untuk setiap microservice, cek logs
# user-service logs di user-service/logs/
# appointment-service logs di appointment-service/logs/
# etc.
```

### 4. Test API Gateway Health

```bash
curl http://localhost:3000/health
```

### 5. Test Individual Service Health

```bash
curl http://localhost:3001/health  # User service
curl http://localhost:3002/health  # Appointment service
curl http://localhost:3003/health  # Medical service
curl http://localhost:3004/health  # Pharmacy service
curl http://localhost:3005/health  # Payment service
curl http://localhost:3006/health  # Analytics service
```

---

## ✅ Testing Checklist

- [ ] Microservices semua running dan sehat
- [ ] Database sudah migrate
- [ ] Laravel serve running
- [ ] Auth endpoints working (login/register)
- [ ] User management endpoints working
- [ ] Appointment endpoints working
- [ ] Medical record endpoints working
- [ ] Error handling working (401, 404, 422, 500)
- [ ] Token properly stored in session
- [ ] Retry logic working untuk failed requests
- [ ] Caching working untuk GET requests
- [ ] Complete workflow test success

---

## 💡 Tips

1. **Use Postman**: Import collection untuk lebih mudah
2. **Environment Variables**: Setup di Postman/curl untuk reusability
3. **Save Responses**: Simpan responses untuk reference
4. **Automate**: Script testing dengan bash atau Python
5. **Monitor Performance**: Catat response times
