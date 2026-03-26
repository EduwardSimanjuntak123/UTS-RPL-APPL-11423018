# 🏥 MediTrack - A Digital Healthcare Platform

> A comprehensive digital healthcare platform connecting patients, doctors, pharmacies, and administrators in a unified system.

## 📋 Table of Contents
- [Overview](#overview)
- [Architecture](#architecture)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [API Documentation](#api-documentation)

## Overview

MediTrack is a monolithic architecture-based healthcare platform built with Laravel 11. It provides comprehensive management for:
- 👥 User management (patients, doctors, pharmacists, admins)
- 📅 Appointment scheduling and management
- 🏥 Electronic Health Records (EHR)
- 💊 Prescription management and pharmacy integration
- 💳 Payment processing and insurance claims
- 📊 Advanced analytics and reporting

---

## Architecture

### Monolithic Architecture Design
The system uses a **monolithic architecture** with:
- Single codebase for all features
- Shared database for all modules
- Modular organization through services and controllers
- Clear separation of concerns using service classes

### Architectural Layers:
```
┌─────────────────────────────────────┐
│         Routes (API Endpoints)       │
├─────────────────────────────────────┤
│    Controllers (Request Handlers)    │
├─────────────────────────────────────┤
│    Services (Business Logic)         │
├─────────────────────────────────────┤
│     Models (Data Representation)     │
├─────────────────────────────────────┤
│    Database (MySQL/MariaDB)          │
└─────────────────────────────────────┘
```

---

## Features

### 1. User Management
- Create, read, update, delete users
- Role-based access: Patient, Doctor, Pharmacist, Admin
- Doctor specialization and license tracking
- Status management (active, inactive, suspended)

### 2. Appointment System
- Schedule appointments with availability checking
- Reschedule, cancel, view appointment history
- Filter by date, status, doctor, patient

### 3. Electronic Health Records (EHR)
- Complete patient medical history
- Diagnosis and treatment documentation
- Lab results, medication tracking
- Follow-up scheduling, export functionality

### 4. Prescription Management
- Create prescriptions from medical records
- Pharmacy integration for prescription fulfillment
- Track prescription status and medication history
- Expiry date management

### 5. Pharmacy Integration
- Pharmacy registration and management
- Drug stock management
- Stock level monitoring, expiry tracking
- Location-based pharmacy search

### 6. Payment System
- Multiple payment methods support
- Refund management
- Insurance claim creation and approval
- Payment statistics and reporting

### 7. Analytics & Reporting
- Dashboard with key metrics
- Patient outcomes analytics
- Doctor performance metrics
- Drug usage trends and revenue analytics

---

## Tech Stack

- **Framework**: Laravel 11
- **Language**: PHP 8.1+
- **Database**: MySQL/MariaDB
- **API**: RESTful with JSON responses
- **Package Manager**: Composer, npm

---

## Installation

### Prerequisites
```bash
- PHP 8.1+
- Composer
- MySQL/MariaDB 5.7+
- Node.js 14+ & npm
```

### Setup Steps

```bash
# 1. Clone repository
git clone <repository-url>
cd meditrack

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meditrack
DB_USERNAME=root
DB_PASSWORD=yourpassword

# 5. Run migrations
php artisan migrate

# 6. Seed database (optional)
php artisan db:seed

# 7. Build assets
npm run build

# 8. Start server
php artisan serve
```

API will be available at `http://localhost:8000/api`

---

## Usage

### Running Tests
```bash
php artisan test
```

### Database Seeding
```bash
php artisan db:seed
```

This creates:
- 1 Admin user
- 3 Sample doctors with different specializations
- 5 Sample patients
- 1 Pharmacist
- 1 Pharmacy with drug stock
- Sample appointments and medical records

**Test Credentials**:
```
Email: admin@meditrack.com
Password: password123
```

---

## Database Schema

### Core Tables
- **users**: System users (patients, doctors, pharmacists, admins)
- **appointments**: Appointment records
- **medical_records**: Patient medical history
- **prescriptions**: Medication prescriptions
- **pharmacies**: Pharmacy information
- **drug_stock**: Pharmacy drug inventory
- **prescription_orders**: Prescription fulfillment
- **payments**: Payment transactions
- **insurance_claims**: Insurance claims
- **analytics_logs**: System activity logs

### Key Relationships
```
Patient (User) 
  ├── Has Many → Appointments
  ├── Has Many → Medical Records
  ├── Has Many → Prescriptions
  ├── Has Many → Payments
  └── Has Many → Insurance Claims

Doctor (User)
  ├── Has Many → Appointments (as doctor)
  ├── Has Many → Medical Records
  └── Has Many → Prescriptions (created)

Appointment
  ├── Belongs To → Patient
  ├── Belongs To → Doctor
  ├── Has Many → Medical Records
  ├── Has Many → Prescriptions
  └── Has One → Payment
```

---

## API Endpoints Summary

### User Management
- `GET /users` - Get all users
- `POST /users` - Create user
- `GET /users/{id}` - Get user details
- `PUT /users/{id}` - Update user
- `GET /users/doctors/all` - Get all doctors

### Appointments
- `GET /appointments` - List appointments
- `POST /appointments` - Create appointment
- `PUT /appointments/{id}` - Update appointment
- `PUT /appointments/{id}/cancel` - Cancel appointment
- `PUT /appointments/{id}/reschedule` - Reschedule appointment

### Medical Records
- `GET /medical-records` - List records
- `POST /medical-records` - Create record
- `GET /medical-records/patient/{id}/history` - Get patient history
- `GET /medical-records/patient/{id}/export` - Export records

### Prescriptions
- `GET /prescriptions` - List prescriptions
- `POST /prescriptions` - Create prescription
- `GET /prescriptions/patient/{id}` - Get patient prescriptions
- `PUT /prescriptions/{id}/complete` - Mark as completed

### Pharmacies
- `GET /pharmacies` - List pharmacies
- `POST /pharmacies` - Create pharmacy
- `POST /pharmacies/{id}/drug-stock` - Add drug stock
- `GET /pharmacies/nearby/search` - Get nearby pharmacies

### Payments
- `GET /payments` - List payments
- `POST /payments` - Create payment
- `POST /payments/insurance/create-claim` - Create insurance claim
- `GET /analytics/revenue/analytics` - Revenue statistics

### Analytics
- `GET /analytics/dashboard/overview` - Dashboard
- `GET /analytics/doctors/performance` - Doctor performance
- `GET /analytics/drugs/usage-trends` - Drug trends

**Full API documentation**: See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

---

## Project Structure

```
meditrack/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── UserController.php
│   │   │   ├── AppointmentController.php
│   │   │   ├── MedicalRecordController.php
│   │   │   ├── PrescriptionController.php
│   │   │   ├── PharmacyController.php
│   │   │   ├── PaymentController.php
│   │   │   └── AnalyticsController.php
│   │   └── Requests/ (Form request validation)
│   ├── Models/ (Eloquent models)
│   ├── Services/ (Business logic)
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php (All API routes)
│   └── web.php
├── resources/
├── storage/
├── tests/
└── API_DOCUMENTATION.md

```

---

## Business Logic Flows

### Appointment → Medical Record → Prescription Flow
```
1. Patient books appointment
2. Doctor completes appointment → creates medical record
3. Doctor creates prescription from medical record
4. Patient selects pharmacy
5. Prescription order created
6. Pharmacy staff prepares medication
7. Patient picks up prescription
```

### Payment & Insurance Flow
```
1. After appointment, payment record created
2. Patient selects payment method
3. Insurance claim created (if applicable)
4. Insurance company reviews and approves/rejects
5. Payment status updated
```

---

## Service Layer

### AppointmentService
- Schedule with availability checking
- Reschedule and cancel
- Get available time slots
- Generate statistics

### MedicalRecordService
- Create from appointments
- Retrieve patient history
- Generate medical summary
- Export records

### PrescriptionService
- Create prescriptions
- Order from pharmacies
- Track status (active, completed, cancelled)
- Generate statistics

### PharmacyService
- Manage drug stock
- Check availability
- Monitor expiry dates
- Find nearby pharmacies (GPS)

### PaymentService
- Process payments
- Handle refunds
- Manage insurance claims
- Generate statistics

---

## Key Features in Action

### 1. Appointment Scheduling
```bash
POST /appointments
{
  "patient_id": 1,
  "doctor_id": 2,
  "appointment_date": "2024-12-20 14:00:00",
  "type": "consultation"
}
```

### 2. Create Medical Record
```bash
POST /medical-records
{
  "patient_id": 1,
  "doctor_id": 2,
  "appointment_id": 1,
  "diagnosis": "Demam Typhoid",
  "treatment": "Istirahat dan minum obat",
  "medications": "Paracetamol 500mg"
}
```

### 3. Create Prescription
```bash
POST /prescriptions
{
  "patient_id": 1,
  "doctor_id": 2,
  "medication": "Paracetamol",
  "dosage": "500mg",
  "frequency": "2x sehari",
  "duration": 7
}
```

### 4. View Dashboard
```bash
GET /analytics/dashboard/overview
```
Returns: user counts, appointment stats, revenue, prescriptions

---

## Performance & Security

### Performance Optimizations
- Database indexing on frequently queried columns
- Eager loading to prevent N+1 queries
- Pagination for large datasets
- Query optimization for complex operations

### Security Features
- Input validation through FormRequests
- Role-based access control
- SQL injection protection (parameterized queries)
- Password hashing with bcrypt
- CORS protection

---

## Troubleshooting

### Database Connection Error
```bash
# Verify .env DB credentials
# Check MySQL service is running
mysql -u root -p
```

### Class Not Found
```bash
composer dump-autoload -o
```

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
```

---

## Future Enhancements

- [ ] Real-time notifications (WebSockets)
- [ ] Video consultation feature
- [ ] Mobile app integration
- [ ] AI-based appointment scheduling
- [ ] Telemedicine capabilities
- [ ] Advanced reporting dashboards

---

## Support & License

For support, create an issue in the repository or contact the development team.

This project is licensed under the MIT License.

---

**Version**: 1.0.0  
**Last Updated**: March 2024  
**Status**: Production Ready ✅
