# MediTrack Architecture Study Guide

**Complete Overview of Monolithic vs Microservices**

---

## 🎯 What You Have

This project contains **two complete implementations** of the same healthcare system:

### 1. **Monolithic Architecture** (Representasi Monolit java/)
- Single Java codebase
- Shared database
- Direct method calls between services
- For educational purposes

### 2. **Microservices Architecture** (microserv/microservices/)
- 7 independent Go services
- Separate databases
- HTTP communication via API Gateway
- Production-ready reference implementation

---

## 📚 Study Path

### Start Here
1. Read: [ARCHITECTURE_COMPARISON.md](ARCHITECTURE_COMPARISON.md)
2. Read: [MONOLITHIC_ANTIPATTERNS.md](MONOLITHIC_ANTIPATTERNS.md)
3. Review: [Representasi Monolit java/README.md](Representasi%20Monolit%20java/README.md)

### Then Study Code
1. **Monolithic Code** (Representasi Monolit java/)
   - DatabaseConnection.java → Shared DB
   - UserService.java → Direct dependencies
   - AppointmentService.java → Cascading operations
   - AnalyticsService.java → Complex queries

2. **Microservices Code** (microserv/microservices/)
   - user-service/main.go → Independent service
   - shared/httpclient/client.go → HTTP communication
   - Each service has isolated database

### Finally Compare
- Run MediTrackMonolithApp.java to see monolithic patterns
- Review Go services to see solutions
- Understand trade-offs

---

## 🔍 Key Concepts

### MONOLITHIC (This Folder)

```
📦 Single Application
│
├─ Database: meditrack_monolith (SHARED)
│  ├─ users table
│  ├─ appointments table
│  ├─ payments table
│  ├─ drug_inventory table
│  └─ ... all tables
│
├─ Services (Direct Calls)
│  ├─ UserService ← called by 5 other services
│  ├─ AppointmentService → calls UserService + PaymentService
│  ├─ PaymentService → called by AppointmentService + PharmacyService
│  ├─ PharmacyService → calls UserService + PaymentService
│  ├─ MedicalService → calls UserService
│  ├─ AnalyticsService → reads ALL tables
│  └─ Controllers
│
└─ One Deployment Unit
   ├─ Deploy all or nothing
   └─ Must restart entire app for any change
```

### MICROSERVICES (Other Folder)

```
🏗️  Distributed Architecture
│
├─ API Gateway (3000)
│
├─ User Service (3001)
│  └─ meditrack_users DB
│
├─ Appointment Service (3002)
│  └─ meditrack_appointments DB
│
├─ Medical Service (3003)
│  └─ meditrack_medical DB
│
├─ Pharmacy Service (3004)
│  └─ meditrack_pharmacy DB
│
├─ Payment Service (3005)
│  └─ meditrack_payment DB
│
└─ Analytics Service (3006)
   └─ meditrack_analytics DB
```

---

## 📊 7 Major Differences

### 1️⃣ Database Architecture

**MONOLITHIC:**
```
Single Database (SHARED)
  ├─ users
  ├─ appointments
  ├─ payments
  ├─ drug_inventory
  └─ medical_records

Problem:
✗ One database down = app down
✗ Cannot scale database independently
✗ Schema changes affect all services
```

**MICROSERVICES:**
```
Per-Service Databases (ISOLATED)
  User DB:         meditrack_users
  Appointment DB:  meditrack_appointments
  Payment DB:      meditrack_payment
  Pharmacy DB:     meditrack_pharmacy
  Medical DB:      meditrack_medical
  Analytics DB:    meditrack_analytics

Benefits:
✓ One database down = only that service down
✓ Can scale each database independently
✓ Can use different database types
```

---

### 2️⃣ Service Communication

**MONOLITHIC:**
```java
// AppointmentService
UserService userService = UserService.getInstance();
paymentService.createPayment(...);  // Direct call
userService.getUserById(...);        // Direct call

Problem:
✗ Tight coupling
✗ Synchronous (blocking)
✗ Cannot test independently
✗ Cannot deploy independently
```

**MICROSERVICES:**
```go
// appointment-service
httpClient.Get("http://user-service:3001/users/{id}")
httpClient.Post("http://payment-service:3005/payments")

Benefits:
✓ Loose coupling
✓ Asynchronous (non-blocking)
✓ Can mock/test independently
✓ Can deploy independently
```

---

### 3️⃣ Failure Isolation

**MONOLITHIC:**
```
Payment Service crashes
        ↓
Shared connection pool exhausted
        ↓
User Service cannot connect
        ↓
Appointment Service cannot connect
        ↓
ENTIRE APPLICATION DOWN

Availability: 0%
Recovery: Restart entire app
```

**MICROSERVICES:**
```
Payment Service crashes
        ↓
API Gateway returns 503 for payment requests
        ↓
User Service continues normally
        ↓
Appointment Service continues normally

Benefits:
✓ User service available
✓ Appointment service available
✓ Only payment requests fail
✓ Can restart just payment service

Availability: 80% (if payment is 20% of traffic)
Recovery: Restart just payment service
```

---

### 4️⃣ Scaling Strategy

**MONOLITHIC:**
```
High traffic → Add CPU/RAM to server
  
Monolithic app scales vertically
├─ Max CPU: 256 cores (theoretical limit)
├─ Max RAM: 3TB (theoretical limit)
└─ Then what? Cannot scale beyond one server

Cost at 100x load:
├─ New server: $200K
└─ Still same code/features
```

**MICROSERVICES:**
```
High traffic on pharmacy → Scale pharmacy service only

Each service scales horizontally
├─ user-service:  2 instances
├─ appointment:   3 instances
├─ pharmacy:      10 instances (high traffic)
├─ payment:       5 instances
└─ analytics:     1 instance (read-only)

Cost at 100x load:
├─ Scale only pharmacy 10x
└─ Cost proportional to actual load
```

---

### 5️⃣ Development Velocity

**MONOLITHIC:**
```
Timeline:
Week 1-2: Fast (easy to add features)
Week 3-4: Slower (interdependencies)
Month 2:  Slow (complex interactions)
Month 3:  Crawl (everything affects everything)

Team: Small (< 10 people)
Problem: Large team → merge conflicts, coordination overhead
```

**MICROSERVICES:**
```
Timeline:
Week 1:   Slower setup (infrastructure)
Week 2+:  Consistent velocity (teams independent)
Month 6:  Fast (no blocking dependencies)

Team: Large (10-100+ people)
Benefit: Teams work independently
```

---

### 6️⃣ Deployment Process

**MONOLITHIC:**
```
Bug fix in UserService
  ↓
Must test ALL services
  ├─ UserService tests
  ├─ AppointmentService tests
  ├─ PaymentService tests
  ├─ PharmacyService tests
  └─ AnalyticsService tests
  ↓
Must deploy entire application
  ├─ Database migration (if any)
  ├─ Code compilation
  ├─ Restart application
  └─ All requests paused (~1 minute)
  ↓
Risk: Regression in unrelated service
```

**MICROSERVICES:**
```
Bug fix in user-service
  ↓
Test only user-service
  ↓
Deploy only user-service
  ├─ New instance started
  ├─ Old instance drained
  ├─ Requests switched (< 1 second downtime)
  └─ Other services unaffected
  ↓
Risk: Only user-service affected
```

---

### 7️⃣ Technology Choice

**MONOLITHIC:**
```
All services must use:
├─ Same language (Java)
├─ Same framework (Spring)
├─ Same database (MySQL)
└─ Same deployment (Tomcat)

Problem: Technology lock-in
├─ Want to use Node.js for realtime? Cannot
├─ Want to use PostgreSQL for analytics? Cannot
└─ Want to use MongoDB for flexibility? Cannot
```

**MICROSERVICES:**
```
Each service can use different:
├─ User Service: Go + MySQL
├─ Appointment Service: Go + MySQL
├─ Medical Service: Python + PostgreSQL
├─ Payment Service: Go + MySQL
├─ Analytics Service: Node.js + MongoDB
└─ Pharmacy Service: Java + MySQL

Benefit:
✓ Right tool for right job
✓ Easy to experiment
✓ Easier to migrate between technologies
```

---

## 🧮 Cost-Benefit Analysis

### When Monolithic Wins

```
Criteria           | Monolithic | Score
Early development  | ✓✓✓       | 9/10
Time to market     | ✓✓✓       | 9/10
Team size < 5      | ✓✓✓       | 9/10
Simple app         | ✓✓✓       | 9/10
Low traffic        | ✓✓✓       | 9/10
Prototype/MVP      | ✓✓✓       | 9/10
```

### When Microservices Win

```
Criteria                  | Microservices | Score
Large codebase            | ✓✓✓          | 9/10
Large team (> 10)         | ✓✓✓          | 9/10
Complex features          | ✓✓✓          | 9/10
High traffic              | ✓✓✓          | 9/10
Independent scaling       | ✓✓✓          | 9/10
Continuous deployment     | ✓✓✓          | 9/10
Technology flexibility    | ✓✓✓          | 9/10
```

---

## 🎓 Learning Objectives

After studying this project, you understand:

✅ **Database Design**
- Shared database problems
- Per-service database benefits
- Schema coupling issues

✅ **Service Communication**
- Direct calls (monolithic)
- HTTP calls (microservices)
- Loose vs tight coupling

✅ **Scalability**
- Vertical scaling limits
- Horizontal scaling benefits
- Resource isolation

✅ **Reliability**
- Single point of failure
- Failure isolation
- Cascading failures

✅ **Development**
- Team coordination
- Testing strategies
- Deployment complexity

✅ **Operations**
- Monitoring multiple services
- Distributed tracing
- Circuit breakers
- Exponential backoff

---

## 📁 File Organization

```
UTS-RPL-APPL-11423018/
│
├── monolit/                           ← ORIGINAL Laravel monolith
│   ├── app/
│   ├── routes/
│   ├── database/migrations/
│   └── ...
│
├── microserv/microservices/          ← GO MICROSERVICES (SOLUTION)
│   ├── shared/
│   │   ├── httpclient/
│   │   ├── logging/
│   │   └── metrics/
│   ├── user-service/
│   ├── appointment-service/
│   ├── medical-service/
│   ├── pharmacy-service/
│   ├── payment-service/
│   ├── analytics-service/
│   ├── api-gateway/
│   └── docker-compose.yml
│
├── Representasi Monolit java/        ← JAVA MONOLITHIC (EDUCATION)
│   ├── src/main/java/com/meditrack/
│   │   ├── database/
│   │   ├── models/
│   │   ├── services/
│   │   ├── controllers/
│   │   └── MediTrackMonolithApp.java
│   └── README.md
│
├── ARCHITECTURE_COMPARISON.md         ← THIS FILE
├── MONOLITHIC_ANTIPATTERNS.md         ← ANTI-PATTERNS EXPLAINED
└── ...other documentation
```

---

## 🚀 Next Steps

### For Learning
1. ✅ Read ARCHITECTURE_COMPARISON.md (5 min)
2. ✅ Read MONOLITHIC_ANTIPATTERNS.md (10 min)
3. ✅ Review Java monolithic code (15 min)
4. ✅ Review Go microservices code (20 min)
5. ✅ Understand differences (10 min)

### For Implementation
1. Study Go shared package (httpclient, logging)
2. Study circuit breaker pattern
3. Study exponential backoff retry
4. Study correlation ID tracing
5. Deploy and run Docker Compose stack

### For Mastery
1. Implement missing services
2. Add health checks
3. Add metrics collection
4. Add request logging
5. Add circuit breaker monitoring

---

## 🎓 Key Takeaways

### MONOLITHIC ❌ (This Folder)

**Strengths:**
- ✓ Simple to understand
- ✓ Easy to develop initially
- ✓ Simple deployment (one package)
- ✓ ACID transactions (guaranteed)

**Weaknesses:**
- ✗ Single point of failure
- ✗ Tight coupling
- ✗ Hard to scale
- ✗ Hard to maintain
- ✗ Technology locked-in

**Best For:**
- MVP/Startup
- Small team
- Simple app
- Low traffic

---

### MICROSERVICES ✅ (Other Folder)

**Strengths:**
- ✓ Loose coupling
- ✓ Independent scaling
- ✓ Fault isolation
- ✓ Technology flexibility
- ✓ Team independence

**Weaknesses:**
- ✗ Complex distributed system
- ✗ Network latency
- ✗ Operational overhead
- ✗ Data consistency challenges
- ✗ DevOps requirements

**Best For:**
- Large application
- Large team
- Complex features
- High traffic
- Continuous deployment

---

## 📞 Quick Reference

| Question | Monolithic | Microservices |
|----------|-----------|--------------|
| Database down? | App down | 1 service down |
| One service slow? | Affects all | Only that service |
| Add new feature? | Test all | Test one |
| Deploy? | Entire app | 1 service |
| New developer? | Learn everything | Learn 1 service |
| Scale high load? | Scale everything | Scale that service |
| Change user model? | Update 6 services | Update 2 services |

---

## 🏆 Conclusion

This project demonstrates **real architectural evolution**:

1. **Started:** Monolithic Laravel app
   - Simple to develop
   - Growing pains

2. **Evolved:** Microservices in Go
   - Distributed system
   - Better scalability
   - More operational complexity

3. **Documented:** Java simulation
   - Educational purposes
   - Clear anti-patterns
   - Contrast to solution

**Learning:** Understand why each decision was made, trade-offs involved, and when to use each approach.

---

**Version:** 1.0
**Created:** April 2026
**Purpose:** Educational Architecture Study
**Target Audience:** Software Engineering Students & Architects

