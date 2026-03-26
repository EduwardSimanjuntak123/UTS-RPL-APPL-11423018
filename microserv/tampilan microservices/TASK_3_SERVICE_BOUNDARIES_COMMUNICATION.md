# TASK 3: SERVICE BOUNDARIES & INTER-SERVICE COMMUNICATION

**MediTrack Transformation - Case Study 2**  
**Date**: March 26, 2026  
**Technology Stack**: Golang (Gin Framework) + HTTP/REST  
**Focus**: Communication Patterns & Data Contracts

---

## 📋 EXECUTIVE SUMMARY

This document defines the **service boundaries**, **communication protocols**, **API contracts**, and **data contracts** for the MediTrack microservices architecture. Clear boundaries prevent data duplication and reduce coupling between services.

---

## 🎯 CORE PRINCIPLES FOR SERVICE BOUNDARIES

### 1. **Single Responsibility**
Each service owns specific business domain:
- **User Service**: User accounts, authentication, authorization
- **Appointment Service**: Scheduling logic, availability
- **Medical Service**: Patient medical data, prescriptions
- **Pharmacy Service**: Drug inventory, stock management
- **Payment Service**: Financial transactions, billing
- **Analytics Service**: Metrics, monitoring, reporting

### 2. **Data Ownership**
Each service is **sole owner** of its data:
```
User Service
└── Owns: users, roles, sessions tables
    └── Only User Service can modify

Appointment Service
└── Owns: appointments, doctor_availability tables
    └── Only Appointment Service can modify

Medical Service
└── Owns: medical_records, prescriptions, lab_results tables
    └── Only Medical Service can modify
```

### 3. **No Database Sharing**
- ❌ **WRONG**: Appointment Service directly queries User Service database
- ✅ **CORRECT**: Appointment Service calls User Service API

### 4. **Autonomous Service**
Each service runs independently:
- Has own database
- Has own business logic
- Has own error handling
- Can be updated independently

---

## 🔗 INTER-SERVICE COMMUNICATION PATTERNS

### Pattern 1: SYNCHRONOUS REST API CALLS

**When to Use**: Need immediate response (80% of cases)

#### Example: Appointment Service validates Patient ID

```go
// File: appointment-service/internal/handlers/appointment.go

package handlers

import (
    "github.com/gin-gonic/gin"
    "appointment-service/internal/services"
)

type AppointmentHandler struct {
    userService *services.UserService
}

func (h *AppointmentHandler) CreateAppointment(c *gin.Context) {
    var req struct {
        PatientID string `json:"patient_id" binding:"required"`
        DoctorID  string `json:"doctor_id" binding:"required"`
        Date      string `json:"date" binding:"required"`
    }
    
    c.ShouldBindJSON(&req)
    
    // SYNCHRONOUS: Call User Service to validate patient exists
    userResponse, err := h.userService.GetUser(req.PatientID)
    if err != nil || userResponse == nil {
        c.JSON(400, gin.H{"error": "Invalid patient ID"})
        return
    }
    
    // SYNCHRONOUS: Call User Service to validate doctor exists
    doctorResponse, err := h.userService.GetUser(req.DoctorID)
    if err != nil || doctorResponse.Role != "doctor" {
        c.JSON(400, gin.H{"error": "Invalid doctor ID"})
        return
    }
    
    // Create appointment after validations
    appointment := services.CreateAppointment(req)
    c.JSON(201, appointment)
}
```

**Flow Diagram**:
```
Client
  │
  ├─→ Appointment Service
  │        │
  │        ├─→ User Service (validate patient)
  │        │        └─→ Response
  │        │
  │        ├─→ User Service (validate doctor)
  │        │        └─→ Response
  │        │
  │        └─→ Create appointment in DB
  │
  └─ Response: Appointment created
```

---

### Pattern 2: ASYNCHRONOUS EVENT-DRIVEN (Future)

**When to Use**: Non-blocking operations (async validations, notifications)

#### Concept for Future Implementation:
```
Payment Service
  │
  ├─→ Process payment
  │
  └─→ Publish Event: "PaymentProcessed"
         │
         ├─→ Invoice Service: Listens & creates invoice
         ├─→ Analytics Service: Listens & records metric
         └─→ Notification Service: Sends confirmation email
```

**Implementation (Go Channel-based for now)**:
```go
// Publish event
eventBus.Publish("order:placed", OrderEvent{
    OrderID: 123,
    UserID: 456,
})

// Subscribe to event
eventBus.Subscribe("order:placed", func(event OrderEvent) {
    invoiceService.CreateInvoice(event)
})
```

---

### Pattern 3: REQUEST/RESPONSE WITH RETRY LOGIC

**When to Use**: Handling transient failures

#### Implementation with Exponential Backoff:

```go
// File: shared/httpClient.go

package shared

import (
    "net/http"
    "time"
)

type RetryConfig struct {
    MaxRetries      int
    InitialDelay    time.Duration
    MaxDelay        time.Duration
    BackoffMultiplier float64
}

func CallServiceWithRetry(url string, config RetryConfig) (*http.Response, error) {
    var resp *http.Response
    var err error
    delay := config.InitialDelay
    
    for attempt := 0; attempt <= config.MaxRetries; attempt++ {
        resp, err = http.Get(url)
        if err == nil && resp.StatusCode < 500 {
            return resp, nil
        }
        
        if attempt < config.MaxRetries {
            time.Sleep(delay)
            delay = time.Duration(float64(delay) * config.BackoffMultiplier)
            if delay > config.MaxDelay {
                delay = config.MaxDelay
            }
        }
    }
    
    return nil, err
}
```

---

## 📡 API CONTRACTS & DATA CONTRACTS

### Contract 1: User Service API

**Service Endpoint**: `http://user-service:3001`

#### Endpoint: GET /api/users/{user_id}

**Request**:
```http
GET http://user-service:3001/api/users/USR001 HTTP/1.1
Authorization: Bearer {internal_token}
```

**Response (200 OK)**:
```json
{
    "id": "USR001",
    "email": "patient@example.com",
    "name": "John Doe",
    "role": "patient",
    "status": "active",
    "created_at": "2026-01-15T10:30:00Z"
}
```

**Response (404 Not Found)**:
```json
{
    "error": "User not found",
    "user_id": "USR001"
}
```

**Data Contract**:
```json
{
    "$schema": "http://json-schema.org/draft-07/schema#",
    "type": "object",
    "properties": {
        "id": {"type": "string", "minLength": 4},
        "email": {"type": "string", "format": "email"},
        "name": {"type": "string"},
        "role": {"enum": ["patient", "doctor", "admin", "pharmacy"]}
    },
    "required": ["id", "email", "name", "role"]
}
```

#### Endpoint: POST /api/users/register

**Request**:
```json
{
    "email": "newuser@example.com",
    "password": "SecurePassword123!",
    "name": "Alice Smith",
    "role": "patient"
}
```

**Response (201 Created)**:
```json
{
    "id": "USR002",
    "email": "newuser@example.com",
    "name": "Alice Smith",
    "role": "patient",
    "status": "active",
    "created_at": "2026-03-26T14:20:00Z"
}
```

**Validation Rules**:
- Email: Must be unique, valid format
- Password: Minimum 8 chars, must include uppercase, number, special char
- Name: Maximum 100 characters
- Role: Must be valid role enum

---

### Contract 2: Appointment Service API

**Service Endpoint**: `http://appointment-service:3002`

#### Endpoint: POST /api/appointments

**Request**:
```json
{
    "patient_id": "USR001",
    "doctor_id": "USR002",
    "appointment_date": "2026-04-15",
    "appointment_time": "14:30",
    "reason": "General checkup"
}
```

**Response (201 Created)**:
```json
{
    "id": "APT001",
    "patient_id": "USR001",
    "doctor_id": "USR002",
    "appointment_date": "2026-04-15T14:30:00Z",
    "status": "confirmed",
    "reason": "General checkup",
    "created_at": "2026-03-26T10:00:00Z"
}
```

**Validation**:
- Appointment date must be in future
- Doctor must have availability at that time
- Patient ID must exist (validated via User Service)
- Doctor ID must exist and have role "doctor"

#### Endpoint: GET /api/appointments/{appointment_id}

**Data Contract**:
```json
{
    "type": "object",
    "properties": {
        "id": {"type": "string"},
        "patient_id": {"type": "string"},
        "doctor_id": {"type": "string"},
        "appointment_date": {"type": "string", "format": "date-time"},
        "status": {"enum": ["pending", "confirmed", "completed", "cancelled"]},
        "reason": {"type": "string"},
        "notes": {"type": "string"}
    }
}
```

---

### Contract 3: Medical Service API

**Service Endpoint**: `http://medical-service:3003`

#### Endpoint: POST /api/prescriptions

**Request**:
```json
{
    "patient_id": "USR001",
    "doctor_id": "USR002",
    "medication_name": "Amoxicillin",
    "dosage": "500mg",
    "frequency": "3 times daily",
    "duration": "7 days"
}
```

**Response (201 Created)**:
```json
{
    "id": "PRE001",
    "patient_id": "USR001",
    "doctor_id": "USR002",
    "medication_name": "Amoxicillin",
    "dosage": "500mg",
    "frequency": "3 times daily",
    "duration": "7 days",
    "status": "active",
    "issued_date": "2026-03-26T10:00:00Z",
    "expiry_date": "2026-06-24T10:00:00Z"
}
```

**Data Contract**:
```json
{
    "type": "object",
    "properties": {
        "id": {"type": "string"},
        "patient_id": {"type": "string"},
        "doctor_id": {"type": "string"},
        "medication_name": {"type": "string"},
        "dosage": {"type": "string"},
        "frequency": {"type": "string"},
        "duration": {"type": "string"},
        "status": {"enum": ["active", "completed", "cancelled"]}
    }
}
```

---

### Contract 4: Pharmacy Service API

**Service Endpoint**: `http://pharmacy-service:3004`

#### Endpoint: GET /api/drugs/{drug_id}

**Response (200 OK)**:
```json
{
    "id": "DRG001",
    "name": "Amoxicillin",
    "category": "Antibiotic",
    "strength": "500mg",
    "manufacturer": "Generic Pharma",
    "price": 5.50,
    "stock_quantity": 450,
    "reorder_level": 100
}
```

**Data Contract**:
```json
{
    "type": "object",
    "properties": {
        "id": {"type": "string"},
        "name": {"type": "string"},
        "category": {"type": "string"},
        "strength": {"type": "string"},
        "price": {"type": "number", "minimum": 0},
        "stock_quantity": {"type": "integer", "minimum": 0},
        "reorder_level": {"type": "integer"}
    }
}
```

---

### Contract 5: Payment Service API

**Service Endpoint**: `http://payment-service:3005`

#### Endpoint: POST /api/payments

**Request**:
```json
{
    "user_id": "USR001",
    "appointment_id": "APT001",
    "amount": 75.50,
    "payment_method": "credit_card",
    "transaction_id": "TXN123456"
}
```

**Response (201 Created)**:
```json
{
    "id": "PAY001",
    "user_id": "USR001",
    "appointment_id": "APT001",
    "amount": 75.50,
    "status": "completed",
    "transaction_id": "TXN123456",
    "payment_date": "2026-03-26T10:00:00Z"
}
```

**Data Contract**:
```json
{
    "type": "object",
    "properties": {
        "id": {"type": "string"},
        "user_id": {"type": "string"},
        "amount": {"type": "number", "minimum": 0.01},
        "status": {"enum": ["pending", "processing", "completed", "failed", "refunded"]},
        "payment_method": {"enum": ["credit_card", "debit_card", "bank_transfer", "cash"]},
        "payment_date": {"type": "string", "format": "date-time"}
    }
}
```

---

### Contract 6: Analytics Service API

**Service Endpoint**: `http://analytics-service:3006`

#### Endpoint: POST /api/metrics

**Request**:
```json
{
    "service_name": "appointment-service",
    "metric_type": "request_count",
    "value": 1,
    "tags": {
        "endpoint": "/api/appointments",
        "status_code": 201
    }
}
```

**Data Contract**:
```json
{
    "type": "object",
    "properties": {
        "service_name": {"type": "string"},
        "metric_type": {"type": "string"},
        "value": {"type": "number"},
        "timestamp": {"type": "string", "format": "date-time"},
        "tags": {"type": "object"}
    }
}
```

---

## 🔄 SERVICE DEPENDENCY GRAPH

### Direct Dependencies

```
User Service
└── (No dependencies)
    └── Provides: User validation, authentication

Appointment Service
├── Depends on: User Service
│   └── Validates: patient_id, doctor_id
└── Provides: Appointment data

Medical Service
├── Depends on: User Service
│   └── Validates: patient_id, doctor_id
├── Depends on: Appointment Service (optional)
│   └── Links records to appointments
└── Provides: Medical records, prescriptions

Pharmacy Service
├── Depends on: Medical Service
│   └── Gets: Prescriptions to fulfill
├── Depends on: Appointment Service (optional)
│   └── Links: Drugs to appointment
└── Provides: Drug inventory, stock data

Payment Service
├── Depends on: User Service
│   └── Validates: user_id
├── Depends on: Appointment Service
│   └── Gets: Appointment cost/details
├── Depends on: Pharmacy Service
│   └── Gets: Drug prices
└── Provides: Payment records, invoices

Analytics Service
├── Depends on: All Services (for metrics)
│   └── Collects: Performance data
└── Provides: Metrics, reports, health status

API Gateway
├── Depends on: All Services
├── Routes: Requests to appropriate service
└── Provides: Single entry point
```

---

## 📊 COMMUNICATION MATRIX

| From | To | Method | Reason | Retry |
|------|----|----|-------|-------|
| Appointment | User | GET /api/users/{id} | Validate patient | Yes |
| Appointment | User | GET /api/users/{id} | Validate doctor | Yes |
| Medical | User | GET /api/users/{id} | Validate doctor | Yes |
| Pharmacy | Medical | GET /api/prescriptions | Fulfill prescription | Yes |
| Payment | User | GET /api/users/{id} | Validate user | Yes |
| Payment | Appointment | GET /api/appointments/{id} | Get cost details | Yes |
| Payment | Pharmacy | GET /api/drugs/{id} | Get drug prices | Yes |
| Analytics | All | POST /api/metrics | Record metrics | No |

---

## ⚠️ KNOWN SERVICE CALL PATTERNS

### Pattern: Cascade Lookups
```go
// ACCEPTABLE: Appointment → User validation
func CreateAppointment(patientID string) {
    user := userService.GetUser(patientID)  // Sync call OK
    if user == nil {
        return error
    }
    // Create appointment
}
```

### Pattern: AVOID Circular Dependencies
```
// ❌ BAD: Circular dependency
User Service calls Payment Service
Payment Service calls User Service  // CIRCULAR!

// ✅ GOOD: One-way dependency only
Appointment → User (one direction)
Only Appointment calls User, never reverse
```

---

## 🛡️ BOUNDARY ENFORCEMENT

### Rules for Service Boundaries

1. **No Database Access Across Services**
   ```go
   // ❌ WRONG
   userDB.Query("SELECT * FROM users WHERE id = ?", userID)
   
   // ✅ CORRECT
   userService.GetUser(userID)  // Via API
   ```

2. **No Shared Models/Entities**
   ```go
   // ❌ WRONG
   import "user-service/models"
   u := models.User{}
   
   // ✅ CORRECT
   // Each service has own models
   // Use DTOs for communication
   ```

3. **Time-Limited Calls**
   ```go
   // Set timeout for inter-service calls
   ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
   defer cancel()
   response, err := userService.GetUserWithContext(ctx, userID)
   ```

4. **Graceful Degradation**
   ```go
   // If User Service is down, don't fail appointment creation
   user, err := userService.GetUser(userID)
   if err != nil {
       // Log warning, but continue with appointment creation
       // Cache might have user data
       log.Warn("User service unavailable, using cache")
   }
   ```

---

## 📝 VERSIONING STRATEGY

### API Versioning

```
/api/v1/users          ← Current version
/api/v2/users          ← New version (backward compatible)
```

### Breaking Changes
- Old version: Continues to work for 6 months
- New version: Supports new features
- Clients gradually migrate

### Example:
```go
// v1: Simple user info
GET /api/v1/users/USR001
Response: {id, email, name, role}

// v2: Enhanced user info
GET /api/v2/users/USR001
Response: {id, email, name, role, profile_pic, phone, address}
```

---

## 🚨 ERROR HANDLING ACROSS SERVICES

### Standard Error Response Format

```json
{
    "error": true,
    "code": "USER_NOT_FOUND",
    "message": "User with ID USR001 not found",
    "status_code": 404,
    "timestamp": "2026-03-26T10:00:00Z",
    "request_id": "REQ123456"
}
```

### HTTP Status Code Mapping

| Code | Meaning | Action |
|------|---------|--------|
| 400 | Bad Request | Validation failed, check input |
| 401 | Unauthorized | Invalid/missing token |
| 403 | Forbidden | Authenticated but no permission |
| 404 | Not Found | Resource doesn't exist |
| 409 | Conflict | Duplicate record |
| 500 | Server Error | Unexpected error, retry |
| 503 | Service Unavailable | Service down, retry later |

---

## ✅ VERIFICATION CHECKLIST

- [x] Each service has clear ownership of data
- [x] No direct database access across services
- [x] All external calls go through APIs
- [x] API contracts are well-defined
- [x] Error handling is standardized
- [x] No circular dependencies
- [x] Timeout limits set on cross-service calls
- [x] Versioning strategy in place
- [x] Graceful degradation implemented

---

## 📌 CONCLUSION

Clear service boundaries and well-defined communication protocols ensure:
- **Loose Coupling**: Services can evolve independently
- **High Cohesion**: Related logic stays together
- **Maintainability**: Clear contracts reduce bugs
- **Scalability**: Each service can scale independently
- **Resilience**: Failures isolated to one service

**Status**: All boundaries and contracts defined and ready for implementation.

---

**Next Steps**: Proceed to TASK 4 for deployment strategy and infrastructure setup.

