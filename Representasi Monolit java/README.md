# Representasi Monolit Java - MediTrack

**Monolithic Architecture Simulation - Simplified Java Code Structure**

---

## 📋 Overview

Folder ini berisi representasi **arsitektur monolitik** dalam bahasa Java yang menunjukkan:

1. ✓ Struktur kode Java yang disederhanakan (kelas, paket, modul)
2. ✓ Ketergantungan erat antar modul (tight coupling)
3. ✓ Database tunggal bersama untuk semua service
4. ✓ Panggilan metode langsung antar service
5. ✓ Masalah dan keterbatasan arsitektur monolitik

---

## 📁 Struktur Folder

```
Representasi Monolit java/
│
├── src/main/java/com/meditrack/
│   │
│   ├── database/
│   │   └── DatabaseConnection.java          ← SHARED DATABASE (Monolithic Problem)
│   │
│   ├── models/
│   │   ├── User.java                        ← Shared model untuk semua service
│   │   ├── Appointment.java
│   │   └── MedicalRecord.java
│   │
│   ├── services/                            ← TIGHTLY COUPLED SERVICES
│   │   ├── UserService.java                 (Called by: Appointment, Medical, Pharmacy, Payment)
│   │   ├── AppointmentService.java          (Depends on: User, Payment)
│   │   ├── MedicalService.java              (Depends on: User)
│   │   ├── PharmacyService.java             (Depends on: User, Payment)
│   │   ├── PaymentService.java              (Depends on: User)
│   │   └── AnalyticsService.java            (Reads: All tables)
│   │
│   ├── controllers/                         ← HTTP Controllers
│   │   ├── UserController.java
│   │   ├── AppointmentController.java
│   │   ├── MedicalController.java
│   │   ├── PharmacyController.java
│   │   └── PaymentController.java
│   │
│   └── MediTrackMonolithApp.java             ← MAIN APPLICATION (Everything bundled)
│
└── README.md                                  ← This file
```

---

## 🔴 Monolithic Pattern Characteristics

### 1. **Single Database Connection** (database/DatabaseConnection.java)

```java
// Singleton - hanya satu koneksi untuk seluruh aplikasi
public class DatabaseConnection {
    private static Connection connection;
    // Semua service menggunakan koneksi yang sama
}
```

**Masalah:**
- ✗ Satu database down = seluruh aplikasi down
- ✗ Tidak bisa isolate database per service
- ✗ Schema coupling antar modul

---

### 2. **Shared Models** (models/)

```java
// Model User digunakan oleh semua service
public class User {
    private int id;
    private String name;
    private String email;
    // ... diakses oleh UserService, AppointmentService, MedicalService, etc
}
```

**Masalah:**
- ✗ Perubahan model mempengaruhi semua service
- ✗ Model menjadi terlalu besar (God Object)
- ✗ Sulit untuk evolusi independent

---

### 3. **Tight Coupling - Direct Method Calls** (services/)

```java
// AppointmentService langsung memanggil UserService
public class AppointmentService {
    private UserService userService = UserService.getInstance();
    
    public boolean createAppointment(Appointment apt) {
        User patient = userService.getUserById(apt.getPatientId());  // DIRECT CALL
        User doctor = userService.getUserById(apt.getDoctorId());    // DIRECT CALL
        // ...
    }
}
```

**Masalah:**
- ✗ Ketergantungan langsung antar service
- ✗ Tidak bisa deploy AppointmentService tanpa UserService
- ✗ Testing memerlukan semua dependency
- ✗ Sulit untuk scale service secara independent

---

### 4. **Cascading Operations** (AppointmentService → PaymentService → UserService)

```java
public void deactivateUserAndCancelAppointments(int userId) {
    // UserService langsung mempengaruhi AppointmentService dan PaymentService
    user.setStatus("inactive");
    updateUser(user);
    
    appointmentService.cancelUserAppointments(userId);  // CASCADING
    paymentService.cancelUserPayments(userId);           // CASCADING
}
```

**Masalah:**
- ✗ Kompleks transaction across services
- ✗ Sulit rollback jika ada error
- ✗ Risk of partial failure
- ✗ Hard to reason about state changes

---

### 5. **Analytics Reads All Tables** (AnalyticsService)

```java
public Map<String, Object> getDashboardMetrics() {
    // SELECT dari users table
    // SELECT dari appointments table
    // SELECT dari medical_records table
    // SELECT dari payments table
    // SELECT dari drug_inventory table
}
```

**Masalah:**
- ✗ Tight coupling dengan schema semua service
- ✗ Complex joins across modules
- ✗ Schema change di satu module = break analytics
- ✗ Performance degradation dengan data besar

---

## 🎯 Demonstrasi Arsitektur

Jalankan `MediTrackMonolithApp.java` untuk melihat:

### Demo 1: Simple User Creation
```
✓ User created
```

### Demo 2: Tight Coupling - Appointment Creation
```
AppointmentService.createAppointment()
  └─ Calls UserService.getUserById()
  └─ Calls UserService.getUserById()
  └─ Calls PaymentService.isUserPaymentUpToDate()
```

### Demo 3: Cascading Operations
```
UserService.deactivateUser(userId)
  └─ Calls AppointmentService.cancelUserAppointments()
  └─ Calls PaymentService.cancelUserPayments()
```

### Demo 4: Complex Analytics
```
AnalyticsService.getDashboardMetrics()
  └─ SELECT from users
  └─ SELECT from appointments
  └─ SELECT from medical_records
  └─ SELECT from payments
  └─ SELECT from drug_inventory
```

### Demo 5: Shared Database Problems
- Single point of failure
- Schema coupling
- Resource contention
- Scaling difficulty
- Data consistency complexity

---

## 📊 Comparison: Monolithic vs Microservices

### MONOLITHIC (Folder ini)

| Aspek | Status | Keterangan |
|-------|--------|-----------|
| Codebase | 1 | Semua dalam satu aplikasi |
| Database | 1 Shared | Satu database untuk semua |
| Communication | Direct Calls | Method calls langsung |
| Coupling | Tight | Service saling bergantung |
| Scaling | Vertical Only | Harus scale keseluruhan |
| Deployment | Monolithic | Deploy semua atau tidak sama sekali |
| Failure Domain | Entire App | Error di satu module = app down |
| Development | Simple (awal) | Cepat untuk project kecil |
| Maintenance | Hard (scale) | Semakin kompleks saat bertambah |

### MICROSERVICES (Folder: microserv/microservices)

| Aspek | Status | Keterangan |
|-------|--------|-----------|
| Codebase | 7 | Separate per service |
| Database | 6 Independent | Isolated per service |
| Communication | HTTP/gRPC | Network calls |
| Coupling | Loose | Service independent |
| Scaling | Horizontal | Scale service per service |
| Deployment | Independent | Deploy service individually |
| Failure Domain | Service | Error isolated to service |
| Development | Complex (awal) | Butuh orchestration |
| Maintenance | Easy (scale) | Scalable dan maintainable |

---

## 🔴 Key Problems in Monolithic Architecture

### 1. **Single Point of Failure**
Database down → Entire application down

### 2. **Tight Coupling**
Change in User model → Must update all 6 services

### 3. **Difficult Scaling**
High pharmacy load → Must scale entire database

### 4. **Complex Transactions**
Deactivate user → Must rollback multiple operations

### 5. **Large Deployment**
Small bugfix → Must test and deploy entire application

### 6. **Technology Lock-in**
Must use same language, framework, database for all modules

### 7. **Testing Complexity**
Test appointment → Must setup user service, payment service

---

## ✅ When Monolithic Works

Monolithic architecture cocok untuk:
- ✓ Small teams (< 10 developers)
- ✓ Simple applications
- ✓ Single module responsibility
- ✓ Low scaling requirements
- ✓ Time-to-market critical

---

## 🚀 Migration Path

```
MONOLITHIC (ini)
       ↓
MODULAR MONOLITH
       ↓
MICROSERVICES (microserv/microservices folder)
       ↓
DISTRIBUTED SYSTEMS
```

---

## 📚 Pembelajaran

Folder ini demonstrasi bahwa:

1. **Shared Database is Evil** 🔴
   - Menyebabkan tight coupling
   - Hard to scale
   - Single point of failure

2. **Direct Service Calls** 🔴
   - Tidak sustainable
   - Sulit test
   - Cascade failures

3. **Single Codebase Limit** 🔴
   - Hard to maintain
   - Risk of regression
   - Long deployment cycles

4. **Monolith Strategy** ✅
   - Good for MVP/startup
   - Easy to develop initially
   - Simple deployment

---

## 🎓 Bandingkan dengan Microservices

**Buka** `microserv/microservices/` untuk melihat:
- ✓ Separate databases per service
- ✓ HTTP/gRPC communication (no direct calls)
- ✓ Independent deployment
- ✓ Loose coupling
- ✓ Horizontal scaling

---

## 📝 Notes

- Java classes adalah **simplified representation** (bukan production code)
- Database connection menggunakan JDBC untuk demo
- Annotations dan frameworks diminimalkan untuk clarity
- Fokus pada **architectural patterns**, bukan implementation details

---

## 🔗 Related Folders

- **microserv/microservices/** - Microservices version
- **monolit/** - Actual Laravel monolithic application
- **Representasi Monolit java/** - This folder (Java simulation)

---

**Created:** April 2026
**Purpose:** Educational - Understand monolithic vs microservices architecture
**Language:** Java (Simplified)
