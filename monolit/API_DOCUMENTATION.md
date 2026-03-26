# MediTrack API Documentation

## Overview
MediTrack adalah platform digital healthcare yang menghubungkan pasien, dokter, dan apotek. API ini dibangun dengan Laravel menggunakan arsitektur monolithic.

## Base URL
```
http://localhost:8000/api
```

## Authentication
Saat ini API menggunakan token-based authentication (dapat dikembangkan lebih lanjut).

---

## 📋 API Endpoints

### 1. USER MANAGEMENT (`/users`)

#### Get All Users
```
GET /users
Query Parameters:
  - role: patient|doctor|pharmacist|admin
  - status: active|inactive|suspended
  - search: string (search by name or email)
  - page: number (default: 1)

Response:
{
  "status": "success",
  "data": {
    "data": [...],
    "current_page": 1,
    "total": 10
  }
}
```

#### Create User
```
POST /users
Body:
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password",
  "phone": "08123456789",
  "role": "patient|doctor|pharmacist",
  "address": "Jl. Contoh No. 123",
  "specialty": "Umum" (required for doctors),
  "license_number": "XXX" (required for doctors),
  "insurance_provider": "BPJS" (required for patients)
}
```

#### Get User Details
```
GET /users/{userId}
Response includes related data: appointments, medical records, prescriptions, payments
```

#### Update User
```
PUT /users/{userId}
Body: (optional fields)
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "phone": "08987654321",
  "address": "New Address",
  "status": "active|inactive|suspended"
}
```

#### Delete User
```
DELETE /users/{userId}
```

#### Get All Doctors
```
GET /users/doctors/all
Returns all active doctors
```

#### Get All Patients
```
GET /users/patients/all
Returns all active patients
```

#### Get All Pharmacists
```
GET /users/pharmacists/all
Returns all active pharmacists
```

---

### 2. APPOINTMENT MANAGEMENT (`/appointments`)

#### Get All Appointments
```
GET /appointments
Query Parameters:
  - patient_id: number
  - doctor_id: number
  - status: scheduled|completed|cancelled|no-show
  - date_from: YYYY-MM-DD
  - date_to: YYYY-MM-DD
```

#### Create Appointment
```
POST /appointments
Body:
{
  "patient_id": 1,
  "doctor_id": 2,
  "appointment_date": "2024-04-20 14:00:00",
  "description": "Pemeriksaan rutin",
  "type": "consultation|follow-up|general-checkup",
  "location": "Ruang Pemeriksaan A",
  "duration": 30
}
```

#### Get Appointment Details
```
GET /appointments/{appointmentId}
```

#### Update Appointment
```
PUT /appointments/{appointmentId}
Body: (optional fields)
{
  "appointment_date": "2024-04-21 10:00:00",
  "description": "Updated description",
  "status": "scheduled|completed|cancelled|no-show"
}
```

#### Cancel Appointment
```
PUT /appointments/{appointmentId}/cancel
```

#### Reschedule Appointment
```
PUT /appointments/{appointmentId}/reschedule
Body:
{
  "appointment_date": "2024-04-25 15:00:00"
}
```

#### Get Appointments by Doctor
```
GET /appointments/doctor/{doctorId}
```

#### Get Appointments by Patient
```
GET /appointments/patient/{patientId}
```

---

### 3. MEDICAL RECORDS (`/medical-records`)

#### Get All Medical Records
```
GET /medical-records
Query Parameters:
  - patient_id: number
  - doctor_id: number
  - page: number
```

#### Create Medical Record
```
POST /medical-records
Body:
{
  "patient_id": 1,
  "doctor_id": 2,
  "appointment_id": 1,
  "diagnosis": "Demam",
  "treatment": "Istirahat dan minum obat",
  "lab_results": "Normal",
  "medications": "Paracetamol 500mg",
  "follow_up_date": "2024-05-01",
  "notes": "Kontrol ulang setelah 1 minggu"
}
```

#### Get Patient Medical History
```
GET /medical-records/patient/{patientId}/history
Returns all medical records for the patient sorted by date
```

#### Export Patient Records
```
GET /medical-records/patient/{patientId}/export
Export medical records in JSON format
```

---

### 4. PRESCRIPTIONS (`/prescriptions`)

#### Get All Prescriptions
```
GET /prescriptions
Query Parameters:
  - patient_id: number
  - doctor_id: number
  - status: active|completed|cancelled
  - page: number
```

#### Create Prescription
```
POST /prescriptions
Body:
{
  "patient_id": 1,
  "doctor_id": 2,
  "appointment_id": 1,
  "medication": "Paracetamol",
  "dosage": "500mg",
  "frequency": "2x sehari",
  "duration": 7,
  "instructions": "Diminum setelah makan",
  "expiry_date": "2024-06-25"
}
```

#### Get Prescriptions by Patient
```
GET /prescriptions/patient/{patientId}
```

#### Get Prescriptions by Doctor
```
GET /prescriptions/doctor/{doctorId}
```

#### Complete Prescription
```
PUT /prescriptions/{prescriptionId}/complete
```

#### Cancel Prescription
```
PUT /prescriptions/{prescriptionId}/cancel
```

---

### 5. PHARMACY MANAGEMENT (`/pharmacies`)

#### Get All Pharmacies
```
GET /pharmacies
Query Parameters:
  - status: active|inactive|suspended
  - search: string
  - page: number
```

#### Create Pharmacy
```
POST /pharmacies
Body:
{
  "name": "Apotek Sehat",
  "address": "Jl. Kesehatan No. 123",
  "phone": "021-xxxxxxxx",
  "email": "apotek@email.com",
  "license_number": "APT-001-2024",
  "manager_id": 1,
  "latitude": -6.2088,
  "longitude": 106.8456
}
```

#### Add Drug Stock
```
POST /pharmacies/{pharmacyId}/drug-stock
Body:
{
  "drug_name": "Paracetamol",
  "quantity": 100,
  "unit_price": 5000,
  "expiry_date": "2025-12-31",
  "manufacturer": "PT. Farmasi Indonesia",
  "batch_number": "BATCH-001"
}
```

#### Get Drug Stock
```
GET /pharmacies/{pharmacyId}/drug-stock
```

#### Update Drug Stock
```
PUT /pharmacies/{pharmacyId}/drug-stock/{drugStockId}
Body:
{
  "quantity": 150
}
```

#### Get Nearby Pharmacies
```
GET /pharmacies/nearby/search
Query Parameters:
  - latitude: number (required)
  - longitude: number (required)
  - distance: number (default: 5 km)
```

---

### 6. PAYMENTS & INSURANCE (`/payments`)

#### Get All Payments
```
GET /payments
Query Parameters:
  - patient_id: number
  - status: pending|completed|failed|refunded
  - method: credit_card|debit_card|bank_transfer|insurance
  - page: number
```

#### Create Payment
```
POST /payments
Body:
{
  "appointment_id": 1,
  "patient_id": 1,
  "amount": 150000,
  "method": "credit_card|debit_card|bank_transfer|insurance",
  "insurance_claim_id": null
}
```

#### Update Payment Status
```
PUT /payments/{paymentId}/status
Body:
{
  "status": "pending|completed|failed|refunded"
}
```

#### Refund Payment
```
PUT /payments/{paymentId}/refund
```

#### Create Insurance Claim
```
POST /payments/insurance/create-claim
Body:
{
  "patient_id": 1,
  "appointment_id": 1,
  "insurance_provider": "BPJS",
  "policy_number": "POL-123456",
  "claim_amount": 150000
}
```

#### Get Insurance Claims
```
GET /payments/insurance/claims
Query Parameters:
  - patient_id: number
  - status: pending|approved|rejected|paid
  - page: number
```

#### Payment Statistics
```
GET /payments/statistics/overview
Query Parameters:
  - date_from: YYYY-MM-DD
  - date_to: YYYY-MM-DD
```

---

### 7. ANALYTICS & REPORTING (`/analytics`)

#### Dashboard Overview
```
GET /analytics/dashboard/overview
Returns: users count, appointments, revenue, prescriptions
```

#### Patient Outcomes Analytics
```
GET /analytics/patients/outcomes
```

#### Doctor Performance Analytics
```
GET /analytics/doctors/performance
Returns: appointment stats, completion rate, specialties
```

#### Drug Usage Trends
```
GET /analytics/drugs/usage-trends
Returns: top medications, usage frequency
```

#### Revenue Analytics
```
GET /analytics/revenue/analytics
Query Parameters:
  - date_from: YYYY-MM-DD
  - date_to: YYYY-MM-DD
```

#### User Statistics
```
GET /analytics/users/statistics
```

#### Appointment Statistics
```
GET /analytics/appointments/statistics
Query Parameters:
  - date_from: YYYY-MM-DD
  - date_to: YYYY-MM-DD
```

#### Prescription Statistics
```
GET /analytics/prescriptions/statistics
```

#### Insurance Claim Statistics
```
GET /analytics/insurance/statistics
```

#### Activity Logs
```
GET /analytics/logs/activity
Query Parameters:
  - event_type: string
  - entity_type: string
  - page: number
```

---

## Response Format

Success Response (200):
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": { ... }
}
```

Error Response (4xx/5xx):
```json
{
  "status": "error",
  "message": "Error description"
}
```

Validation Error (422):
```json
{
  "status": "error",
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## Pagination
Endpoints yang mengembalikan banyak data menggunakan pagination dengan parameter:
- `page`: Nomor halaman (default: 1)
- `per_page`: Jumlah item per halaman (default: 15)

Response pagination:
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 10,
  "per_page": 15,
  "total": 150
}
```

---

## Installation & Setup

### Prerequisites
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL/MariaDB
- Node.js (untuk frontend assets)

### Setup Steps
```bash
# Clone repository
git clone <repository-url>
cd meditrack

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meditrack
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

---

## Testing
```bash
# Run tests
php artisan test

# Run tests dengan coverage
php artisan test --coverage
```

---

## Error Codes
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 500: Internal Server Error

---

## Best Practices
1. Selalu gunakan parameter pagination untuk mengambil daftar data
2. Validasi input data sebelum dikirim ke server
3. Gunakan authentication token pada request yang memerlukan autentikasi
4. Handle error response dengan baik pada client
5. Cache hasil request yang tidak berubah sering

---

## Support & Contribution
Untuk pertanyaan atau kontribusi, hubungi tim development MediTrack.
