<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Base API prefix: /api

// ==================== AUTHENTICATION ====================
Route::post('/register', [AuthController::class, 'apiRegister'])->name('auth.register');
Route::post('/login', [AuthController::class, 'apiLogin'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'apiLogout'])->middleware('auth:sanctum')->name('auth.logout');

// ==================== USER MANAGEMENT ====================
Route::prefix('users')->controller(UserController::class)->group(function () {
    Route::get('/', 'index')->name('users.index');
    Route::post('/', 'store')->name('users.store');
    Route::get('/{user}', 'show')->name('users.show');
    Route::put('/{user}', 'update')->name('users.update');
    Route::delete('/{user}', 'destroy')->name('users.destroy');
    
    Route::get('/doctors/all', 'getDoctors')->name('users.doctors');
    Route::get('/patients/all', 'getPatients')->name('users.patients');
    Route::get('/pharmacists/all', 'getPharmacists')->name('users.pharmacists');
});

// ==================== APPOINTMENT MANAGEMENT ====================
Route::prefix('appointments')->controller(AppointmentController::class)->group(function () {
    Route::get('/', 'index')->name('appointments.index');
    Route::post('/', 'store')->name('appointments.store');
    Route::get('/{appointment}', 'show')->name('appointments.show');
    Route::put('/{appointment}', 'update')->name('appointments.update');
    Route::delete('/{appointment}', 'destroy')->name('appointments.destroy');
    
    Route::put('/{appointment}/cancel', 'cancel')->name('appointments.cancel');
    Route::put('/{appointment}/reschedule', 'reschedule')->name('appointments.reschedule');
    
    Route::get('/doctor/{doctorId}', 'getByDoctor')->name('appointments.by-doctor');
    Route::get('/patient/{patientId}', 'getByPatient')->name('appointments.by-patient');
});

// ==================== MEDICAL RECORDS ====================
Route::prefix('medical-records')->controller(MedicalRecordController::class)->group(function () {
    Route::get('/', 'index')->name('medical-records.index');
    Route::post('/', 'store')->name('medical-records.store');
    Route::get('/{medicalRecord}', 'show')->name('medical-records.show');
    Route::put('/{medicalRecord}', 'update')->name('medical-records.update');
    Route::delete('/{medicalRecord}', 'destroy')->name('medical-records.destroy');
    
    Route::get('/patient/{patientId}/history', 'getPatientHistory')->name('medical-records.patient-history');
    Route::get('/patient/{patientId}/export', 'export')->name('medical-records.export');
});

// ==================== PRESCRIPTIONS ====================
Route::prefix('prescriptions')->controller(PrescriptionController::class)->group(function () {
    Route::get('/', 'index')->name('prescriptions.index');
    Route::post('/', 'store')->name('prescriptions.store');
    Route::get('/{prescription}', 'show')->name('prescriptions.show');
    Route::put('/{prescription}', 'update')->name('prescriptions.update');
    Route::delete('/{prescription}', 'destroy')->name('prescriptions.destroy');
    
    Route::get('/patient/{patientId}', 'getByPatient')->name('prescriptions.by-patient');
    Route::get('/doctor/{doctorId}', 'getByDoctor')->name('prescriptions.by-doctor');
    
    Route::put('/{prescription}/complete', 'complete')->name('prescriptions.complete');
    Route::put('/{prescription}/cancel', 'cancel')->name('prescriptions.cancel');
});

// ==================== PHARMACY MANAGEMENT ====================
Route::prefix('pharmacies')->controller(PharmacyController::class)->group(function () {
    Route::get('/', 'index')->name('pharmacies.index');
    Route::post('/', 'store')->name('pharmacies.store');
    Route::get('/{pharmacy}', 'show')->name('pharmacies.show');
    Route::put('/{pharmacy}', 'update')->name('pharmacies.update');
    Route::delete('/{pharmacy}', 'destroy')->name('pharmacies.destroy');
    
    // Drug stock management
    Route::post('/{pharmacy}/drug-stock', 'addDrugStock')->name('pharmacies.add-drug-stock');
    Route::get('/{pharmacy}/drug-stock', 'getDrugStock')->name('pharmacies.get-drug-stock');
    Route::put('/{pharmacy}/drug-stock/{drugStock}', 'updateDrugStock')->name('pharmacies.update-drug-stock');
    Route::delete('/{pharmacy}/drug-stock/{drugStock}', 'deleteDrugStock')->name('pharmacies.delete-drug-stock');
    
    // Nearby pharmacies
    Route::get('/nearby/search', 'getNearby')->name('pharmacies.nearby');
});

// ==================== PAYMENTS & INSURANCE ====================
Route::prefix('payments')->controller(PaymentController::class)->group(function () {
    Route::get('/', 'index')->name('payments.index');
    Route::post('/', 'store')->name('payments.store');
    Route::get('/{payment}', 'show')->name('payments.show');
    Route::put('/{payment}/status', 'updateStatus')->name('payments.update-status');
    
    Route::get('/patient/{patientId}', 'getByPatient')->name('payments.by-patient');
    Route::get('/appointment/{appointmentId}', 'getByAppointment')->name('payments.by-appointment');
    Route::put('/{payment}/refund', 'refund')->name('payments.refund');
    
    // Insurance claims
    Route::post('/insurance/create-claim', 'createInsuranceClaim')->name('payments.create-insurance-claim');
    Route::get('/insurance/claims', 'getInsuranceClaims')->name('payments.get-insurance-claims');
    Route::put('/insurance/claims/{claim}', 'updateInsuranceClaim')->name('payments.update-insurance-claim');
    
    // Statistics
    Route::get('/statistics/overview', 'getStatistics')->name('payments.statistics');
});

// ==================== ANALYTICS & REPORTING ====================
Route::prefix('analytics')->controller(AnalyticsController::class)->group(function () {
    Route::get('/dashboard/overview', 'getDashboardOverview')->name('analytics.dashboard');
    
    Route::get('/patients/outcomes', 'getPatientOutcomes')->name('analytics.patient-outcomes');
    Route::get('/doctors/performance', 'getDoctorPerformance')->name('analytics.doctor-performance');
    Route::get('/drugs/usage-trends', 'getDrugUsageTrends')->name('analytics.drug-trends');
    Route::get('/revenue/analytics', 'getRevenueAnalytics')->name('analytics.revenue');
    
    Route::get('/users/statistics', 'getUserStatistics')->name('analytics.users');
    Route::get('/appointments/statistics', 'getAppointmentStatistics')->name('analytics.appointments');
    Route::get('/prescriptions/statistics', 'getPrescriptionStatistics')->name('analytics.prescriptions');
    Route::get('/insurance/statistics', 'getInsuranceClaimStatistics')->name('analytics.insurance');
    
    Route::get('/logs/activity', 'getActivityLogs')->name('analytics.activity-logs');
});
