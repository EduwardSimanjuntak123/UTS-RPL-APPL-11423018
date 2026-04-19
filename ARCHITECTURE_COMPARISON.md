# Arsitektur Monolitik vs Microservices

**Detailed Comparison & Analysis**

---

## 📊 Architecture Comparison Table

| Dimension | Monolithic | Microservices |
|-----------|-----------|--------------|
| **Structure** | Single codebase | Multiple services |
| **Database** | One shared database | Database per service |
| **Communication** | Direct method calls | HTTP/REST/gRPC |
| **Dependencies** | Compile-time | Runtime |
| **Scaling** | Vertical (add CPU/RAM) | Horizontal (add instances) |
| **Deployment** | All or nothing | Individual deployment |
| **Technology** | Uniform tech stack | Polyglot possible |
| **Failure Impact** | Application-wide | Service-isolated |
| **Development Speed** | Fast initially | Slower upfront, fast later |
| **Team Size** | 1-10 developers | 10+ developers |

---

## 🏗️ Structural Differences

### MONOLITHIC (Folder ini)

```
MediTrackMonolithApp
│
├── UserService
│   ├── createUser()
│   ├── getUserById()          ← Called by other services
│   └── deactivateUser()       ← Cascades to other services
│
├── AppointmentService
│   ├── userService (dependency)
│   ├── paymentService (dependency)
│   └── createAppointment()    ← Calls 2 other services
│
├── PaymentService
│   ├── userService (dependency)
│   └── createPayment()        ← Called by Appointment & Pharmacy
│
├── PharmacyService
│   ├── userService (dependency)
│   ├── paymentService (dependency)
│   └── createPharmacyOrder()  ← Calls 2 other services
│
└── DatabaseConnection (SHARED)
    └── One connection pool for ALL services
```

**Dependency Graph:**
```
AppointmentService → UserService
AppointmentService → PaymentService
AppointmentService → DatabaseConnection

PharmacyService → UserService
PharmacyService → PaymentService
PharmacyService → DatabaseConnection

AnalyticsService → DatabaseConnection (reads all tables)
```

### MICROSERVICES (Go implementations)

```
API Gateway (3000)
│
├─ HTTP → User Service (3001)
│         └── meditrack_users DB
│
├─ HTTP → Appointment Service (3002)
│         └── meditrack_appointments DB
│
├─ HTTP → Medical Service (3003)
│         └── meditrack_medical DB
│
├─ HTTP → Payment Service (3005)
│         └── meditrack_payment DB
│
├─ HTTP → Pharmacy Service (3004)
│         └── meditrack_pharmacy DB
│
└─ HTTP → Analytics Service (3006)
          └── meditrack_analytics DB (read replicas only)
```

**Dependency Graph:**
```
Nothing directly depends on anything else
Services communicate only via HTTP
Each service can be deployed independently
Each service has isolated database
```

---

## 📈 Data Flow Comparison

### MONOLITHIC: Creating Appointment

```
Request: POST /appointments
    ↓
AppointmentController.createAppointment()
    ↓
AppointmentService.createAppointment()
    ├─ (Direct Call) UserService.getUserById(patientId)
    │   └─ Query: SELECT * FROM users WHERE id = ?
    │       └─ Shared database connection
    ├─ (Direct Call) UserService.getUserById(doctorId)
    │   └─ Query: SELECT * FROM users WHERE id = ?
    │       └─ Shared database connection
    ├─ (Direct Call) PaymentService.isUserPaymentUpToDate(patientId)
    │   └─ Query: SELECT COUNT(*) FROM payments WHERE ... AND status='overdue'
    │       └─ Shared database connection
    └─ INSERT INTO appointments
        └─ Shared database connection
```

**Problems:**
- ✗ All on same thread
- ✗ All use same database connection
- ✗ If UserService slow → whole request slow
- ✗ If database locked → all requests blocked

---

### MICROSERVICES: Creating Appointment

```
Request: POST /api/appointments
    ↓
API Gateway (3000)
    ├─ HTTP GET http://user-service:3001/users/{patientId}
    │   └─ user-service queries meditrack_users DB
    │       └─ Returns {name, email, ...}
    │
    ├─ HTTP GET http://user-service:3001/users/{doctorId}
    │   └─ user-service queries meditrack_users DB
    │       └─ Returns {name, email, ...}
    │
    ├─ HTTP GET http://payment-service:3005/users/{patientId}/status
    │   └─ payment-service queries meditrack_payment DB
    │       └─ Returns {status: "uptodate"}
    │
    └─ HTTP POST http://appointment-service:3002/appointments
        └─ appointment-service queries meditrack_appointments DB
            └─ INSERT new appointment
```

**Benefits:**
- ✓ Parallel requests (async HTTP)
- ✓ Independent databases
- ✓ User service slow → doesn't block payment service
- ✓ Payment DB locked → doesn't affect appointment DB

---

## 🔴 Monolithic Anti-Patterns

### 1. **God Database**

```java
// DatabaseConnection.java
public class DatabaseConnection {
    // One connection pool for EVERYTHING
    private static Connection connection;
    
    // tables:
    // - users
    // - appointments
    // - medical_records
    // - payments
    // - drug_inventory
    // - pharmacy_orders
    // - analytics_logs
    
    // Semua service share satu koneksi
}
```

**Problems:**
- Database down → app down
- Can't backup users independently
- Can't migrate payments separately
- Schema change → coordinate all teams

---

### 2. **Service Locator Anti-Pattern**

```java
// AppointmentService
public class AppointmentService {
    UserService userService = UserService.getInstance();        // Tight coupling
    PaymentService paymentService = PaymentService.getInstance(); // Tight coupling
    
    public void createAppointment(Appointment apt) {
        userService.getUserById(...);      // Direct call
        paymentService.isPaymentUpToDate(...); // Direct call
    }
}
```

**Problems:**
- Hard dependency on other services
- Can't test without mocking everything
- Can't change UserService implementation

---

### 3. **Cascading Updates**

```java
// UserService
public void deactivateUser(int userId) {
    user.setStatus("inactive");
    updateUser(user);
    
    appointmentService.cancelUserAppointments(userId);  // Cascade 1
    paymentService.cancelUserPayments(userId);          // Cascade 2
}
```

**Problems:**
- Multi-service transaction
- Hard to rollback on failure
- Risk of partial failure
- Complex state management

---

### 4. **Shared Models Coupling**

```java
// models/User.java
public class User {
    // UserService uses:
    private int id;
    private String name;
    private String email;
    
    // AppointmentService needs:
    // - id (for lookup)
    
    // PharmacyService needs:
    // - id (for validation)
    
    // But must coordinate changes across all services
}
```

**Problems:**
- Can't evolve User model independently
- Model becomes God Object
- Tight coupling through data structure

---

### 5. **Complex Cross-Database Queries**

```java
// AnalyticsService
public Map<String, Object> getDashboardMetrics() {
    // SELECT COUNT(*) FROM users           ← UserService data
    // SELECT COUNT(*) FROM appointments    ← AppointmentService data
    // SELECT SUM(amount) FROM payments     ← PaymentService data
    // SELECT COUNT(*) FROM drug_inventory  ← PharmacyService data
}
```

**Problems:**
- Analytics must know all schemas
- Schema change → break analytics
- Complex query optimization
- Can't scale analytics independently

---

## ✅ Microservices Advantages

### 1. **Isolated Databases**

```go
// user-service/main.go
db := mysql.Open("meditrack_users")

// appointment-service/main.go
db := mysql.Open("meditrack_appointments")

// Each service:
// - Has separate database
// - Can use different database type
// - Can scale database independently
// - Schema changes isolated
```

### 2. **HTTP Communication (Loose Coupling)**

```go
// appointment-service
func createAppointment(apt Appointment) error {
    // HTTP call (retry-able, timeout-able)
    userResp, err := httpClient.Get("/users/" + apt.PatientID)
    if err != nil {
        return err  // Handle failure gracefully
    }
    
    paymentResp, err := httpClient.Get("/payments/status")
    if err != nil {
        return err  // Handle independently
    }
    
    // Continue independently
}
```

### 3. **Independent Scaling**

```yaml
# docker-compose.yml for microservices
services:
  user-service:
    replicas: 1    # Low traffic

  appointment-service:
    replicas: 3    # High traffic

  pharmacy-service:
    replicas: 2    # Medium traffic

  analytics-service:
    replicas: 1    # Read-only
```

### 4. **Polyglot Architecture**

```yaml
# Different services, different tech
services:
  user-service:     Go + MySQL
  appointment-service: Go + MySQL
  analytics-service:  Node.js + PostgreSQL
  medical-service:  Python + MongoDB
```

---

## 📊 Performance Comparison

### Request: Create Appointment

#### MONOLITHIC
```
Timeline:
0ms    → Request received
5ms    → Check user (UserService)
10ms   → Check doctor (UserService)
15ms   → Check payment status (PaymentService)
20ms   → Create appointment
25ms   → Response returned

Total: 25ms (serial execution)
```

#### MICROSERVICES
```
Timeline:
0ms    → Request received by API Gateway
       → Parallel HTTP calls:
         ├─ GET /users/{patientId}     [async]
         ├─ GET /users/{doctorId}      [async]
         └─ GET /payments/status       [async]
10ms   → All responses received (parallel)
15ms   → Create appointment (appointment-service)
20ms   → Response returned

Total: 20ms (parallel execution)
+ Network latency (network overhead)

With optimal network: 15-20ms vs 25ms
```

---

## 🎯 When to Use Each

### MONOLITHIC is Best For:

✓ Startup MVP (< 3 months)
✓ Small team (< 5 people)
✓ Simple application
✓ Low traffic
✓ Prototype/POC
✓ Quick market validation

Example: Blog platform, Survey app, Basic CRUD

---

### MICROSERVICES is Best For:

✓ Large application (100K+ LOC)
✓ Large team (> 10 developers)
✓ Multiple independent features
✓ High traffic variability
✓ Need different technologies
✓ Continuous deployment

Example: MediTrack (this project), Netflix, Uber

---

## 🔄 Migration Strategy

### Phase 1: Monolithic (Today)
```
├── Laravel Monolith (monolit/)
└── Java Representation (Representasi Monolit java/)
```

### Phase 2: Modular Monolith
```
laravel-app/
├── User Module
├── Appointment Module
├── Medical Module
├── Pharmacy Module
├── Payment Module
└── Analytics Module
```

### Phase 3: Monolith + Services
```
laravel-app/
└── API Layer

Go Microservices/
├── user-service
├── appointment-service
└── ...
```

### Phase 4: Full Microservices
```
API Gateway
├── user-service (separate repo)
├── appointment-service (separate repo)
├── medical-service (separate repo)
├── pharmacy-service (separate repo)
├── payment-service (separate repo)
└── analytics-service (separate repo)
```

---

## 📚 Key Learnings

### MONOLITHIC
- ✓ Fast to develop initially
- ✓ Simple to deploy
- ✓ Easy to test (all code local)
- ✗ Hard to scale
- ✗ Risky updates (all or nothing)
- ✗ Technology locked-in
- ✗ Communication bottleneck

### MICROSERVICES
- ✓ Scalable independently
- ✓ Safe updates (one service at a time)
- ✓ Technology flexible
- ✓ Clear boundaries
- ✗ Complex distributed systems
- ✗ Network latency
- ✗ Data consistency challenges
- ✗ Operational overhead

---

## 🎓 Architecture Trade-offs

```
                    Monolithic
                        |
        Simple       Complexity      Scalable
           |             |              |
      Fast Development   |         Harder Dev
           |             |              |
        Easy Testing      |       Distributed Tests
           |             |              |
        Easy Deploy   Operational    Complex Deploy
                    Complexity
                        |
                  Microservices
```

---

## Kesimpulan

**Monolithic Architecture** (Java Representation):
- Shared database menyebabkan tight coupling
- Direct method calls tidak sustainable
- Good for small projects saja
- Grows into maintenance nightmare

**Microservices Architecture** (Go Implementation):
- Separate databases → loose coupling
- HTTP communication → distributed but resilient
- Scalable and maintainable
- But requires more operational complexity

**MediTrack Project:**
- Started as monolith (Laravel)
- Converted to microservices (Go)
- This folder shows the "before" state
- microservices folder shows the "after" state

---

**References:**
- Martin Fowler - Microservice Architecture
- Sam Newman - Building Microservices
- Eric Evans - Domain-Driven Design

