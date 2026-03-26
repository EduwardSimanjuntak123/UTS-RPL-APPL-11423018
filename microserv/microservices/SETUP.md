# Microservices Setup Guide

This guide provides step-by-step instructions to set up and run the MediTrack microservices architecture without Docker.

## System Requirements

- **Go**: 1.21 or higher
- **MySQL**: 8.0 or higher
- **Node.js**: 16+ (optional, for frontend integration)
- **Environment**: Linux, macOS, or Windows

## Database Configuration

All services are configured to use the same database host and credentials:

```
DB_HOST=localhost
DB_PORT=3307
DB_USER=root
DB_PASSWORD=(empty)
```

### 1. MySQL Database Setup

Ensure MySQL is running on port 3307. If your MySQL is on a different port, update all `.env` files accordingly.

#### Create Databases

```sql
CREATE DATABASE IF NOT EXISTS meditrack_users;
CREATE DATABASE IF NOT EXISTS meditrack_appointments;
CREATE DATABASE IF NOT EXISTS meditrack_medical;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_payment;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;
```

#### Run Migrations

For each service, execute its migration SQL file to create tables:

```bash
# User Service
mysql -h localhost -P 3307 -u root meditrack_users < user-service/migrations/001_create_users_table.sql

# Appointment Service
mysql -h localhost -P 3307 -u root meditrack_appointments < appointment-service/migrations/001_create_appointments_table.sql

# Medical Service
mysql -h localhost -P 3307 -u root meditrack_medical < medical-service/migrations/001_create_medical_tables.sql

# Pharmacy Service
mysql -h localhost -P 3307 -u root meditrack_pharmacy < pharmacy-service/migrations/001_create_pharmacy_tables.sql

# Payment Service
mysql -h localhost -P 3307 -u root meditrack_payment < payment-service/migrations/001_create_payment_tables.sql

# Analytics Service
mysql -h localhost -P 3307 -u root meditrack_analytics < analytics-service/migrations/001_create_analytics_tables.sql
```

## Service Architecture

### Microservices Overview

| Service | Port | Database | Purpose |
|---------|------|----------|---------|
| API Gateway | 3000 | None | Central routing, JWT auth, rate limiting |
| User Service | 3001 | meditrack_users | Authentication, user management, roles |
| Appointment Service | 3002 | meditrack_appointments | Scheduling, appointment management |
| Medical Service | 3003 | meditrack_medical | EHR, prescriptions, lab results |
| Pharmacy Service | 3004 | meditrack_pharmacy | Drug inventory, ordering |
| Payment Service | 3005 | meditrack_payment | Invoicing, payments, insurance claims |
| Analytics Service | 3006 | meditrack_analytics | Metrics, dashboards, monitoring |

## Installation & Setup

### 1. Install Go Dependencies for All Services

```bash
cd microservices

# User Service
cd user-service && go mod download && cd ..

# Appointment Service
cd appointment-service && go mod download && cd ..

# Medical Service
cd medical-service && go mod download && cd ..

# Pharmacy Service
cd pharmacy-service && go mod download && cd ..

# Payment Service
cd payment-service && go mod download && cd ..

# Analytics Service
cd analytics-service && go mod download && cd ..

# API Gateway
cd api-gateway && go mod download && cd ..
```

### 2. Run Each Service

On Windows, use Git Bash or PowerShell. Open separate terminal windows for each service:

**Terminal 1 - User Service**
```bash
cd user-service
go run cmd/main.go
```

**Terminal 2 - Appointment Service**
```bash
cd appointment-service
go run cmd/main.go
```

**Terminal 3 - Medical Service**
```bash
cd medical-service
go run cmd/main.go
```

**Terminal 4 - Pharmacy Service**
```bash
cd pharmacy-service
go run cmd/main.go
```

**Terminal 5 - Payment Service**
```bash
cd payment-service
go run cmd/main.go
```

**Terminal 6 - Analytics Service**
```bash
cd analytics-service
go run cmd/main.go
```

**Terminal 7 - API Gateway (Run Last)**
```bash
cd api-gateway
go run cmd/main.go
```

### 3. Verify Services Are Running

```bash
# Check if API Gateway is responding
curl http://localhost:3000/health

# Check individual services
curl http://localhost:3001/health  # User Service
curl http://localhost:3002/health  # Appointment Service
curl http://localhost:3003/health  # Medical Service
curl http://localhost:3004/health  # Pharmacy Service
curl http://localhost:3005/health  # Payment Service
curl http://localhost:3006/health  # Analytics Service
```

## REST API Endpoints

### Authentication (via API Gateway)

**Register New User**
```bash
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securepassword",
  "role": "patient",
  "phone": "555-1234",
  "address": "123 Main St"
}
```

**Login**
```bash
POST /auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "securepassword"
}
```

Response:
```json
{
  "message": "Login successful",
  "token": "token_xxxx-xxxx-xxxx",
  "user": {
    "id": 1,
    "email": "john@example.com",
    "role": "patient"
  }
}
```

### User Service Endpoints

```
GET    /api/v1/users              - List all users
POST   /api/v1/users              - Create new user
GET    /api/v1/users/:id          - Get user by ID
PUT    /api/v1/users/:id          - Update user
DELETE /api/v1/users/:id          - Delete user

GET    /api/v1/roles              - List all roles
POST   /api/v1/roles              - Create role
GET    /api/v1/audit-logs         - View audit logs
```

### Appointment Service Endpoints

```
GET    /api/v1/appointments       - List appointments
POST   /api/v1/appointments       - Create appointment
GET    /api/v1/appointments/:id   - Get appointment
PUT    /api/v1/appointments/:id   - Update appointment
DELETE /api/v1/appointments/:id   - Cancel appointment

GET    /api/v1/slots              - Get available slots
POST   /api/v1/slots              - Create doctor slots
```

### Medical Service Endpoints

```
GET    /api/v1/medical-records    - List medical records
POST   /api/v1/medical-records    - Create record
GET    /api/v1/medical-records/:id - Get record
PUT    /api/v1/medical-records/:id - Update record

GET    /api/v1/prescriptions      - List prescriptions
POST   /api/v1/prescriptions      - Create prescription
GET    /api/v1/lab-results        - List lab results
POST   /api/v1/lab-results        - Create lab result
```

### Pharmacy Service Endpoints

```
GET    /api/v1/drugs              - List all drugs
POST   /api/v1/drugs              - Add new drug
GET    /api/v1/stocks             - View drug stocks
POST   /api/v1/stocks             - Update stock
GET    /api/v1/stocks/low         - Get low stock items

GET    /api/v1/orders             - List pharmacy orders
POST   /api/v1/orders             - Create order
PUT    /api/v1/orders/:id/confirm - Confirm order
```

### Payment Service Endpoints

```
GET    /api/v1/invoices           - List invoices
POST   /api/v1/invoices           - Create invoice
GET    /api/v1/invoices/:id       - Get invoice
PUT    /api/v1/invoices/:id       - Update invoice

GET    /api/v1/payments           - List payments
POST   /api/v1/payments           - Record payment
POST   /api/v1/payments/:id/confirm - Confirm payment

GET    /api/v1/insurance-claims   - List claims
POST   /api/v1/insurance-claims   - Submit claim
```

### Analytics Service Endpoints

```
GET    /api/v1/metrics            - Get service metrics
GET    /api/v1/health-indicators  - System health
GET    /api/v1/alerts             - System alerts
POST   /api/v1/alerts             - Create alert

GET    /api/v1/dashboard/summary  - Dashboard overview
GET    /api/v1/reports/daily      - Daily reports
GET    /api/v1/reports/weekly     - Weekly reports
GET    /api/v1/reports/monthly    - Monthly reports
```

## Environment Configuration

Each service has an `.env` file that can be customized:

```env
# Service Configuration
SERVICE_NAME=user-service
SERVICE_PORT=3001

# Database Configuration
DB_HOST=localhost
DB_PORT=3307
DB_USER=root
DB_PASSWORD=
DB_NAME=meditrack_users

# JWT (User Service only)
JWT_SECRET=your_secret_key_here_change_in_production
JWT_EXPIRY=24

# Service URLs
USER_SERVICE_URL=http://localhost:3001
APPOINTMENT_SERVICE_URL=http://localhost:3002
MEDICAL_SERVICE_URL=http://localhost:3003
PHARMACY_SERVICE_URL=http://localhost:3004
PAYMENT_SERVICE_URL=http://localhost:3005
ANALYTICS_SERVICE_URL=http://localhost:3006

# Environment
ENVIRONMENT=development
LOG_LEVEL=info
```

## Database Schema Overview

### User Service Tables
- `users` - User accounts and profiles
- `roles` - Role definitions
- `permissions` - Permission definitions
- `role_permissions` - Role-permission mappings
- `audit_logs` - Security audit trail

### Appointment Service Tables
- `appointments` - Appointment records
- `appointment_slots` - Doctor availability slots
- `appointment_notifications` - Appointment notifications

### Medical Service Tables
- `medical_records` - Patient medical records
- `prescriptions` - Drug prescriptions
- `lab_results` - Laboratory test results
- `clinical_notes` - Clinical notes

### Pharmacy Service Tables
- `drugs` - Drug inventory
- `drug_stocks` - Stock levels by location
- `pharmacy_orders` - Patient medication orders
- `order_items` - Items in each order
- `drug_inventory_log` - Inventory transaction log

### Payment Service Tables
- `invoices` - Service invoices
- `payments` - Payment transactions
- `insurance_claims` - Insurance claim tracking
- `payment_proofs` - Payment documentation
- `refunds` - Refund records

### Analytics Service Tables
- `service_metrics` - Service performance metrics
- `user_analytics` - User activity analytics
- `appointment_analytics` - Appointment statistics
- `revenue_analytics` - Revenue tracking
- `health_indicators` - System health metrics
- `system_alerts` - System alerts and notifications

## Troubleshooting

### Database Connection Issues

**Error: "Access denied for user 'root'@'localhost'"**
- Verify MySQL is running on port 3307
- Ensure credentials in `.env` files are correct
- Check MySQL user permissions

**Error: "Can't connect to MySQL server"**
- Verify MySQL service is running
- Check port 3307 is accessible
- Use `mysql -h localhost -P 3307 -u root` to test connection

### Service Connection Issues

**Error: "Connection refused on port 300X"**
- Ensure all services are running
- Check firewall isn't blocking ports
- Verify correct port in `.env` files
- Run `netstat -an | grep 300` to see listening ports

### Migration Errors

**Error: "Table already exists"**
- This is normal if running migrations multiple times
- The `CREATE TABLE IF NOT EXISTS` prevents errors

**Error: "Unknown column 'x' in 'field list'"**
- Ensure you ran all migration files for that service
- Check migration file was executed correctly

## Performance Optimization

### Database Connection Pooling
Each service automatically configures connection pooling:
- Max Open Connections: 25
- Max Idle Connections: 5

### Recommended Improvements
1. Add caching layer (Redis) for frequently accessed data
2. Implement database query optimization and indexing
3. Add API rate limiting
4. Implement JWT token refresh mechanism
5. Add comprehensive logging and monitoring

## Security Recommendations

1. **Change JWT Secret**: Update `JWT_SECRET` in user-service `.env`
2. **Database Backup**: Implement regular MySQL backups
3. **Environment Variables**: Never commit actual `.env` files to version control
4. **HTTPS**: Implement SSL/TLS in production
5. **API Authentication**: All protected endpoints require valid JWT token
6. **Input Validation**: All endpoints validate incoming data

## Building for Production

### Build Executables

```bash
# User Service
cd user-service
go build -o user-service ./cmd/main.go

# Appointment Service
cd ../appointment-service
go build -o appointment-service ./cmd/main.go

# (Repeat for other services)
```

### Running Built Executables

```bash
./user-service/user-service
./appointment-service/appointment-service
# etc.
```

## Integration with Laravel Frontend

Update your Laravel frontend configuration to point to the API Gateway:

```php
// config/app.php or .env
API_URL=http://localhost:3000

// In your API client
protected $baseUrl = env('API_URL', 'http://localhost:3000');
```

Example API call from Laravel:

```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $token,
    'Content-Type' => 'application/json',
])->get('http://localhost:3000/api/v1/users');
```

## Monitoring & Logging

Check logs for debugging:

```bash
# Check application output in each terminal
# Look for ERROR, WARNING, or INFO messages

# For database logs
tail -f /var/log/mysql/error.log  # Linux/macOS
Get-Content C:\ProgramData\MySQL\MySQL Server 8.0\Data\*.err  # Windows
```

## Next Steps

1. ✅ Ensure all databases are created and migrations executed
2. ✅ Verify all 7 services are running and responding to /health
3. ✅ Test authentication flow (register → login)
4. ✅ Test CRUD operations on each service
5. ✅ Integrate with Laravel frontend
6. ✅ Configure and test inter-service communication
7. ✅ Set up monitoring and alerting

## Support

For issues or questions:
1. Check the service logs in each terminal
2. Verify database connectivity
3. Ensure all `.env` files have correct configuration
4. Review the REST API endpoints documentation above

---

**Last Updated**: March 25, 2026  
**Version**: 1.0 - Microservices Architecture
