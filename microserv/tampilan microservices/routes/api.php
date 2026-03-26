<?php
use App\Http\Controllers\UserControllerMicroservices;
use App\Http\Controllers\AppointmentControllerMicroservices;
use App\Http\Controllers\MedicalRecordControllerMicroservices;
use App\Http\Controllers\PrescriptionControllerMicroservices;
use App\Http\Controllers\PharmacyControllerMicroservices;
use App\Http\Controllers\PaymentControllerMicroservices;
use App\Http\Controllers\AnalyticsControllerMicroservices;
use Illuminate\Support\Facades\Route;

// Base API prefix: /api/v1

// ==================== USER MANAGEMENT ====================
Route::post('/auth/login', [UserControllerMicroservices::class, 'login']);
Route::post('/auth/register', [UserControllerMicroservices::class, 'register']);

Route::middleware(['auth:sanctum', 'microservice.token'])->group(function () {
    Route::post('/auth/logout', [UserControllerMicroservices::class, 'logout']);
    
    Route::prefix('users')->controller(UserControllerMicroservices::class)->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::post('/', 'store')->name('users.store');
        Route::get('/{id}', 'show')->name('users.show');
        Route::put('/{id}', 'update')->name('users.update');
        Route::delete('/{id}', 'destroy')->name('users.destroy');
    });
    
    Route::get('/roles', [UserControllerMicroservices::class, 'getRoles']);
    Route::get('/audit-logs', [UserControllerMicroservices::class, 'auditLogs']);

    // ==================== APPOINTMENT MANAGEMENT ====================
    Route::prefix('appointments')->controller(AppointmentControllerMicroservices::class)->group(function () {
        Route::get('/', 'index')->name('appointments.index');
        Route::post('/', 'store')->name('appointments.store');
        Route::get('/today', 'getTodayAppointments')->name('appointments.today');
        Route::get('/{id}', 'show')->name('appointments.show');
        Route::put('/{id}', 'update')->name('appointments.update');
        Route::delete('/{id}', 'destroy')->name('appointments.destroy');
        
        Route::post('/{id}/confirm', 'confirm')->name('appointments.confirm');
        Route::post('/{id}/cancel', 'cancel')->name('appointments.cancel');
        Route::post('/{id}/complete', 'complete')->name('appointments.complete');
        Route::put('/{id}/reschedule', 'reschedule')->name('appointments.reschedule');
        
        Route::get('/patients/{patientId}', 'getPatientAppointments')->name('appointments.by-patient');
        Route::get('/doctors/{doctorId}/schedule', 'getDoctorSchedule')->name('appointments.doctor-schedule');
    });
    
    Route::get('/doctors/{doctorId}/available-slots', [AppointmentControllerMicroservices::class, 'getAvailableSlots']);

    // ==================== MEDICAL RECORDS ====================
    Route::prefix('medical-records')->controller(MedicalRecordControllerMicroservices::class)->group(function () {
        Route::get('/', 'indexMedicalRecords')->name('medical-records.index');
        Route::post('/', 'storeMedicalRecord')->name('medical-records.store');
        Route::get('/{id}', 'showMedicalRecord')->name('medical-records.show');
        Route::put('/{id}', 'updateMedicalRecord')->name('medical-records.update');
        Route::delete('/{id}', 'destroyMedicalRecord')->name('medical-records.destroy');
        
        Route::get('/patients/{patientId}', 'getPatientMedicalRecords')->name('medical-records.by-patient');
    });

    // ==================== PRESCRIPTIONS ====================
    Route::prefix('prescriptions')->controller(PrescriptionControllerMicroservices::class)->group(function () {
        Route::get('/', 'index')->name('prescriptions.index');
        Route::post('/', 'store')->name('prescriptions.store');
        Route::get('/{id}', 'show')->name('prescriptions.show');
        Route::put('/{id}', 'update')->name('prescriptions.update');
        Route::delete('/{id}', 'destroy')->name('prescriptions.destroy');
        
        Route::get('/patients/{patientId}', 'getPatientPrescriptions')->name('prescriptions.by-patient');
        Route::get('/doctors/{doctorId}', 'getDoctorPrescriptions')->name('prescriptions.by-doctor');
        
        Route::put('/{id}/refill', 'refillPrescription')->name('prescriptions.refill');
        Route::delete('/{id}/cancel', 'cancelPrescription')->name('prescriptions.cancel');
    });

    // ==================== PHARMACY MANAGEMENT ====================
    Route::prefix('pharmacies')->controller(PharmacyControllerMicroservices::class)->group(function () {
        Route::get('/', 'index')->name('pharmacies.index');
        Route::post('/', 'store')->name('pharmacies.store');
        Route::get('/{id}', 'show')->name('pharmacies.show');
        Route::put('/{id}', 'update')->name('pharmacies.update');
        Route::delete('/{id}', 'destroy')->name('pharmacies.destroy');
        
        Route::get('/{pharmacyId}/low-stock', 'getLowStockDrugs')->name('pharmacies.low-stock');
    });
    
    // Drug Stock
    Route::prefix('drug-stock')->controller(PharmacyControllerMicroservices::class)->group(function () {
        Route::get('/', 'getDrugStock')->name('drug-stock.index');
        Route::post('/', 'addDrugStock')->name('drug-stock.store');
        Route::put('/{id}', 'updateDrugStock')->name('drug-stock.update');
    });
    
    // Drug Orders
    Route::prefix('drug-orders')->controller(PharmacyControllerMicroservices::class)->group(function () {
        Route::get('/', 'getDrugOrders')->name('drug-orders.index');
        Route::post('/', 'createDrugOrder')->name('drug-orders.store');
        Route::put('/{id}', 'updateDrugOrderStatus')->name('drug-orders.update');
    });

    // ==================== PAYMENTS & INSURANCE ====================
    Route::prefix('payments')->controller(PaymentControllerMicroservices::class)->group(function () {
        Route::get('/', 'index')->name('payments.index');
        Route::post('/', 'store')->name('payments.store');
        Route::get('/{id}', 'show')->name('payments.show');
        Route::put('/{id}', 'update')->name('payments.update');
        
        Route::put('/{id}/complete', 'completePayment')->name('payments.complete');
        Route::post('/{id}/refund', 'refundPayment')->name('payments.refund');
        
        Route::get('/patients/{patientId}', 'getPatientPayments')->name('payments.by-patient');
    });
    
    // Invoices
    Route::prefix('invoices')->controller(PaymentControllerMicroservices::class)->group(function () {
        Route::get('/', 'getInvoices')->name('invoices.index');
        Route::post('/', 'createInvoice')->name('invoices.store');
    });
    
    // Insurance Claims
    Route::prefix('insurance-claims')->controller(PaymentControllerMicroservices::class)->group(function () {
        Route::get('/', 'getInsuranceClaims')->name('insurance-claims.index');
        Route::post('/', 'createInsuranceClaim')->name('insurance-claims.store');
        Route::put('/{id}', 'updateInsuranceClaim')->name('insurance-claims.update');
    });

    // ==================== ANALYTICS & REPORTING ====================
    Route::prefix('analytics')->controller(AnalyticsControllerMicroservices::class)->group(function () {
        Route::get('/dashboard/summary', 'getDashboardSummary')->name('analytics.dashboard');
        
        Route::get('/metrics', 'getServiceMetrics')->name('analytics.metrics');
        Route::post('/metrics', 'recordServiceMetric')->name('analytics.record-metric');
        
        Route::get('/health-indicators', 'getHealthIndicators')->name('analytics.health');
        Route::get('/services/{service}/health', 'getServiceHealth')->name('analytics.service-health');
        
        Route::get('/alerts', 'getAlerts')->name('analytics.alerts');
        Route::post('/alerts', 'createAlert')->name('analytics.create-alert');
        Route::put('/alerts/{id}/resolve', 'resolveAlert')->name('analytics.resolve-alert');
    });
    
    // Reports
    Route::prefix('reports')->controller(AnalyticsControllerMicroservices::class)->group(function () {
        Route::get('/daily', 'getDailyReport')->name('reports.daily');
        Route::get('/weekly', 'getWeeklyReport')->name('reports.weekly');
        Route::get('/monthly', 'getMonthlyReport')->name('reports.monthly');
        Route::post('/custom', 'getCustomReport')->name('reports.custom');
    });
    
    // Analytics Queries
    Route::prefix('analytics')->controller(AnalyticsControllerMicroservices::class)->group(function () {
        Route::get('/users', 'getUserAnalytics')->name('analytics.users');
        Route::get('/appointments', 'getAppointmentAnalytics')->name('analytics.appointments');
        Route::get('/revenue', 'getRevenueAnalytics')->name('analytics.revenue');
    });
});

// Lab Results & Clinical Notes (Medical Service)
Route::middleware(['auth:sanctum', 'microservice.token'])->prefix('medical')->group(function () {
    Route::prefix('lab-results')->controller(MedicalRecordControllerMicroservices::class)->group(function () {
        Route::get('/', 'indexLabResults')->name('lab-results.index');
        Route::post('/', 'storeLabResult')->name('lab-results.store');
    });
    
    Route::prefix('clinical-notes')->controller(MedicalRecordControllerMicroservices::class)->group(function () {
        Route::get('/', 'indexClinicalNotes')->name('clinical-notes.index');
        Route::post('/', 'storeClinicalNote')->name('clinical-notes.store');
        Route::put('/{id}', 'updateClinicalNote')->name('clinical-notes.update');
    });
    
    Route::get('/patients/{patientId}/clinical-notes', [MedicalRecordControllerMicroservices::class, 'getPatientClinicalNotes']);
});
