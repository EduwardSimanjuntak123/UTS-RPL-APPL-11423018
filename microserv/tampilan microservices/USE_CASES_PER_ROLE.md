# USE CASE DIAGRAM & SPESIFIKASI MEDITRACK

**MediTrack Transformation - Case Study 2**  
**Date**: March 26, 2026  
**Focus**: Use Cases untuk Setiap Role/Actor

---

## 📋 DAFTAR ROLE/ACTOR

1. **Patient (Pasien)**
2. **Doctor (Dokter)**
3. **Pharmacy Staff (Staf Farmasi)**
4. **Admin (Administrator)**
5. **Manager/Analytics User (Manajer)**
6. **Payment Processor (Processor Pembayaran)**

---

## 1️⃣ PATIENT (PASIEN) - USE CASES

### System Overview untuk Patient

```
┌─────────────────────────────────────────────────────────┐
│                    MEDITRACK SYSTEM                      │
│                                                           │
│  ┌──────────────┐                                        │
│  │   PATIENT    │                                        │
│  └──────────────┘                                        │
│          │                                               │
│         ╔╩═══════════════════════════════════════╗       │
│         ║                                         ║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ Register &  │  │   Schedule   │  │ View   ║║       │
│    │   Login     │  │ Appointment  │  │ Medical║║       │
│    └─────────────┘  └──────────────┘  │Records ║║       │
│         │                 │              │      ║║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ View Profile│  │   Reschedule │  │ Refill ║║       │
│    │ & Settings  │  │ Appointment  │  │Prescrip║tion    │
│    └─────────────┘  └──────────────┘  └────────┘║       │
│         │                 │              │      ║║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ Update      │  │Cancel        │  │Download║║       │
│    │ Contact Info│  │Appointment   │  │Medical ║║       │
│    │ & Password  │  │              │  │Report  ║║       │
│    └─────────────┘  └──────────────┘  └────────┘║       │
│         │                 │              │      ║║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ Manage      │  │  View Waiting│  │ Pay    ║║       │
│    │ Insurance   │  │  Time & Queue│  │ Invoice║║       │
│    │ Information │  │              │  │        ║║       │
│    └────────────┘  └──────────────┘  └────────┘║       │
│         │                 │              │      ║║       │
│         └─────────────────┴──────────────┴──────╚╝       │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

### Use Case: UC-P1 - Register & Login

**Actor**: Patient (Unregistered/Registered)

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Pasien dapat membuat akun baru dan login ke sistem |
| **Precondition** | Pasien belum terdaftar atau sudah terdaftar |
| **Postcondition** | Pasien berhasil login dan dapat mengakses dashboard |

**Main Flow**:
```
1. Pasien membuka aplikasi MediTrack
2. Pasien memilih "Register" atau "Login"
3. [Register Path]
   - Masukkan email, password, nama lengkap
   - Sistem validasi email unik
   - Sistem hash password dengan bcrypt
   - Email verifikasi dikirim
   - Pasien verifikasi email
   - Akun aktif
4. [Login Path]
   - Masukkan email & password
   - Sistem validasi credentials
   - JWT token dihasilkan
   - Pasien redirect ke dashboard
```

**Alternatif Flow**:
```
- Email sudah terdaftar → Error message
- Password < 8 karakter → Error message
- Email verification expired → Resend verification link
- Login gagal 3x → Lock account 30 menit
```

**Associated Microservice**: User Service (Port 3001)

**API Endpoints**:
```
POST   /api/users/register
POST   /api/users/login
POST   /api/users/verify-email
POST   /api/users/resend-verification
```

---

### Use Case: UC-P2 - Schedule Appointment

**Actor**: Patient

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Pasien dapat menjadwalkan appointment dengan dokter |
| **Precondition** | Pasien sudah login, dokter tersedia |
| **Postcondition** | Appointment berhasil dibuat, konfirmasi dikirim |

**Main Flow**:
```
1. Pasien login & navigasi ke "Schedule Appointment"
2. Sistem load daftar dokter & spesialisasi
3. Pasien memilih dokter
4. Sistem tampilkan available time slots (next 30 days)
5. Pasien memilih date & time
6. Pasien masukkan reason appointment (opsional notes)
7. Sistem validasi:
   - Slot masih available
   - Tidak ada conflict
   - Pasien belum punya appointment di waktu yang sama
8. Appointment dibuat dengan status "PENDING"
9. Sistem kirim email konfirmasi ke pasien & dokter
10. Pasien lanjut ke payment (jika diperlukan)
```

**Alternatif Flow**:
```
- Slot penuh → Suggest alternative times
- Dokter offline → Show next available day
- Appointment conflicts → Warning message
- User cancels → Go back to main menu
```

**Associated Microservice**: Appointment Service (Port 3002)

**API Endpoints**:
```
GET    /api/doctors                              → List doctors
GET    /api/doctors/{doctor_id}/availability     → Available slots
POST   /api/appointments                          → Create appointment
GET    /api/appointments/{appointment_id}        → Get appointment details
```

---

### Use Case: UC-P3 - View Medical Records

**Actor**: Patient

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Pasien dapat melihat riwayat medis, resep, & hasil lab |
| **Precondition** | Pasien sudah login, memiliki medical records |
| **Postcondition** | Medical records ditampilkan dengan aman |

**Main Flow**:
```
1. Pasien login & navigasi ke "Medical Records"
2. Sistem load:
   - Medical history
   - Recent prescriptions
   - Lab results
   - Doctor notes
3. Pasien dapat filter by date range
4. Pasien dapat download records (PDF format)
5. Sistem log akses untuk audit trail
```

**UI Display**:
```
┌────────────────────────────────────┐
│ MEDICAL RECORDS                    │
├────────────────────────────────────┤
│ Filter: [From Date] [To Date] [Go] │
├────────────────────────────────────┤
│                                    │
│ PRESCRIPTIONS (Last 6 months)     │
│ └─ Amoxicillin 500mg         │    │
│    Date: 2026-03-20          │    │
│    Status: [Active]          │    │
│    Refills Left: 2           │    │
│                                    │
│ LAB RESULTS (Last Year)           │
│ └─ Blood Test             │        │
│    Date: 2026-03-15       │        │
│    Status: [Ready]        │        │
│    [View Details]         │        │
│                                    │
│ MEDICAL HISTORY                   │
│ └─ Diabetes Type 2        │        │
│    Diagnosed: 2020        │        │
│    Status: [Active]       │        │
│                                    │
│ [Download All Records] [Print]    │
└────────────────────────────────────┘
```

**Associated Microservice**: Medical Service (Port 3003)

**API Endpoints**:
```
GET    /api/medical-records/{patient_id}    → Get all records
GET    /api/prescriptions/{patient_id}      → Get prescriptions
GET    /api/lab-results/{patient_id}        → Get lab results
GET    /api/medical-records/{patient_id}/download → Download PDF
```

---

### Use Case: UC-P4 - Reschedule/Cancel Appointment

**Actor**: Patient

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Pasien dapat mengubah atau membatalkan appointment |
| **Precondition** | Appointment exists dan status PENDING/CONFIRMED |
| **Postcondition** | Appointment updated/cancelled, notifikasi dikirim |

**Main Flow - Reschedule**:
```
1. Pasien navigasi ke "My Appointments"
2. Pilih appointment untuk reschedule
3. Sistem tampilkan available slots
4. Pasien pilih slot baru
5. Sistem validasi tidak ada conflict
6. Appointment diupdate
7. Notifikasi dikirim ke pasien & dokter
```

**Main Flow - Cancel**:
```
1. Pasien pilih appointment
2. Klik "Cancel Appointment"
3. Enter reason untuk pembatalan
4. Sistem request konfirmasi 2x
5. Appointment dibatalkan
6. Refund diproses (jika payment sudah dilakukan)
7. Notifikasi dikirim
```

**Constraints**:
```
- Dapat reschedule hingga 24 jam sebelum appointment
- Dapat cancel hingga 24 jam sebelum appointment
- Late cancellation (< 24 jam) → Charge 50% dari biaya
- Cancellation < 2 jam → No refund
```

**Associated Microservice**: Appointment Service (Port 3002), Payment Service (Port 3005)

**API Endpoints**:
```
PUT    /api/appointments/{appointment_id}    → Reschedule
DELETE /api/appointments/{appointment_id}    → Cancel
POST   /api/refunds                           → Process refund
```

---

### Use Case: UC-P5 - Refill Prescription

**Actor**: Patient

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Pasien dapat request refill resep obat |
| **Precondition** | Prescription ada & masih valid, refills tersisa |
| **Postcondition** | Refill request dikirim ke farmasi |

**Main Flow**:
```
1. Pasien navigasi ke "My Prescriptions"
2. Lihat active prescriptions dengan refills count
3. Klik "Request Refill" pada resep terpilih
4. Pilih pickup method:
   - Ambil langsung di farmasi
   - Delivery ke alamat (jika available)
5. Input special instructions (opsional)
6. Konfirmasi request
7. Sistem kirim ke pharmacy service
8. Pharmacy staff fulfill request dalam 24 jam
9. Notifikasi dikirim ke pasien
```

**UI Display**:
```
┌──────────────────────────────────┐
│ MY PRESCRIPTIONS                 │
├──────────────────────────────────┤
│ Amoxicillin 500mg               │
│ Prescribed: 2026-03-20          │
│ Dosage: 3x daily for 7 days    │
│ Refills Left: 2                │
│ Status: [Active]               │
│ [Request Refill] [View Details]│
└──────────────────────────────────┘
```

**Associated Microservice**: Medical Service (Port 3003), Pharmacy Service (Port 3004)

**API Endpoints**:
```
GET    /api/prescriptions/{patient_id}           → Get prescriptions
POST   /api/prescriptions/{prescription_id}/refill → Request refill
PUT    /api/prescriptions/{prescription_id}       → Update status
```

---

### Use Case: UC-P6 - Pay Invoice

**Actor**: Patient

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Pasien dapat membayar invoice/tagihan |
| **Precondition** | Invoice ada dengan status UNPAID |
| **Postcondition** | Payment berhasil, invoice marked PAID |

**Main Flow**:
```
1. Pasien navigasi ke "Invoices & Billing"
2. Lihat list invoices dengan status
3. Klik invoice untuk detail
4. Klik "Pay Now"
5. Pilih payment method:
   - Credit Card
   - Debit Card
   - Bank Transfer
   - Insurance Claim
6. Untuk credit card:
   - Enter card details
   - Enter CVV
   - Enter billing address
7. Sistem proses payment gateway
8. [Success]
   - Payment confirmed
   - Receipt generated
   - Email sent
   - Invoice status → PAID
9. [Failed]
   - Error message
   - Option retry atau change payment method
```

**Security**:
```
- HTTPS only
- PCI DSS compliant
- No card details stored
- 3D Secure untuk transaksi besar
```

**Associated Microservice**: Payment Service (Port 3005)

**API Endpoints**:
```
GET    /api/invoices/{user_id}              → Get invoices
GET    /api/invoices/{invoice_id}           → Invoice details
POST   /api/payments                         → Process payment
GET    /api/payments/{payment_id}/receipt   → Download receipt
```

---

## 2️⃣ DOCTOR (DOKTER) - USE CASES

### System Overview untuk Doctor

```
┌─────────────────────────────────────────────────────────┐
│                    MEDITRACK SYSTEM                      │
│                                                           │
│  ┌──────────────┐                                        │
│  │    DOCTOR    │                                        │
│  └──────────────┘                                        │
│          │                                               │
│         ╔╩═══════════════════════════════════════╗       │
│         ║                                         ║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ View        │  │   Manage     │  │ Create ║║       │
│    │ Appointments│  │ Availability │  │Medical ║║       │
│    │             │  │              │  │Records ║║       │
│    └─────────────┘  └──────────────┘  └────────┘║       │
│         │                 │              │      ║║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ Update      │  │   Write      │  │ View   ║║       │
│    │ Appointment │  │  Prescription│  │Medical ║║       │
│    │ Status      │  │              │  │History ║║       │
│    └─────────────┘  └──────────────┘  └────────┘║       │
│         │                 │              │      ║║       │
│    ┌────▼────────┐  ┌──────────────┐  ┌────────┐║       │
│    │ View Patient│  │ Refer to     │  │  View  ║║       │
│    │ Medical Info│  │ Specialist   │  │Patient ║║       │
│    │             │  │              │  │Notes   ║║       │
│    └─────────────┘  └──────────────┘  └────────┘║       │
│         │                 │              │      ║║       │
│         └─────────────────┴──────────────┴──────╚╝       │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

### Use Case: UC-D1 - View Appointments Schedule

**Actor**: Doctor

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Dokter dapat melihat jadwal appointment |
| **Precondition** | Dokter sudah login |
| **Postcondition** | Appointment schedule ditampilkan |

**Main Flow**:
```
1. Dokter login ke dashboard
2. Sistem tampilkan calendar view:
   - Today's appointments
   - Upcoming appointments (next 7 days)
3. Dokter dapat filter:
   - By date
   - By status (confirmed, pending, completed)
   - By patient name
4. Klik appointment untuk melihat detail:
   - Patient info
   - Appointment time & reason
   - Patient medical history
   - Previous notes
```

**UI Display**:
```
┌──────────────────────────────────┐
│ TODAY'S SCHEDULE (March 26, 2026)│
├──────────────────────────────────┤
│ 09:00 - John Doe (General Check) │
│        Reason: Flu symptoms      │
│        Status: [Confirmed]       │
│        [Start Appointment]       │
│                                  │
│ 10:30 - Jane Smith (Checkup)     │
│         Reason: Annual checkup   │
│         Status: [Confirmed]      │
│         [Start Appointment]      │
│                                  │
│ 12:00 - LUNCH BREAK             │
│                                  │
│ 14:00 - Bob Johnson (Follow-up)  │
│         Reason: Diabetes review  │
│         Status: [Pending]        │
│         [Confirm] [Reschedule]   │
└──────────────────────────────────┘
```

**Associated Microservice**: Appointment Service (Port 3002)

**API Endpoints**:
```
GET    /api/appointments/doctor/{doctor_id}     → Get doctor appointments
GET    /api/appointments/{appointment_id}       → Appointment details
PUT    /api/appointments/{appointment_id}       → Update appointment
```

---

### Use Case: UC-D2 - Create Medical Record

**Actor**: Doctor

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Dokter membuat catatan medis & diagnosis |
| **Precondition** | Appointment berlangsung atau sudah selesai |
| **Postcondition** | Medical record disimpan ke database |

**Main Flow**:
```
1. Dokter klik "Start Appointment" atau "Create Medical Record"
2. Sistem load form dengan:
   - Patient info (read-only)
   - Chief complaint
   - Vital signs
   - Diagnosis
   - Assessment
   - Treatment plan
   - Medications (jika perlu)
   - Follow-up notes
3. Dokter fill form:
   - Include vital signs dari perawat/patient
   - Input diagnosis codes (ICD-10)
   - Detailed assessment
   - Treatment recommendations
4. Dokter dapat attach:
   - Lab results
   - Imaging scans
   - Test results
5. Konfirmasi & Save
6. Sistem generate medical record ID
7. Notifikasi dikirim ke patient
```

**Medical Record Structure**:
```json
{
  "id": "MR001",
  "patient_id": "USR001",
  "doctor_id": "USR002",
  "appointment_id": "APT001",
  "visit_date": "2026-03-26T10:00:00Z",
  "chief_complaint": "Flu symptoms",
  "vital_signs": {
    "temperature": 38.5,
    "blood_pressure": "120/80",
    "heart_rate": 72,
    "respiratory_rate": 16
  },
  "diagnosis": {
    "primary": "Influenza A",
    "icd_code": "J11.1",
    "severity": "moderate"
  },
  "assessment": "Patient presents with...",
  "treatment_plan": "Rest, fluids, paracetamol...",
  "medications": [],
  "follow_up": "Return in 3 days if symptoms persist",
  "attachments": []
}
```

**Associated Microservice**: Medical Service (Port 3003)

**API Endpoints**:
```
POST   /api/medical-records               → Create medical record
PUT    /api/medical-records/{record_id}   → Update record
GET    /api/medical-records/{patient_id}  → Get patient records
```

---

### Use Case: UC-D3 - Write Prescription

**Actor**: Doctor

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Dokter dapat menulis resep obat |
| **Precondition** | Medical record dibuat, diagnosis ditentukan |
| **Postcondition** | Prescription tersimpan & dikirim ke pharmacy |

**Main Flow**:
```
1. Dalam medical record form, dokter klik "Add Prescription"
2. Sistem buka prescription form:
   - Drug search (autocomplete dari drug database)
   - Select drug
   - Enter dosage
   - Enter frequency
   - Enter duration
   - Add refill count
   - Special instructions
3. Dokter dapat add multiple drugs
4. Sistem validasi:
   - No drug interactions
   - No allergy conflicts (check patient profile)
   - Dosage appropriate untuk patient age/weight
5. Konfirmasi prescription
6. Sistem generate prescription ID
7. Prescription dikirim ke pharmacy & patient
```

**Prescription Example**:
```
┌────────────────────────────────────┐
│ PRESCRIPTION                       │
├────────────────────────────────────┤
│ Patient: John Doe                  │
│ Date: 2026-03-26                   │
│ Doctor: Dr. Jane Smith             │
│                                    │
│ Rx1: Amoxicillin                   │
│      Dosage: 500mg                 │
│      Frequency: 3x daily           │
│      Duration: 7 days              │
│      Refills: 0                    │
│                                    │
│ Rx2: Paracetamol                   │
│      Dosage: 500mg                 │
│      Frequency: As needed (max 3x) │
│      Duration: 10 days             │
│      Refills: 1                    │
│                                    │
│ Instructions: Take with food       │
│                                    │
│ Signature: ________________         │
└────────────────────────────────────┘
```

**Associated Microservice**: Medical Service (Port 3003), Pharmacy Service (Port 3004)

**API Endpoints**:
```
POST   /api/prescriptions                  → Create prescription
GET    /api/drugs                          → Search drugs
POST   /api/prescriptions/{id}/pharmacies  → Send to pharmacy
```

---

### Use Case: UC-D4 - Manage Availability

**Actor**: Doctor

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Dokter dapat mengatur jadwal ketersediaan |
| **Precondition** | Dokter sudah login |
| **Postcondition** | Availability updated, patients dapat lihat |

**Main Flow**:
```
1. Dokter navigasi ke "My Availability"
2. Sistem tampilkan calendar dengan availability settings
3. Setup recurring availability:
   - Select days (Mon-Fri)
   - Set working hours (09:00-17:00)
   - Set appointment duration (30 min)
   - Set lunch break (12:00-13:00)
4. Add blocked times:
   - Conferences
   - Training
   - Personal time
   - Off-hours
5. Konfirmasi setting
6. Sistem update availability
7. Patients dapat lihat updated slots
```

**Associated Microservice**: Appointment Service (Port 3002)

**API Endpoints**:
```
GET    /api/doctors/{doctor_id}/availability      → Get availability
PUT    /api/doctors/{doctor_id}/availability      → Update availability
POST   /api/doctors/{doctor_id}/blocked-times     → Add blocked time
```

---

## 3️⃣ PHARMACY STAFF - USE CASES

### System Overview untuk Pharmacy

```
┌─────────────────────────────────────────────────────────┐
│                    MEDITRACK SYSTEM                      │
│                                                           │
│  ┌──────────────────┐                                    │
│  │ PHARMACY STAFF   │                                    │
│  └──────────────────┘                                    │
│            │                                             │
│           ╔╩═════════════════════════════════════╗       │
│           ║                                       ║       │
│      ┌────▼────────┐  ┌──────────────┐  ┌────────┐║     │
│      │ View Pending│  │  Fulfill     │  │ Manage ║║     │
│      │ Prescriptions   │Prescriptions │  │ Stock  ║║     │
│      └─────────────┘  └──────────────┘  └────────┘║     │
│            │                 │              │     ║║     │
│      ┌────▼────────┐  ┌──────────────┐  ┌────────┐║     │
│      │ Verify Drug │  │  Create Drug │  │ Low    ║║     │
│      │ Allocation  │  │  Orders      │  │ Stock  ║║     │
│      │             │  │              │  │Alerts  ║║     │
│      └─────────────┘  └──────────────┘  └────────┘║     │
│            │                 │              │     ║║     │
│            └─────────────────┴──────────────┴─────╚╝     │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

### Use Case: UC-PH1 - View Pending Prescriptions

**Actor**: Pharmacy Staff

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Staf farmasi dapat melihat resep yang perlu dipenuhi |
| **Precondition** | Prescriptions ada dalam sistem |
| **Postcondition** | Prescription list ditampilkan |

**Main Flow**:
```
1. Staf farmasi login ke pharmacy dashboard
2. Sistem tampilkan queue prescriptions:
   - Pending fulfillment
   - Awaiting verification
   - Ready for pickup
   - Ready for delivery
3. Filter by:
   - Status
   - Patient name
   - Date
   - Priority
4. Click prescription untuk lihat detail:
   - Patient info
   - Drug details
   - Quantity needed
   - Special instructions
   - Allergy warnings
```

**Associated Microservice**: Pharmacy Service (Port 3004), Medical Service (Port 3003)

**API Endpoints**:
```
GET    /api/drug-orders                      → Get pending orders
GET    /api/drug-orders/{order_id}           → Order details
PUT    /api/drug-orders/{order_id}/status    → Update status
```

---

### Use Case: UC-PH2 - Fulfill & Dispense Prescription

**Actor**: Pharmacy Staff

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Verifikasi & dispensing obat sesuai resep |
| **Precondition** | Prescription ada, drug available |
| **Postcondition** | Drug dispensed, inventory updated |

**Main Flow**:
```
1. Staf ambil prescription dari queue
2. Cek drug availability di stock
3. Verify drug:
   - Drug name
   - Dosage
   - Quantity
   - Expiry date
4. Print label dengan:
   - Patient name
   - Drug name & dosage
   - Instructions
   - Warnings
5. Prepare drug:
   - Measure correct quantity
   - Package secara proper
   - Label clearly
6. Quality check
7. Update status → "READY_FOR_PICKUP"
8. Notifikasi ke patient
9. Record dalam inventory system
```

**Associated Microservice**: Pharmacy Service (Port 3004)

**API Endpoints**:
```
PUT    /api/drug-orders/{order_id}              → Update fulfillment
POST   /api/drug-stocks/{drug_id}/deduct        → Reduce stock
GET    /api/drugs/{drug_id}/details             → Drug info
```

---

### Use Case: UC-PH3 - Manage Inventory/Stock

**Actor**: Pharmacy Staff

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Monitoring & maintain drug stock levels |
| **Precondition** | Inventory system access |
| **Postcondition** | Stock levels accurate & maintained |

**Main Flow**:
```
1. Staf navigasi ke "Inventory Management"
2. Sistem tampilkan:
   - Current stock levels
   - Low stock items (< reorder level)
   - Expired items
   - Recently added items
3. Daily tasks:
   - Check stock levels
   - Verify expiry dates
   - Remove expired drugs
   - Update quantities
4. Reordering:
   - When stock < reorder_level
   - Review suggested quantities
   - Create purchase order
   - Send ke supplier
5. Receiving:
   - Verify received drugs
   - Check expiry & quality
   - Update stock in system
```

**Stock Management UI**:
```
┌────────────────────────────────────┐
│ INVENTORY MANAGEMENT               │
├────────────────────────────────────┤
│ Total Items: 245                   │
│ Low Stock Items: 12                │
│ Expired: 0                          │
│                                    │
│ CRITICAL (Stock < 10)             │
│ □ Amoxicillin 500mg             │
│   Current: 5 units              │
│   Reorder: 100 units            │
│   [Create Order]                │
│                                    │
│ MEDIUM (Stock 10-50)              │
│ □ Paracetamol 500mg             │
│   Current: 30 units             │
│   Reorder: 50 units             │
│                                    │
│ [+ New Entry] [Export Report]    │
└────────────────────────────────────┘
```

**Associated Microservice**: Pharmacy Service (Port 3004)

**API Endpoints**:
```
GET    /api/drug-stocks                         → View stock
PUT    /api/drug-stocks/{drug_id}               → Update stock
POST   /api/drug-orders                         → Create order
GET    /api/drugs/low-stock                     → Low stock alert
```

---

## 4️⃣ ADMINISTRATOR - USE CASES

### Use Case: UC-A1 - User Management

**Actor**: Admin

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Admin dapat manage user accounts |
| **Precondition** | Admin login |
| **Postcondition** | User updated/activated/deactivated |

**Main Flow**:
```
1. Admin navigasi ke "User Management"
2. Sistem tampilkan user list dengan:
   - User ID
   - Name
   - Email
   - Role
   - Status (Active/Inactive)
   - Created date
3. Admin dapat:
   - View user details
   - Edit user info
   - Change user role
   - Activate/Deactivate account
   - Reset password
   - Delete account (soft delete)
4. Action logged untuk audit
```

**Associated Microservice**: User Service (Port 3001)

**API Endpoints**:
```
GET    /api/admin/users                    → List all users
GET    /api/admin/users/{user_id}          → User details
PUT    /api/admin/users/{user_id}          → Update user
DELETE /api/admin/users/{user_id}          → Deactivate user
POST   /api/admin/users/{user_id}/reset-password → Reset password
```

---

### Use Case: UC-A2 - System Configuration

**Actor**: Admin

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Admin dapat configure system settings |
| **Precondition** | Admin login |
| **Postcondition** | Settings updated |

**Main Flow**:
```
1. Admin navigasi ke "System Configuration"
2. Edit settings untuk:
   - Appointment duration (default 30 min)
   - Cancellation policy
   - Payment gateway
   - Email notifications
   - Backup schedule
   - Security policies
3. Konfirmasi & save changes
4. System apply pada semua services
```

---

## 5️⃣ MANAGER/ANALYTICS USER - USE CASES

### Use Case: UC-M1 - View Dashboard & Reports

**Actor**: Manager

| Aspek | Deskripsi |
|-------|-----------|
| **Goal** | Manager dapat lihat metrics & generate reports |
| **Precondition** | Manager login |
| **Postcondition** | Dashboard & reports displayed |

**Main Flow**:
```
1. Manager login ke analytics dashboard
2. Sistem tampilkan KPI dashboard:
   - Total appointments (today/month/year)
   - Total revenue
   - Prescription fulfilled rate
   - Patient satisfaction score
   - System uptime
3. Manager dapat:
   - Filter by date range
   - Filter by department/doctor
   - Generate custom reports
   - Export data (CSV/PDF)
   - Schedule recurring reports
```

**Dashboard Metrics**:
```
┌────────────────────────────────────┐
│ MEDITRACK ANALYTICS DASHBOARD      │
├────────────────────────────────────┤
│                                    │
│ TODAY'S METRICS:                  │
│ ┌──────────────────────────────┐  │
│ │ Appointments: 42             │  │
│ │ Revenue: $3,150              │  │
│ │ Avg Wait Time: 15 min        │  │
│ │ Patient Satisfaction: 4.8/5  │  │
│ └──────────────────────────────┘  │
│                                    │
│ THIS MONTH:                        │
│ ┌──────────────────────────────┐  │
│ │ Total Appointments: 820      │  │
│ │ Unique Patients: 380         │  │
│ │ Revenue: $45,200             │  │
│ │ Rx Fulfilled: 95%            │  │
│ └──────────────────────────────┘  │
│                                    │
│ [Generate Report] [Export CSV]    │
└────────────────────────────────────┘
```

**Associated Microservice**: Analytics Service (Port 3006)

**API Endpoints**:
```
GET    /api/metrics                 → Get metrics
GET    /api/reports/{report_type}   → Generate reports
POST   /api/reports                 → Custom report
GET    /api/health                  → System health
```

---

## 📊 SUMMARY - ROLE VS USE CASES

| Role | Key Use Cases | Services Used |
|------|--------------|----------------|
| **Patient** | Register, Schedule, View Records, Pay | User, Appointment, Medical, Payment |
| **Doctor** | View Schedule, Create Records, Write Rx | Appointment, Medical |
| **Pharmacy** | View Rx, Fulfill, Manage Stock | Pharmacy, Medical |
| **Admin** | User Mgmt, Config | User, All Services |
| **Manager** | Dashboard, Reports | Analytics |

---

## 🔄 CROSS-ROLE INTERACTIONS

```
Patient schedules appointment
    ↓
Doctor reviews appointment
    ↓
Doctor writes prescription during/after appointment
    ↓
Pharmacy staff receives prescription
    ↓
Patient picks up medication
    ↓
Manager reviews metrics
    ↓
Admin monitors system health
```

---

**Status**: Use Case diagram & specifications complete untuk semua roles.

