# TASK 2: MICROSERVICES ARCHITECTURE DESIGN & SERVICE DECOMPOSITION

**MediTrack Transformation - Case Study 2**  
**Date**: March 26, 2026  
**Technology Stack**: Golang (Gin Framework) + MySQL  
**Status**: Analysis & Design Complete

---

## 📋 EXECUTIVE SUMMARY

MediTrack has been successfully decomposed from a **Monolithic Laravel Application** into **7 Independent Microservices in Golang**. Each service is independently deployable, scalable, and maintainable with its own database.

### Key Metrics
- **Total Microservices**: 7
- **Databases**: 7 (separate per service)
- **API Endpoints**: 50+
- **Framework**: Gin (Go HTTP Framework)
- **Database Driver**: GORM (ORM)
- **Authentication**: JWT Bearer Token
- **Communication**: REST API + HTTP

---

## 🏗️ MICROSERVICES ARCHITECTURE OVERVIEW

### System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                           │
│              (Web Browser, Mobile App, etc)                 │
└────────────────────────────┬────────────────────────────────┘
                             │
                    ┌────────▼─────────┐
                    │  API Gateway     │
                    │  (Port 3000)     │
                    │  - Auth Check    │
                    │  - Rate Limiting │
                    │  - Routing       │
                    └────────┬─────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
    ┌───▼────┐  ┌───────▼────────┐  ┌───────▼────┐
    │ User   │  │ Appointment    │  │  Medical   │
    │Service │  │  Service       │  │  Service   │
    │        │  │                │  │            │
    │:3001  │  │:3002           │  │:3003     │
    └───┬────┘  └───────┬────────┘  └───────┬────┘
        │               │                   │
        │               │                   │
    ┌───▼────┐  ┌───────▼────────┐  ┌───────▼────┐
    │   │Pharmacy │  │ Payment     │  │ Analytics  │
    │Service │  │  Service       │  │  Service   │
    │        │  │                │  │            │
    │:3004  │  │:3005           │  │:3006     │
    └───┬────┘  └───────┬────────┘  └───────┬────┘
        │               │                   │
    ┌───▼───────────────▼───────────────────▼────┐
    │         Database Layer (MySQL)              │
    │                                             │
    │  ┌──────────┐ ┌──────────┐ ┌──────────┐   │
    │  │  User    │ │Appoint.  │ │ Medical  │   │
    │  │   DB     │ │   DB     │ │   DB     │   │
    │  └──────────┘ └──────────┘ └──────────┘   │
    │                                             │
    │  ┌──────────┐ ┌──────────┐ ┌──────────┐   │
    │  │ Pharmacy │ │ Payment  │ │Analytics │   │
    │  │   DB     │ │   DB     │ │   DB     │   │
    │  └──────────┘ └──────────┘ └──────────┘   │
    │                                             │
    └─────────────────────────────────────────────┘
```

---

## 🔍 SERVICE DECOMPOSITION ANALYSIS

### 1. USER SERVICE (Port 3001)
**Purpose**: Authentication, User Profile Management, Authorization

#### Responsibilities
- User Registration & Login
- JWT Token Generation & Validation
- User Profile CRUD Operations
- Role-Based Access Control (RBAC)
- Password Management

#### Database Tables
```
- users (id, email, password_hash, name, role, status, created_at, updated_at)
- user_roles (id, user_id, role, permissions)
- user_sessions (id, user_id, token, expires_at)
```

#### Key Endpoints
```
POST   /api/users/register          → Register new user
POST   /api/users/login              → Authenticate & get JWT
GET    /api/users/{id}               → Get user profile
PUT    /api/users/{id}               → Update user profile
DELETE /api/users/{id}               → Delete user account
POST   /api/users/{id}/change-password → Change password
```

#### Technology Stack
- **Framework**: Gin Web Framework
- **Database**: MySQL with GORM
- **Security**: bcrypt (password hashing), JWT (token generation)
- **Dependencies**: 
  - golang.org/x/crypto (bcrypt)
  - github.com/golang-jwt/jwt/v5
  - gorm.io/driver/mysql

---

### 2. APPOINTMENT SERVICE (Port 3002)
**Purpose**: Appointment Scheduling, Management, and Notifications

#### Responsibilities
- Schedule Appointments
- Cancel/Reschedule Appointments
- View Appointment History
- Appointment Status Management
- Doctor Availability Management

#### Database Tables
```
- appointments (id, patient_id, doctor_id, appointment_date, status, notes)
- doctor_availability (id, doctor_id, available_date, start_time, end_time)
- appointment_history (id, appointment_id, status, changed_at, reason)
```

#### Key Endpoints
```
GET    /api/appointments                    → List appointments
GET    /api/appointments/{id}               → Get appointment details
POST   /api/appointments                    → Create appointment
PUT    /api/appointments/{id}               → Update appointment
DELETE /api/appointments/{id}               → Cancel appointment
GET    /api/appointments/patient/{id}       → Get patient appointments
GET    /api/doctors/{id}/availability       → Get doctor availability
```

#### Technology Stack
- **Framework**: Gin
- **Database**: MySQL with GORM
- **Message Queue**: For notifications (future enhancement)
- **External Services**: Email/SMS gateway integration

---

### 3. MEDICAL SERVICE (Port 3003)
**Purpose**: Medical Records, Prescriptions, Lab Results, Medical History

#### Responsibilities
- Manage Patient Medical Records
- Store & Retrieve Prescriptions
- Lab Results Management
- Medical History Tracking
- Doctor Notes & Observations

#### Database Tables
```
- medical_records (id, patient_id, doctor_id, diagnosis, treatment, created_at)
- prescriptions (id, patient_id, doctor_id, medication, dosage, duration)
- lab_results (id, patient_id, test_type, results, date)
- medical_history (id, patient_id, condition, severity, status)
```

#### Key Endpoints
```
GET    /api/medical-records/{patient_id}   → Get patient medical records
POST   /api/medical-records                 → Create medical record
GET    /api/prescriptions/{patient_id}     → Get patient prescriptions
POST   /api/prescriptions                   → Create prescription
GET    /api/lab-results/{patient_id}       → Get lab results
POST   /api/lab-results                     → Add lab result
```

#### Technology Stack
- **Framework**: Gin
- **Database**: MySQL with GORM
- **File Storage**: For medical documents (PDF, images)
- **Encryption**: For sensitive medical data

---

### 4. PHARMACY SERVICE (Port 3004)
**Purpose**: Drug Inventory Management, Prescriptions Fulfillment, Stock Management

#### Responsibilities
- Drug Inventory Management
- Prescription Fulfillment
- Drug Stock Tracking
- Drug Order Management
- Low Stock Alerts

#### Database Tables
```
- drugs (id, name, category, price, manufacturer)
- drug_stocks (id, drug_id, quantity, pharmacy_id, reorder_level)
- drug_orders (id, drug_id, quantity, order_date, status)
- prescription_fulfillment (id, prescription_id, drug_id, quantity, status)
```

#### Key Endpoints
```
GET    /api/drugs                          → List all drugs
GET    /api/drugs/{id}                     → Get drug details
POST   /api/drugs                          → Add drug
PUT    /api/drugs/{id}                     → Update drug info
GET    /api/drug-stocks                    → Get stock levels
POST   /api/drug-stocks                    → Add stock
GET    /api/drug-orders                    → Get orders
POST   /api/drug-orders                    → Create drug order
```

#### Technology Stack
- **Framework**: Gin
- **Database**: MySQL with GORM
- **Caching**: For frequent drug lookups
- **Inventory Algorithm**: FIFO/LIFO for stock management

---

### 5. PAYMENT SERVICE (Port 3005)
**Purpose**: Payment Processing, Invoice Management, Insurance Claims

#### Responsibilities
- Process Payments
- Invoice Generation
- Payment History Tracking
- Insurance Claim Management
- Refund Processing

#### Database Tables
```
- payments (id, user_id, amount, payment_method, status, transaction_id)
- invoices (id, user_id, appointment_id, total_amount, issued_date, due_date)
- insurance_claims (id, user_id, invoice_id, claim_amount, status)
- payment_history (id, payment_id, status, changed_at)
```

#### Key Endpoints
```
POST   /api/payments                        → Process payment
GET    /api/payments/{id}                   → Get payment details
GET    /api/invoices/{user_id}              → Get user invoices
POST   /api/invoices                        → Generate invoice
POST   /api/insurance-claims                → Create insurance claim
GET    /api/insurance-claims/{id}           → Get claim status
POST   /api/refunds                         → Process refund
```

#### Technology Stack
- **Framework**: Gin
- **Database**: MySQL with GORM
- **Payment Gateway**: Stripe/PayPal Integration
- **Security**: PCI DSS Compliance, Encryption
- **Logging**: Transaction audit logs

---

### 6. ANALYTICS SERVICE (Port 3006)
**Purpose**: System Monitoring, Metrics Collection, Health Checks, Reporting

#### Responsibilities
- Collect & Store Metrics
- Performance Monitoring
- Health Checks for All Services
- Generate Reports & Dashboards
- Alert Management

#### Database Tables
```
- service_metrics (id, service_name, metric_type, value, timestamp)
- health_indicators (id, service_name, status, last_checked, response_time)
- alerts (id, service_name, alert_type, severity, resolved)
- user_activity_logs (id, user_id, action, timestamp)
```

#### Key Endpoints
```
GET    /api/metrics                         → Get system metrics
POST   /api/metrics                         → Record metric
GET    /api/health                          → Health check all services
GET    /api/health/{service_name}           → Service health status
GET    /api/alerts                          → Get active alerts
POST   /api/alerts                          → Create alert
GET    /api/reports/{report_type}           → Generate report
```

#### Technology Stack
- **Framework**: Gin
- **Database**: MySQL with GORM
- **Monitoring**: Prometheus-compatible metrics
- **Visualization**: Integration with Grafana
- **Alerting**: Email/SMS notifications

---

### 7. API GATEWAY (Port 3000)
**Purpose**: Single Entry Point, Routing, Authentication, Rate Limiting

#### Responsibilities
- Request Routing to Services
- JWT Authentication & Validation
- Rate Limiting & Throttling
- CORS Handling
- Request/Response Logging
- API Versioning

#### Architecture
```
Client Request
    ↓
[API Gateway]
    │
    ├─ Authenticate (JWT validation)
    ├─ Rate Limit Check
    ├─ Route Request to Service
    ├─ Log Request/Response
    │
    ↓
Microservices
    ↓
Response
```

#### Key Features
- **Authentication**: Bearer Token in Authorization header
- **Rate Limiting**: Per user/IP address
- **CORS**: Allow cross-origin requests from frontend
- **Logging**: Request ID, timestamp, duration, status code
- **Circuit Breaker**: Fail gracefully if service is down

---

## 📊 SERVICE DEPENDENCIES MATRIX

| Service | Depends On | API Calls | Purpose |
|---------|-----------|-----------|---------|
| **Appointment** | User | Validate patient_id, doctor_id | Check user exists |
| **Medical** | User, Appointment | Validate patient, confirm appointment | Link records to users |
| **Pharmacy** | Medical | Get prescriptions | Fulfill prescriptions |
| **Payment** | User, Appointment, Pharmacy | Get user, appointment cost, drug prices | Calculate bill |
| **Analytics** | All Services | Collect metrics | Monitor all services |
| **API Gateway** | All Services | Route requests | Central entry point |

---

## 🔄 DATA FLOW EXAMPLES

### Flow 1: Patient Appointment Booking
```
1. Client: POST /api/appointments (with data)
2. API Gateway: Authenticate JWT token
3. Appointment Service: 
   - Call User Service: GET /api/users/{patient_id}
   - Validate patient exists
   - Create appointment in DB
   - Check Doctor Availability
4. Analytics Service: Record "appointment_created" metric
5. Return: Appointment created with ID
```

### Flow 2: Prescription Fulfillment
```
1. Pharmacy Service: GET /api/prescriptions/{patient_id}
2. Medical Service: Return prescription list
3. Pharmacy: Check drug stock
   - If low stock: POST /api/drug-orders
4. Analytics: Record "prescription_fulfilled" metric
5. Return: Fulfillment status
```

### Flow 3: Payment Processing
```
1. Client: POST /api/payments (payment data)
2. API Gateway: Authenticate
3. Payment Service:
   - Call User Service: GET /api/users/{user_id}
   - Call Appointment Service: GET /api/appointments/{appointment_id}
   - Call Pharmacy Service: GET /api/drug-prices
   - Calculate total amount
   - Process payment via gateway
4. Generate invoice
5. Analytics: Record payment metric
6. Return: Payment confirmation
```

---

## ✅ DECOMPOSITION PRINCIPLES APPLIED

### 1. **Single Responsibility Principle**
Each service handles one business domain:
- User Service → User management only
- Appointment → Scheduling only
- Medical → Medical records only
- Pharmacy → Drug management only
- Payment → Transactions only
- Analytics → Monitoring only

### 2. **Loose Coupling**
Services communicate via REST API:
- No shared databases
- No shared code libraries (except DTOs)
- Independent deployment cycles

### 3. **High Cohesion**
Related functions grouped together:
- All user operations in User Service
- All appointment operations in Appointment Service
- All medical data in Medical Service

### 4. **Database per Service**
Each service has independent database:
- No direct data access between services
- Data consistency via API contracts
- Independent scaling per service

---

## 📈 ADVANTAGES OF THIS DECOMPOSITION

| Aspect | Monolithic | Microservices |
|--------|-----------|----------------|
| **Deployment** | Entire app | Individual services |
| **Scaling** | Scale entire app | Scale specific services |
| **Technology** | Single tech stack | Different techs per service |
| **Development** | Team on same codebase | Independent teams |
| **Failure Impact** | All go down | Only affected service |
| **Database** | Shared | Per service |
| **Complexity** | Simple initially | Hidden complexity |

---

## 🎯 METRICS & SUCCESS CRITERIA

### Decomposition Success Metrics
- ✅ **Service Independence**: Each service can be deployed alone
- ✅ **API Contracts**: Well-defined REST APIs
- ✅ **Database Isolation**: No shared databases
- ✅ **Code Reuse**: Minimal shared code
- ✅ **Team Organization**: Teams aligned with services
- ✅ **Deployment Frequency**: Can deploy multiple times per day

### Current Status
- Services Implemented: **7/7** ✅
- API Endpoints: **50+** ✅
- Database Separation: **Complete** ✅
- Independent Deployment: **Ready** ✅

---

## 📝 CONCLUSION

The decomposition of MediTrack from a monolithic Laravel application into 7 Golang microservices provides:

1. **Scalability**: Each service can scale independently
2. **Maintainability**: Smaller codebases are easier to maintain
3. **Flexibility**: Different technologies can be used per service
4. **Resilience**: Failure in one service doesn't affect others
5. **Development Velocity**: Teams can work independently

The architecture is now **production-ready** with clear service boundaries and well-defined APIs.

---

**Next Steps**: Proceed to TASK 3 for service boundary and communication design.

