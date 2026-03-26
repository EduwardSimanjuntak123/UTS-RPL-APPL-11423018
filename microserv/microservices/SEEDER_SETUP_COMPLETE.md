# ✅ Seeder Setup Completed

## 📦 What Was Created

### 1. **Seeder Entry Points** (untuk setiap service)
Dibuat folder `cmd/seeder/` di setiap service dengan `main.go`:

```
✓ user-service/cmd/seeder/main.go
✓ appointment-service/cmd/seeder/main.go
✓ pharmacy-service/cmd/seeder/main.go
✓ medical-service/cmd/seeder/main.go
✓ payment-service/cmd/seeder/main.go
✓ analytics-service/cmd/seeder/main.go
```

Setiap file connect ke specific database dan run seeder-nya.

---

### 2. **Automated Seeder Scripts**
Di folder `microservices/`:

**Windows:**
```
run_all_seeders.bat
```
- Menjalankan semua seeder secara berurutan
- Build & run seeder untuk setiap service
- Show summary (success/failed count)

**Linux/macOS:**
```
run_all_seeders.sh
```
- Sama functionality dengan batch file
- Dapat dijalankan dengan `bash` atau `chmod +x`

---

### 3. **Documentation Files**
```
SEEDER_QUICK_START.md       ← Start dari sini!
SEEDERS_DOCUMENTATION.md    ← Detailed documentation
```

---

## 🚀 How to Run (3 Simple Options)

### **OPTION 1: Run All Seeders At Once** (RECOMMENDED) ⭐

#### Windows (PowerShell/CMD):
```bash
cd microservices
.\run_all_seeders.bat
```

#### Linux/macOS:
```bash
cd microservices
bash run_all_seeders.sh
```

**Result**: Semua 6 services di-seed dalam satu command!

---

### **OPTION 2: Run Individual Service**

```bash
# User Service
cd microservices/user-service
go run cmd/seeder/main.go

# Atau compile dulu
go build -o seeder cmd/seeder/main.go
./seeder
```

Repeat untuk service lain sesuai kebutuhan.

---

### **OPTION 3: Integrate ke Service Main**

Tambahkan di main `main.go` setiap service untuk auto-seed saat startup:

```go
import "service-name/internal/seeders"

func main() {
    db := setupDatabase()
    
    // Auto-seed jika database kosong
    if err := seeders.SeedDatabase(db); err != nil {
        log.Println("Seeding skipped atau failed:", err)
    }
    
    // Start server
    startServer(db)
}
```

---

## 📊 What Gets Seeded

| Service | Models | Records |
|---------|--------|---------|
| **User** | Users, Departments, Doctors, Patients, Pharmacists, Admins | 20+ |
| **Appointment** | Appointments, Medical Records | 20+ |
| **Pharmacy** | Pharmacies, Drugs, Drug Stocks, Orders | 62+ |
| **Medical** | Prescriptions, Lab Results, Clinical Notes | 47+ |
| **Payment** | Payments, Invoices, Insurance Claims | 33+ |
| **Analytics** | Service Metrics, Health Indicators, System Alerts | 926+ |
| **TOTAL** | | **~1,176 records** |

---

## 🔐 Test Credentials (After Seeding)

```
ADMIN:
  Email: admin@meditrack.com
  Password: password123

DOCTOR (example):
  Email: dr.budi@meditrack.com
  Password: password123

PATIENT (example):
  Email: pasien1@meditrack.com
  Password: password123

PHARMACIST (example):
  Email: apt.budi@meditrack.com
  Password: password123
```

---

## ✅ Prerequisite Checklist

- [ ] PostgreSQL 12+ installed & running
- [ ] 6 databases created:
  - [ ] meditrack_users
  - [ ] meditrack_appointments
  - [ ] meditrack_medical
  - [ ] meditrack_pharmacy
  - [ ] meditrack_payment
  - [ ] meditrack_analytics
- [ ] Go 1.18+ installed
- [ ] GORM & dependencies installed
- [ ] Seeder scripts/binaries ready

---

## 🔍 Verify Seeding

After running seeder:

```bash
# Check user count
psql -U postgres -d meditrack_users -c "SELECT COUNT(*) as users FROM users;"

# Check appointments
psql -U postgres -d meditrack_appointments -c "SELECT COUNT(*) as appointments FROM appointments;"

# Check all services
psql -U postgres -d meditrack_pharmacy -c "SELECT COUNT(*) as drugs FROM drugs;"
psql -U postgres -d meditrack_medical -c "SELECT COUNT(*) as prescriptions FROM prescriptions;"
psql -U postgres -d meditrack_payment -c "SELECT COUNT(*) as payments FROM payments;"
psql -U postgres -d meditrack_analytics -c "SELECT COUNT(*) as metrics FROM service_metrics;"
```

---

## 📝 File Structure

```
microservices/
├── run_all_seeders.bat              ← Jalankan ini (Windows)
├── run_all_seeders.sh               ← Jalankan ini (Linux/macOS)
├── SEEDER_QUICK_START.md            ← Baca ini untuk quick start
├── SEEDERS_DOCUMENTATION.md         ← Detailed docs
│
├── user-service/
│   ├── cmd/seeder/main.go           ← User service seeder entry point
│   └── internal/seeders/seeder.go   ← User seeder logic
│
├── appointment-service/
│   ├── cmd/seeder/main.go           ← Appointment seeder entry point
│   └── internal/seeders/seeder.go   ← Appointment seeder logic
│
├── pharmacy-service/
│   ├── cmd/seeder/main.go           ← Pharmacy seeder entry point
│   └── internal/seeders/seeder.go   ← Pharmacy seeder logic
│
├── medical-service/
│   ├── cmd/seeder/main.go           ← Medical seeder entry point
│   └── internal/seeders/seeder.go   ← Medical seeder logic
│
├── payment-service/
│   ├── cmd/seeder/main.go           ← Payment seeder entry point
│   └── internal/seeders/seeder.go   ← Payment seeder logic
│
└── analytics-service/
    ├── cmd/seeder/main.go           ← Analytics seeder entry point
    └── internal/seeders/seeder.go   ← Analytics seeder logic
```

---

## ⚡ Quick Start (60 seconds)

1. **Setup databases:**
   ```bash
   psql -U postgres
   CREATE DATABASE meditrack_users;
   CREATE DATABASE meditrack_appointments;
   CREATE DATABASE meditrack_medical;
   CREATE DATABASE meditrack_pharmacy;
   CREATE DATABASE meditrack_payment;
   CREATE DATABASE meditrack_analytics;
   ```

2. **Run seeders** (Windows):
   ```bash
   cd microservices
   .\run_all_seeders.bat
   ```
   
   Or (Linux/macOS):
   ```bash
   cd microservices
   bash run_all_seeders.sh
   ```

3. **Verify:**
   ```bash
   psql -U postgres -d meditrack_users -c "SELECT COUNT(*) FROM users;"
   ```

4. **Use credentials** (dari list di atas) untuk login

Done! ✅

---

## 🆘 Need Help?

**Problem:** "Failed to connect to database"
- **Solution**: Pastikan PostgreSQL running dan databases ter-create

**Problem:** "command not found: go"
- **Solution**: Install Go atau add ke PATH

**Problem:** Seeding timeout
- **Solution**: Increase timeout di seeder main.go atau check database performance

**Problem:** "port 5432 already in use"
- **Solution**: Change port di connection string atau stop other PostgreSQL instance

---

## 📚 Next Steps

1. ✅ Run seeder dengan `run_all_seeders.bat/sh`
2. ✅ Verify data ter-seed dengan queries
3. ✅ Get test credentials
4. ✅ Start microservices (API gateway, individual services)
5. ✅ Test API endpoints dengan seeded data
6. ✅ Start Laravel web app
7. ✅ Login dengan test credentials
8. ✅ Buat appointments, manage pharmacy, track payments, etc

---

**Status**: ✅ Seeder Setup Complete & Ready to Use!

**Last Updated**: March 26, 2026
