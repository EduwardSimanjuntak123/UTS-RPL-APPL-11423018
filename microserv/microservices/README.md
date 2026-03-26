# 🏥 MediTrack Microservices Architecture

## 📋 Deskripsi Proyek

**MediTrack** adalah transformasi dari aplikasi monolitik Laravel menjadi arsitektur microservices berbasis **Golang + Gin Framework**. Sistem ini menyediakan platform manajemen kesehatan terintegrasi dengan layanan independen untuk pengelolaan pengguna, janji temu, rekam medis, farmasi, pembayaran, dan analitik.

### 🎯 Transformasi Arsitektur
- **Dari**: Monolith Laravel (database terpusat)
- **Ke**: Microservices Golang (database per service)
- **UI**: Tetap menggunakan Laravel (frontend)
- **Backend**: Completely rewritten in Golang with REST APIs

---

## 🏗️ Struktur Microservices

```
microservices/
├── api-gateway/              # Central orchestrator (Port 3000)
├── user-service/             # Identity & Authentication (Port 3001)
├── appointment-service/      # Appointment scheduling (Port 3002)
├── medical-service/          # Medical records & EHR (Port 3003)
├── pharmacy-service/         # Drug stock & inventory (Port 3004)
├── payment-service/          # Invoices & payments (Port 3005)
├── analytics-service/        # Metrics & reporting (Port 3006)
├── docker-compose.yml        # Orchestration config
└── README.md                 # This file
```

---

## 🚀 Services Overview

### 1. **API Gateway** (Port 3000)
**Fungsi**: Central entry point untuk semua requests

**Features**:
- JWT authentication & authorization
- Request routing ke services yang sesuai
- Rate limiting (100 req/min per default)
- CORS handling
- Request/response logging
- Service health monitoring
- Load balancing ready

**Key Endpoints**:
```
GET  /health                   - Gateway health check
GET  /status                   - Detailed service status
POST /auth/login               - User login
POST /auth/register            - User registration
GET  /api/v1/*                 - Proxied to respective services
```

**Tech Stack**: Gin, JWT (golang-jwt), CORS middleware

---

### 2. **User Service** (Port 3001)
**Fungsi**: User management, authentication, RBAC

**Database Tables**:
- `users` - User profiles (patients, doctors, pharmacists, admins)
- `roles` - Role definitions (Doctor, Patient, Pharmacist, Admin)
- `permissions` - Permission definitions (read, write, delete)
- `role_permissions` - RBAC mapping
- `audit_logs` - Security event tracking

**Key Endpoints**:
```
POST   /users                  - Create new user
GET    /users/:id              - Get user details
GET    /users                  - List users (with pagination & filtering)
PUT    /users/:id              - Update user profile
DELETE /users/:id              - Delete user
POST   /auth/login             - Login (password verification)
GET    /health                 - Service health check
```

**Key Features**:
- Bcrypt password hashing (cost 11)
- Token generation ready
- Duplicate email prevention
- Role-based access control
- Audit trail logging
- SQL injection prevention (parameterized queries)

**Models**:
```go
User struct {
    ID, PatientID, DoctorID
    Name, Email, Password (hashed)
    Phone, Address
    Role, Status
    Specialty, LicenseNumber (for doctors)
    InsuranceProvider
}
```

---

### 3. **Appointment Service** (Port 3002)
**Fungsi**: Appointment scheduling & management

**Database Tables**:
- `appointments` - Appointment records
- `appointment_slots` - Doctor availability slots
- `appointment_notifications` - SMS/Email/Push notifications

**Key Endpoints**:
```
POST   /appointments           - Book new appointment
GET    /appointments/:id       - Get appointment details
GET    /patients/:id/appointments - List patient's appointments
PUT    /appointments/:id/status - Update appointment status
DELETE /appointments/:id       - Cancel appointment
GET    /health                 - Service health check
```

**Key Features**:
- 24-hour advance booking requirement
- Doctor conflict detection
- Appointment status tracking (scheduled, confirmed, completed, cancelled, rescheduled)
- Notification system integration
- Wait time calculation

**Statuses**: 'scheduled', 'confirmed', 'completed', 'cancelled', 'rescheduled'

---

### 4. **Medical Service** (Port 3003)
**Fungsi**: EHR (Electronic Health Records) management

**Database Tables**:
- `medical_records` - Patient medical history
- `prescriptions` - Drug prescriptions
- `lab_results` - Laboratory test results
- `clinical_notes` - Doctor notes during appointments

**Key Endpoints**:
```
POST   /medical-records        - Create medical record
GET    /patients/:id/medical-records - Get patient's records
POST   /prescriptions          - Create prescription
GET    /patients/:id/prescriptions - Get patient's prescriptions
POST   /lab-results            - Add lab test result
GET    /health                 - Service health check
```

**Key Features**:
- HIPAA-ready compliance structure
- Confidential record flagging
- Lab result status tracking
- Prescription fulfillment tracking
- Drug-disease interaction checking ready

**Prescription Statuses**: 'pending', 'fulfilled', 'cancelled'

---

### 5. **Pharmacy Service** (Port 3004)
**Fungsi**: Drug inventory & pharmacy management

**Database Tables**:
- `drugs` - Drug master data (with license numbers, expiry)
- `drug_stocks` - Stock levels per location
- `pharmacy_orders` - Customer orders
- `order_items` - Order line items
- `drug_inventory_log` - Stock transaction history

**Key Endpoints**:
```
POST   /drugs                  - Register new drug
GET    /drugs/:id              - Get drug details
POST   /stocks                 - Add/update stock
GET    /stocks/:drug_id        - Get stock levels
GET    /low-stock              - Get drugs below reorder level
POST   /orders                 - Create pharmacy order
GET    /orders/:id             - Get order details
GET    /health                 - Service health check
```

**Key Features**:
- Drug license number tracking
- Expiry date management
- Multi-location stock tracking
- Reorder level alerts
- Inventory audit trail
- Order status workflow (pending→processing→ready→picked_up→delivered)

**Payment Statuses**: 'unpaid', 'paid', 'refunded'

---

### 6. **Payment Service** (Port 3005)
**Fungsi**: Financial transactions & billing

**Database Tables**:
- `invoices` - Billing documents
- `payments` - Payment records
- `insurance_claims` - Insurance reimbursement claims
- `payment_proofs` - Payment verification documents
- `refunds` - Refund tracking

**Key Endpoints**:
```
POST   /invoices               - Create invoice
GET    /invoices/:id           - Get invoice details
POST   /payments               - Record payment
GET    /payments/:id           - Get payment details
POST   /payments/:id/confirm   - Confirm/complete payment
POST   /insurance-claims       - File insurance claim
GET    /insurance-claims/:id   - Get claim details
GET    /health                 - Service health check
```

**Key Features**:
- Automatic invoice numbering (INV-UUID format)
- Transaction ID generation
- Multi-method payment support
- Insurance claim workflow
- Refund processing
- Payment proof attachment

**Invoice Statuses**: 'draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'
**Claim Statuses**: 'submitted', 'under_review', 'approved', 'rejected', 'paid'

---

### 7. **Analytics Service** (Port 3006)
**Fungsi**: System monitoring & reporting

**Database Tables**:
- `service_metrics` - Performance metrics per service
- `user_analytics` - Daily user statistics
- `appointment_analytics` - Appointment metrics
- `revenue_analytics` - Financial metrics
- `health_indicators` - System health status
- `system_alerts` - Alert notifications

**Key Endpoints**:
```
POST   /metrics                - Record service metric
GET    /metrics/:service       - Get service metrics
GET    /health-indicators      - Get system health
GET    /alerts                 - Get active alerts
POST   /alerts                 - Create alert
GET    /dashboard/summary      - Get dashboard summary
GET    /health                 - Service health check
```

**Dashboard Summary Data**:
- Active users
- Total transactions
- Total revenue
- System uptime %
- Average response time
- Error rate %
- Pending appointments
- Pending payments

---

## 🗄️ Database Architecture

**Total: 6 Independent Databases (1 per service)**

```
meditrack_users         ← User Service
meditrack_appointments  ← Appointment Service
meditrack_medical       ← Medical Service
meditrack_pharmacy      ← Pharmacy Service
meditrack_payment       ← Payment Service
meditrack_analytics     ← Analytics Service
```

### Key Design Principles
✅ **Data Isolation**: Each service owns its data
✅ **No Shared Database**: Prevents tight coupling
✅ **Async Communication**: Rest APIs + event-based
✅ **Scalability**: Independent scaling per service
✅ **Resilience**: Failure in one service doesn't affect others

---

## 🐳 Docker Deployment

### Prerequisites
- Docker >= 20.10
- Docker Compose >= 1.29

### Quick Start

#### 1. Build & Run All Services
```bash
cd microservices
docker-compose up -d
```

This will:
- Create 6 MySQL databases
- Build 7 Go services
- Set up network connectivity
- Initialize health checks

#### 2. Verify Services Running
```bash
docker-compose ps
```

Expected output:
```
CONTAINER ID  IMAGE                          STATUS
...
meditrack-api-gateway         Up (healthy)
meditrack-user-service       Up (healthy)
meditrack-appointment-service Up (healthy)
meditrack-medical-service    Up (healthy)
meditrack-pharmacy-service   Up (healthy)
meditrack-payment-service    Up (healthy)
meditrack-analytics-service  Up (healthy)
```

#### 3. Check Gateway Health
```bash
curl http://localhost:3000/health
```

Response:
```json
{
  "status": "operational",
  "services": {
    "user": true,
    "appointment": true,
    "medical": true,
    "pharmacy": true,
    "payment": true,
    "analytics": true
  }
}
```

#### 4. Stop Services
```bash
docker-compose down
```

#### 5. View Logs
```bash
docker-compose logs -f api-gateway
docker-compose logs -f user-service
```

---

## 🔐 Authentication Flow

### 1. Register User
```bash
curl -X POST http://localhost:3000/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Dr. Budi",
    "email": "budi@hospital.com",
    "password": "secure_password123",
    "role": "doctor"
  }'
```

### 2. Login
```bash
curl -X POST http://localhost:3000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "budi@hospital.com",
    "password": "secure_password123"
  }'
```

Response:
```json
{
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "expires_in": "24h"
}
```

### 3. Use Token in Authenticated Requests
```bash
curl -X GET http://localhost:3000/api/v1/users/1 \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIs..."
```

---

## 📊 API Usage Examples

### Create Appointment
```bash
curl -X POST http://localhost:3000/api/v1/appointments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "doctor_id": 2,
    "appointment_date": "2024-04-15T14:30:00Z",
    "type": "consultation",
    "location": "Room 102",
    "duration": 30,
    "description": "General checkup"
  }'
```

### Get Patient Medical Records
```bash
curl -X GET "http://localhost:3000/api/v1/patients/1/medical-records" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Invoice
```bash
curl -X POST http://localhost:3000/api/v1/invoices \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "service_type": "consultation",
    "total_amount": 250000,
    "due_date": "2024-04-30T23:59:59Z",
    "item_details": "Doctor consultation - 30 mins"
  }'
```

### Record Payment
```bash
curl -X POST http://localhost:3000/api/v1/payments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "invoice_id": 1,
    "amount": 250000,
    "payment_method": "credit_card",
    "description": "Payment for consultation"
  }'
```

---

## 🔌 Service Communication

### Service URLs (Internal Network)
```
User Service:         http://user-service:3001
Appointment Service:  http://appointment-service:3002
Medical Service:      http://medical-service:3003
Pharmacy Service:     http://pharmacy-service:3004
Payment Service:      http://payment-service:3005
Analytics Service:    http://analytics-service:3006
API Gateway:          http://api-gateway:3000
```

### Service URLs (From Host)
```
User Service:         http://localhost:3001
Appointment Service:  http://localhost:3002
Medical Service:      http://localhost:3003
Pharmacy Service:     http://localhost:3004
Payment Service:      http://localhost:3005
Analytics Service:    http://localhost:3006
API Gateway:          http://localhost:3000
```

---

## 📈 Performance & Scalability

### Current Configuration
- **Database Connection Pool**: 25 max, 5 idle per service
- **Rate Limiting**: 100 requests/minute per client
- **Health Checks**: 30-second intervals
- **Response Timeout**: 10 seconds

### Scaling Recommendations

#### Horizontal Scaling
```bash
# Scale up API Gateway replicas
docker-compose up -d --scale api-gateway=3

# Scale up individual services
docker-compose up -d --scale appointment-service=2
```

#### Performance Tuning
1. **Database**: Increase connection pool based on load
2. **Cache**: Add Redis for frequently accessed data
3. **Load Balancing**: Implement Nginx/HAProxy frontend
4. **Message Queue**: Add RabbitMQ for async operations

---

## 🛠️ Development Setup

### Prerequisites
- Go 1.21+
- MySQL 8.0+
- Git

### Local Development (Without Docker)

#### 1. Create Databases
```sql
CREATE DATABASE meditrack_users;
CREATE DATABASE meditrack_appointments;
CREATE DATABASE meditrack_medical;
CREATE DATABASE meditrack_pharmacy;
CREATE DATABASE meditrack_payment;
CREATE DATABASE meditrack_analytics;
```

#### 2. Configure Environment Variables
```bash
# For each service, copy and edit .env.example
cp user-service/.env.example user-service/.env
# Edit with your local DB connection details
```

#### 3. Run Individual Services
```bash
# Terminal 1 - User Service
cd user-service
go mod download
go run cmd/main.go

# Terminal 2 - Appointment Service
cd appointment-service
go mod download
go run cmd/main.go

# ... repeat for other services
```

#### 4. Run API Gateway (after other services start)
```bash
cd api-gateway
go mod download
go run cmd/main.go
```

---

## 🐛 Troubleshooting

### Service Connection Issues
```bash
# Check if service is running
docker-compose ps user-service

# View service logs
docker-compose logs user-service

# Restart service
docker-compose restart user-service
```

### Database Connection Errors
```bash
# Verify MySQL is running
docker-compose ps mysql-user

# Check database credentials in .env files
cat user-service/.env
```

### Port Conflicts
```bash
# Find what's using port 3000
netstat -ano | findstr :3000  # Windows
lsof -i :3000                 # Linux/Mac

# Change port in docker-compose.yml
```

---

## 📝 Key Features

### Security
✅ JWT-based authentication
✅ Bcrypt password hashing
✅ SQL injection prevention (parameterized queries)
✅ CORS support
✅ Rate limiting per client
✅ Audit logging for sensitive operations

### Reliability
✅ Health checks (30-second intervals)
✅ Database connection pooling
✅ Service isolation & resilience
✅ Graceful shutdown
✅ Error recovery

### Maintainability
✅ Consistent code structure across services
✅ Environment-based configuration
✅ Docker containerization
✅ Comprehensive logging
✅ API versioning (/api/v1)

### Scalability
✅ Microservices architecture
✅ Independent databases
✅ Horizontal scaling ready
✅ Stateless services
✅ Load balancer compatible

---

## 🚀 Deployment Checklist

- [ ] Configure production environment variables
- [ ] Set strong JWT_SECRET in api-gateway
- [ ] Enable database backups
- [ ] Set up monitoring & alerting
- [ ] Configure rate limiting thresholds
- [ ] Set up log aggregation (ELK, Splunk, etc.)
- [ ] Enable SSL/TLS encryption
- [ ] Configure API versioning strategy
- [ ] Set up CI/CD pipeline
- [ ] Document API changes
- [ ] Train operations team
- [ ] Plan disaster recovery

---

## 📞 Integration Points

### Frontend (Laravel) Communication
```
Laravel App
    ↓
API Gateway (http://localhost:3000)
    ↓
Appropriate Microservice
```

### Service-to-Service Communication
```
Example: Appointment Service → User Service
GET http://user-service:3001/users/:id
```

### External Integrations Ready For
- Payment gateways (Stripe, PayPal)
- SMS providers (Twilio)
- Email services (SendGrid)
- Analytics platforms (Google Analytics)

---

## 📚 Project Structure

Each service follows this standardized pattern:

```
service-name/
├── cmd/
│   └── main.go              # Service entry point
├── config/
│   └── config.go            # Configuration management
├── internal/
│   ├── db/
│   │   └── db.go           # Database setup & queries
│   ├── handlers/
│   │   └── *_handler.go    # HTTP request handlers
│   └── models/
│       └── *.go            # Data models
├── migrations/              # Database schema scripts
├── .env.example            # Configuration template
├── Dockerfile              # Container image
├── go.mod                  # Go module definition
└── README.md              # Service documentation
```

---

## 📋 Configuration Reference

### Common Environment Variables
```
SERVICE_NAME=your-service
SERVICE_PORT=3001
DB_HOST=localhost
DB_PORT=3306
DB_USER=meditrack
DB_PASSWORD=password
DB_NAME=database_name
ENVIRONMENT=development|production
LOG_LEVEL=debug|info|warning|error
```

### API Gateway Specific
```
JWT_SECRET=your_secret_key
TOKEN_EXPIRY=24h
RATE_LIMIT=100
RATE_LIMIT_WINDOW=1m
CORS_ALLOWED_ORIGINS=http://localhost:3000,https://app.com
```

---

## 🎓 Learning Resources

### Recommended Topics
- Microservices Architecture
- REST API Design
- JWT Authentication
- Docker & Container Orchestration
- Database Normalization & Design
- Go/Golang Programming
- Service Mesh (future enhancement)
- Load Balancing Strategies

---

## 📄 License & Contact

MediTrack Microservices v1.0
Developed as part of UTS RPL APPL course

---

**Last Updated**: 2024
**Architecture Version**: 1.0
**Go Version**: 1.21+
**Database**: MySQL 8.0+

---

## 🎯 Next Steps

1. Deploy to development environment
2. Run integration tests
3. Set up monitoring & alerting
4. Configure CI/CD pipeline
5. Prepare for production deployment
6. Document API for frontend team
7. Set up disaster recovery plan
8. Plan for future enhancements (caching, message queues)

Selamat! Microservices MediTrack siap digunakan! 🚀
