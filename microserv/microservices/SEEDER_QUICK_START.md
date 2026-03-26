# 🌱 MediTrack Seeders - Quick Start Guide

## Prerequisites

Pastikan sudah ter-install:
- ✅ Go 1.18+
- ✅ PostgreSQL 12+
- ✅ GORM dan drivers

## 📋 Setup Database

### 1. Create Databases untuk setiap service:

```sql
-- Connect ke PostgreSQL sebagai admin
psql -U postgres

-- Create databases
CREATE DATABASE meditrack_users;
CREATE DATABASE meditrack_appointments;
CREATE DATABASE meditrack_medical;
CREATE DATABASE meditrack_pharmacy;
CREATE DATABASE meditrack_payment;
CREATE DATABASE meditrack_analytics;

-- Verify
\l
```

## 🚀 Menjalankan Seeder

### **OPTION 1: Run All Seeders Sekaligus (RECOMMENDED)**

#### Windows (PowerShell atau CMD):
```bash
cd microservices
.\run_all_seeders.bat
```

#### Linux/macOS (Bash):
```bash
cd microservices
chmod +x run_all_seeders.sh
./run_all_seeders.sh
```

**Output yang diharapkan:**
```
====================================================
   MediTrack Database Seeding - All Services
====================================================

[*] Starting seeding for user-service...
  [>] Building seeder...
  [>] Running seeder...
✅ Created admin: Admin Utama (admin@meditrack.com)
✅ Created department: Kardiologi
✅ Created doctor: Dr. Budi Hartono (dr.budi@meditrack.com)
...
[OK] user-service seeding completed

[*] Starting seeding for appointment-service...
...
====================================================
   Seeding Summary
====================================================
   Success: 6/6
   Failed:  0/6
====================================================

[OK] All services seeded successfully!
```

---

### **OPTION 2: Run Individual Service Seeders**

Jika ingin run seeder untuk service spesifik:

#### User Service:
```bash
cd microservices/user-service
go run cmd/seeder/main.go
# atau
go build -o seeder cmd/seeder/main.go && ./seeder
```

#### Appointment Service:
```bash
cd microservices/appointment-service
go run cmd/seeder/main.go
```

#### Pharmacy Service:
```bash
cd microservices/pharmacy-service
go run cmd/seeder/main.go
```

#### Medical Service:
```bash
cd microservices/medical-service
go run cmd/seeder/main.go
```

#### Payment Service:
```bash
cd microservices/payment-service
go run cmd/seeder/main.go
```

#### Analytics Service:
```bash
cd microservices/analytics-service
go run cmd/seeder/main.go
```

---

## 🔐 Default Test Credentials

Setelah seeding, gunakan credentials ini untuk login:

### Admin Account
```
Email: admin@meditrack.com
Password: password123
```

### Doctors (pilih salah satu):
```
Email: dr.budi@meditrack.com
Email: dr.siti@meditrack.com
Email: dr.ahmad@meditrack.com
Email: dr.eka@meditrack.com
Email: dr.rinto@meditrack.com
Password: password123
```

### Patients (pilih salah satu):
```
Email: pasien1@meditrack.com
Email: pasien2@meditrack.com
... sampai pasien10@meditrack.com
Password: password123
```

### Pharmacists (pilih salah satu):
```
Email: apt.budi@meditrack.com
Email: apt.siti@meditrack.com
Email: apt.rudi@meditrack.com
Password: password123
```

---

## ✅ Verify Seeding Success

### Check User Service data:
```sql
psql -U postgres -d meditrack_users -c "SELECT COUNT(*) as total_users FROM users;"
psql -U postgres -d meditrack_users -c "SELECT name, email, role FROM users LIMIT 10;"
```

Expected: 20 users (2 admin + 5 doctor + 3 pharmacist + 10 patient)

### Check Appointment Service data:
```sql
psql -U postgres -d meditrack_appointments -c "SELECT COUNT(*) as total_appointments FROM appointments;"
```

Expected: 20 appointments

### Check Pharmacy Service data:
```sql
psql -U postgres -d meditrack_pharmacy -c "SELECT COUNT(*) as total_drugs FROM drugs;"
psql -U postgres -d meditrack_pharmacy -c "SELECT COUNT(*) as total_stocks FROM drug_stocks;"
```

Expected: 8 drugs, 24 drug stocks

### Check Payment Service data:
```sql
psql -U postgres -d meditrack_payment -c "SELECT COUNT(*) as total_payments FROM payments;"
```

Expected: 15 payments

### Check Medical Service data:
```sql
psql -U postgres -d meditrack_medical -c "SELECT COUNT(*) as total_prescriptions FROM prescriptions;"
```

Expected: 20 prescriptions

### Check Analytics Service data:
```sql
psql -U postgres -d meditrack_analytics -c "SELECT COUNT(*) as total_metrics FROM service_metrics;"
```

Expected: 900+ metrics

---

## 📊 Data Summary After Seeding

| Service | Table | Count | Details |
|---------|-------|-------|---------|
| **User** | users | 20 | 2 admin, 5 doctor, 3 pharmacist, 10 patient |
| **User** | departments | 3 | Kardiologi, Neurologi, Umum |
| **User** | doctors | 5 | Dengan license numbers & specializations |
| **User** | patients | 10 | Dengan medical numbers & blood types |
| **Appointment** | appointments | 20 | Berbagai status & types |
| **Appointment** | medical_records | ~10 | Untuk completed appointments |
| **Pharmacy** | pharmacies | 3 | Dengan koordinat & info lengkap |
| **Pharmacy** | drugs | 8 | Medication data lengkap |
| **Pharmacy** | drug_stocks | 24 | Inventory per pharmacy |
| **Pharmacy** | prescription_orders | 15 | Order history |
| **Payment** | payments | 15 | Berbagai payment methods |
| **Payment** | invoices | ~8 | Untuk completed payments |
| **Payment** | insurance_claims | 10 | Klaim asuransi |
| **Medical** | prescriptions | 20 | Dengan dosage & frequency |
| **Medical** | lab_results | 15 | Test results |
| **Medical** | clinical_notes | 12 | Catatan medis |
| **Analytics** | service_metrics | 900+ | Response time, throughput, etc |
| **Analytics** | health_indicators | 6 | Per service |
| **Analytics** | system_alerts | 20 | Berbagai severity levels |

---

## 🔧 Troubleshooting

### Error: "Failed to connect to database"
- **Fix**: Pastikan PostgreSQL running dan databases sudah ter-create
```bash
# Check PostgreSQL status
sudo service postgresql status

# Atau di Windows
Get-Service postgresql-x64-*
```

### Error: "database does not exist"
```bash
psql -U postgres -c "CREATE DATABASE meditrack_users;"
```

### Error: "Permission denied"
```bash
# Ubah file permissions
chmod +x run_all_seeders.sh

# Atau run dengan bash/sh langsung
bash run_all_seeders.sh
```

### Error: "go: command not found"
- Pastikan Go sudah ter-install dan ada di PATH
```bash
go version
```

### Connection refused
- Pastikan database connection string di seeder main.go sesuai dengan PostgreSQL config
```go
dsn := "host=localhost user=postgres password=postgres dbname=meditrack_users port=5432 sslmode=disable"
```

---

## 📝 Database Connection Strings

Setiap service menggunakan connection string:
```
host=localhost 
user=postgres 
password=postgres 
dbname=meditrack_SERVICE_NAME
port=5432 
sslmode=disable
```

**Jika berbeda**, edit di file `cmd/seeder/main.go` masing-masing service:

```go
dsn := "host=YOUR_HOST user=YOUR_USER password=YOUR_PASSWORD dbname=YOUR_DB port=5432 sslmode=disable"
```

---

## ✨ Custom Seeding

Jika ingin modify data seeding:

1. Edit file seeder di `internal/seeders/seeder.go` setiap service
2. Ubah jumlah records, values, atau fields
3. Run seeder lagi

Contoh - ubah jumlah patients di user-service:
```go
// Change from 10 to 20 patients
for i := 0; i < 20; i++ {
    // ... create patient data
}
```

---

## 📚 Additional Resources

- [SEEDERS_DOCUMENTATION.md](./SEEDERS_DOCUMENTATION.md) - Detailed documentation
- [User Service Seeder](./user-service/internal/seeders/seeder.go)
- [Appointment Service Seeder](./appointment-service/internal/seeders/seeder.go)
- [Pharmacy Service Seeder](./pharmacy-service/internal/seeders/seeder.go)
- [Medical Service Seeder](./medical-service/internal/seeders/seeder.go)
- [Payment Service Seeder](./payment-service/internal/seeders/seeder.go)
- [Analytics Service Seeder](./analytics-service/internal/seeders/seeder.go)

---

## ✅ Quick Checklist

- [ ] PostgreSQL installed & running
- [ ] 6 databases created (meditrack_users, meditrack_appointments, etc)
- [ ] Go 1.18+ installed
- [ ] Seeder scripts copied (run_all_seeders.bat/sh)
- [ ] Run seeder script atau individual seeders
- [ ] Verify data dengan queries
- [ ] Get test credentials & try login
- [ ] Start microservices
- [ ] Test API endpoints

---

**Last Updated**: March 26, 2026  
**Status**: ✅ Ready to Use
