# 🌱 MediTrack Microservices Database Seeder

Dokumentasi lengkap mengenai seeding data untuk semua microservices di MediTrack.

## 📋 Daftar Seeder

### 1. **User Service Seeder** (`user-service/internal/seeders/seeder.go`)

Membuat data pengguna dengan berbagai role:

#### Data yang di-seed:
- **2 Admin Users**
  - Email: admin@meditrack.com, admin2@meditrack.com
  - Role: admin
  - Status: active

- **5 Doctors**
  - Dr. Budi Hartono, Dr. Siti Nurhaliza, Dr. Ahmad Rizki, Dr. Eka Putri, Dr. Rinto Harahap
  - Specializations: Kardiologi, Neurologi, Umum, Penyakit Dalam, Bedah
  - License Numbers: DR01001 - DR01005
  - Consultation Fee: 100,000 - 300,000 IDR

- **3 Departments**
  - Kardiologi (Jantung dan Pembuluh Darah)
  - Neurologi (Saraf)
  - Umum (Praktik Umum)

- **10 Patients**
  - Pasien 1 - Pasien 10
  - Medical Numbers: MED05001 - MED05010
  - Gender: Mixed (M/F)
  - Blood Types: A, B, AB, O

- **3 Pharmacists**
  - Apt. Budi Setiawan, Apt. Siti Handayani, Apt. Rudi Gunawan
  - License Numbers: APT08001 - APT08003

#### Credentials Default:
```
Admin:
  Email: admin@meditrack.com
  Password: password123
  
Doctor:
  Email: dr.budi@meditrack.com (atau sesuai nama dokter)
  Password: password123
  
Patient:
  Email: pasien1@meditrack.com (atau sesuai nama pasien)
  Password: password123
  
Pharmacist:
  Email: apt.budi@meditrack.com (atau sesuai nama apoteker)
  Password: password123
```

---

### 2. **Appointment Service Seeder** (`appointment-service/internal/seeders/seeder.go`)

Data janji temu dan rekam medis pasien.

#### Data yang di-seed:
- **20 Appointments**
  - Types: consultation, follow-up, general-checkup, emergency
  - Statuses: scheduled, completed, cancelled, no-show
  - Durations: 30, 45, 60 menit
  - Locations: Ruang Pemeriksaan A, B, C

- **Medical Records** (untuk setiap appointment yang completed)
  - Diagnoses: Demam Biasa, Sakit Gigi, Kolesterol Tinggi, Hipertensi, Diabetes, Asma, GERD, Migren
  - Medications: Paracetamol, Ibuprofen, Amoxicillin, Metformin, dll
  - Lab Results: Normal, Abnormal
  - Follow-up dates: Otomatis 7 hari setelah pemeriksaan

#### Relationships:
- Setiap appointment terhubung ke patient dan doctor dari user-service
- Medical records otomatis dibuat untuk appointment yang selesai

---

### 3. **Pharmacy Service Seeder** (`pharmacy-service/internal/seeders/seeder.go`)

Data apotek, obat, dan stok farmasi.

#### Data yang di-seed:
- **3 Pharmacies**
  - Apotek Sehat Sejahtera (Jl. Merdeka No. 123, Jakarta)
  - Apotek Mitra Kesehatan (Jl. Sudirman No. 456, Jakarta)
  - Apotek Farmasi Modern (Jl. Gatot Subroto No. 789, Jakarta)
  - License Numbers: APT-001-2024, APT-002-2024, APT-003-2024

- **8 Drugs** dengan informasi lengkap:
  - Paracetamol 500mg, Ibuprofen 400mg, Amoxicillin 500mg, Metformin 500mg
  - Lisinopril 10mg, Omeprazol 20mg, Cetirizine 10mg, Vitamin C 1000mg
  - Setiap obat memiliki category, manufacturer, dan description

- **Drug Stocks** (24 entries = 3 pharmacies × 8 drugs)
  - Quantity: 50-300 units
  - Unit prices: 5,000 - 40,000 IDR
  - Expiry dates: 1 tahun ke depan
  - Batch numbers: Unik untuk setiap lot

- **15 Prescription Orders**
  - Statuses: pending, ready, completed, cancelled
  - Total prices: 75,000 IDR (default)
  - References ke patient dan pharmacy

---

### 4. **Payment Service Seeder** (`payment-service/internal/seeders/seeder.go`)

Data pembayaran, invoice, dan klaim asuransi.

#### Data yang di-seed:
- **15 Payments**
  - Methods: credit card, bank transfer, cash, insurance
  - Amounts: 100,000 - 750,000 IDR
  - Reference numbers: REF-XXXXXXXX (unik)
  - Statuses: pending, completed, failed, refunded

- **Invoices** (untuk setiap payment yang completed)
  - Invoice numbers: INV-XXXXXXXX (unik)
  - Tax: 10% dari amount
  - Due dates: 30 hari dari tanggal issue
  - Status: paid (untuk completed transactions)

- **10 Insurance Claims**
  - Insurance codes: ASURANSI-001, ASURANSI-002, ASURANSI-003, ASURANSI-004
  - Claim types: consultation, medication, diagnostic, hospitalization
  - Amounts: 150,000 - 650,000 IDR
  - Statuses: pending, approved, rejected

---

### 5. **Medical Service Seeder** (`medical-service/internal/seeders/seeder.go`)

Data resep, hasil lab, dan catatan klinis.

#### Data yang di-seed:
- **20 Prescriptions**
  - Medications: 8 macam obat berbeda
  - Frequencies: 1x, 2x, 3x, 4x sehari
  - Durations: 7, 14, 21 hari
  - Expiry dates: 3 bulan ke depan
  - Statuses: active, inactive, refilled, expired

- **15 Lab Results**
  - Test names: CBC, Metabolic Panel, Lipid Panel, Urinalysis, dll
  - Test types: Blood, Urine, Saliva, Tissue
  - Results: Normal, Abnormal, Critical
  - Units: mg/dL, g/dL, µmol/L, mmol/L
  - Statuses: pending, completed, reviewed

- **12 Clinical Notes**
  - Catatan medis lengkap dari doctor
  - Includes findings, assessments, treatment plans
  - Statuses: draft, finalized, archived

---

### 6. **Analytics Service Seeder** (`analytics-service/internal/seeders/seeder.go`)

Data metrics, health indicators, dan system alerts.

#### Data yang di-seed:
- **Service Metrics** (30 entries per service × 5 metric types):
  - Response time: 100-400 ms
  - Throughput: 1000-2000 req/s
  - Error rate: 0-1.5%
  - CPU usage: 10-70%
  - Memory usage: 20-80%

- **Health Indicators** (per service):
  - Status: healthy, degraded, down
  - Uptime: 99.9%
  - Response time: rata-rata per layanan
  - Error rate: 0.1%
  - Active requests: 150-300

- **System Alerts** (20 entries):
  - Alert types: high_error_rate, slow_response, service_down, high_memory, high_cpu
  - Severities: info, warning, critical
  - Statuses: open, resolved

---

## 🚀 Cara Menggunakan Seeder

### Method 1: Manual Seeding (Recommended untuk development)

#### Di User Service (`cmd/main.go`):
```go
import "user-service/internal/seeders"

func main() {
    // ... setup database connection ...
    
    // Run seeder
    if err := seeders.SeedDatabase(db); err != nil {
        log.Printf("Seeding failed: %v\n", err)
    }
}
```

#### Di Appointment Service (`cmd/main.go`):
```go
import "appointment-service/internal/seeders"

func main() {
    // ... setup database connection ...
    
    if err := seeders.SeedAppointments(db); err != nil {
        log.Printf("Seeding failed: %v\n", err)
    }
}
```

#### Di Pharmacy Service (`cmd/main.go`):
```go
import "pharmacy-service/internal/seeders"

func main() {
    // ... setup database connection ...
    
    if err := seeders.SeedPharmacy(db); err != nil {
        log.Printf("Seeding failed: %v\n", err)
    }
}
```

#### Di Medical Service (`cmd/main.go`):
```go
import "medical-service/internal/seeders"

func main() {
    // ... setup database connection ...
    
    if err := seeders.SeedMedical(db); err != nil {
        log.Printf("Seeding failed: %v\n", err)
    }
}
```

#### Di Payment Service (`cmd/main.go`):
```go
import "payment-service/internal/seeders"

func main() {
    // ... setup database connection ...
    
    if err := seeders.SeedPayments(db); err != nil {
        log.Printf("Seeding failed: %v\n", err)
    }
}
```

#### Di Analytics Service (`cmd/main.go`):
```go
import "analytics-service/internal/seeders"

func main() {
    // ... setup database connection ...
    
    if err := seeders.SeedAnalytics(db); err != nil {
        log.Printf("Seeding failed: %v\n", err)
    }
}
```

### Method 2: Conditional Seeding (Auto-seed if empty)

```go
// Di dalam database initialization
if err := database.CheckAndSeed(db, "user-service"); err != nil {
    log.Printf("Auto-seeding failed: %v\n", err)
}
```

---

## 📊 Ringkasan Data yang Ter-seed

### Total Records Dibuat:
| Service | Model | Count |
|---------|-------|-------|
| User Service | Users | 20 (2 admin + 5 doctor + 3 pharmacist + 10 patient) |
| User Service | Departments | 3 |
| User Service | Doctors | 5 |
| User Service | Patients | 10 |
| User Service | Pharmacists | 3 |
| User Service | Admins | 2 |
| Appointment Service | Appointments | 20 |
| Appointment Service | Medical Records | ~10 |
| Pharmacy Service | Pharmacies | 3 |
| Pharmacy Service | Drugs | 8 |
| Pharmacy Service | Drug Stocks | 24 |
| Pharmacy Service | Prescription Orders | 15 |
| Payment Service | Payments | 15 |
| Payment Service | Invoices | ~8 |
| Payment Service | Insurance Claims | 10 |
| Medical Service | Prescriptions | 20 |
| Medical Service | Lab Results | 15 |
| Medical Service | Clinical Notes | 12 |
| Analytics Service | Service Metrics | 900 (6 services × 30 times × 5 types) |
| Analytics Service | Health Indicators | 6 |
| Analytics Service | System Alerts | 20 |
| **TOTAL** | | **~1,176 records** |

---

## ✅ Checklist Testing

Setelah seeding, cek hal berikut:

- [ ] Semua 20 user berhasil dibuat (2 admin, 5 doctor, 3 pharmacist, 10 patient)
- [ ] Semua doctor terhubung ke department
- [ ] Semua 20 appointments berhasil dibuat
- [ ] Medical records dibuat hanya untuk completed appointments
- [ ] Semua 3 pharmacy dengan lengkap
- [ ] Drug stocks konsisten untuk setiap pharmacy
- [ ] Prescription orders terhubung ke patient dan pharmacy
- [ ] Payments dan invoices sesuai
- [ ] Insurance claims terdistribusi
- [ ] Prescriptions, lab results, clinical notes lengkap
- [ ] Metrics dan health indicators terekord dengan baik
- [ ] System alerts create dengan variasi status

---

## 🔍 Query Testing

Setelah seeding berhasil, coba query berikut untuk verify:

```go
// User Service - Count users by role
var adminCount, doctorCount, patientCount, pharmacistCount int64
db.Table("users").Where("role = ?", "admin").Count(&adminCount)
db.Table("users").Where("role = ?", "doctor").Count(&doctorCount)
db.Table("users").Where("role = ?", "patient").Count(&patientCount)
db.Table("users").Where("role = ?", "pharmacist").Count(&pharmacistCount)

// Appointment Service - Count appointments by status
var completedCount, scheduledCount int64
db.Table("appointments").Where("status = ?", "completed").Count(&completedCount)
db.Table("appointments").Where("status = ?", "scheduled").Count(&scheduledCount)

// Pharmacy Service - Get total inventory value
var totalValue int64
db.Table("drug_stocks").Select("SUM(quantity * unit_price)").Row().Scan(&totalValue)
```

---

## 💡 Tips & Troubleshooting

### Jika seeding gagal:
1. Pastikan database sudah ter-create dan ter-migrate
2. Cek database connection string di environment variables
3. Pastikan foreign key constraints tidak conflicting
4. Lihat error logs untuk detailed error messages

### Jika ingin re-seed:
1. Backup data penting
2. Drop dan recreate tables
3. Run seeder lagi

### Customization:
Edit jumlah records, values, atau fields langsung di seeder file sesuai kebutuhan.

---

**Last Updated**: March 26, 2026  
**Version**: 1.0  
**Status**: ✅ Production Ready
