<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\PatientController;
use App\Http\Controllers\Web\DoctorController;
use App\Http\Controllers\Web\PharmacistController;
use App\Http\Controllers\Web\AdminController;

// Guest routes
Route::get('/', [GuestController::class, 'dashboard'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Patient routes
    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [PatientController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [PatientController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [PatientController::class, 'updateProfile'])->name('profile.update');
        
        // Appointments
        Route::get('/appointments', [PatientController::class, 'appointmentsIndex'])->name('appointments');
        Route::get('/appointments/create', [PatientController::class, 'createAppointment'])->name('appointments.create');
        Route::post('/appointments', [PatientController::class, 'storeAppointment'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [PatientController::class, 'showAppointment'])->name('appointments.show');
        Route::get('/appointments/{appointment}/edit', [PatientController::class, 'editAppointment'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [PatientController::class, 'updateAppointment'])->name('appointments.update');
        Route::delete('/appointments/{appointment}', [PatientController::class, 'destroyAppointment'])->name('appointments.destroy');
        
        // Medical Records
        Route::get('/medical-records', [PatientController::class, 'medicalRecordsIndex'])->name('medical-records');
        Route::get('/medical-records/{record}', [PatientController::class, 'showMedicalRecord'])->name('medical-records.show');
        
        // Prescriptions
        Route::get('/prescriptions', [PatientController::class, 'prescriptionsIndex'])->name('prescriptions');
        Route::get('/prescriptions/{prescription}', [PatientController::class, 'showPrescription'])->name('prescriptions.show');
        
        // Payments
        Route::get('/payments', [PatientController::class, 'paymentsIndex'])->name('payments');
        Route::get('/payments/{payment}', [PatientController::class, 'showPayment'])->name('payments.show');
    });
    
    // Doctor routes
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [DoctorController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [DoctorController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [DoctorController::class, 'updateProfile'])->name('profile.update');
        
        // Appointments
        Route::get('/appointments', [DoctorController::class, 'appointmentsIndex'])->name('appointments');
        Route::get('/appointments/create', function() { return abort(403, 'Doctors cannot create appointments'); })->name('appointments.create');
        Route::get('/appointments/{appointment}', [DoctorController::class, 'showAppointment'])->name('appointments.show');
        Route::put('/appointments/{appointment}', [DoctorController::class, 'updateAppointment'])->name('appointments.update');
        
        // Patients
        Route::get('/patients', [DoctorController::class, 'patientsIndex'])->name('patients');
        Route::get('/patients/{patient}', [DoctorController::class, 'showPatient'])->name('patients.show');
        
        // Medical Records
        Route::get('/medical-records', [DoctorController::class, 'medicalRecordsIndex'])->name('medical-records');
        Route::get('/medical-records/create', [DoctorController::class, 'createMedicalRecord'])->name('medical-records.create');
        Route::post('/medical-records', [DoctorController::class, 'storeMedicalRecord'])->name('medical-records.store');
        Route::get('/medical-records/{record}', [DoctorController::class, 'showMedicalRecord'])->name('medical-records.show');
        Route::get('/medical-records/{record}/edit', [DoctorController::class, 'editMedicalRecord'])->name('medical-records.edit');
        Route::put('/medical-records/{record}', [DoctorController::class, 'updateMedicalRecord'])->name('medical-records.update');
        Route::delete('/medical-records/{record}', [DoctorController::class, 'destroyMedicalRecord'])->name('medical-records.destroy');
        
        // Prescriptions
        Route::get('/prescriptions', [DoctorController::class, 'prescriptionsIndex'])->name('prescriptions');
        Route::get('/prescriptions/{prescription}', [DoctorController::class, 'showPrescription'])->name('prescriptions.show');
    });
    
    // Pharmacist routes
    Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
        Route::get('/dashboard', [PharmacistController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [PharmacistController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [PharmacistController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [PharmacistController::class, 'updateProfile'])->name('profile.update');
        
        // Inventory
        Route::get('/inventory', [PharmacistController::class, 'inventoryIndex'])->name('inventory');
        Route::put('/inventory/{drug}', [PharmacistController::class, 'updateInventory'])->name('inventory.update');
        Route::delete('/inventory/{drug}', [PharmacistController::class, 'destroyInventory'])->name('inventory.destroy');
        
        // Orders
        Route::get('/orders', [PharmacistController::class, 'ordersIndex'])->name('orders');
        Route::get('/orders/{order}', [PharmacistController::class, 'showOrder'])->name('orders.show');
        Route::put('/orders/{order}', [PharmacistController::class, 'updateOrder'])->name('orders.update');
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Users management
        Route::get('/users', [AdminController::class, 'usersIndex'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        
        // Analytics
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        
        // Pharmacies management
        Route::get('/pharmacies', [AdminController::class, 'pharmaciesIndex'])->name('pharmacies');
        Route::get('/pharmacies/create', [AdminController::class, 'createPharmacy'])->name('pharmacies.create');
        Route::post('/pharmacies', [AdminController::class, 'storePharmacy'])->name('pharmacies.store');
        Route::get('/pharmacies/{pharmacy}', [AdminController::class, 'showPharmacy'])->name('pharmacies.show');
        Route::get('/pharmacies/{pharmacy}/edit', [AdminController::class, 'editPharmacy'])->name('pharmacies.edit');
        Route::put('/pharmacies/{pharmacy}', [AdminController::class, 'updatePharmacy'])->name('pharmacies.update');
        Route::delete('/pharmacies/{pharmacy}', [AdminController::class, 'destroyPharmacy'])->name('pharmacies.destroy');
        
        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/update-email', [AdminController::class, 'updateSettings'])->name('settings.update-email');
    });
});
