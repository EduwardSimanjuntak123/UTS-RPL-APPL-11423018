<?php

namespace Database\Seeders;

use App\Services\UserService;
use App\Services\AppointmentService;
use App\Services\MedicalRecordService;
use App\Services\PharmacyService;
use App\Services\PaymentService;
use App\Services\PrescriptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * MicroservicesSeeder
 * 
 * Seeds application data through Go microservices APIs.
 * This seeder does NOT access the database directly.
 * All operations go through the microservices layer.
 * 
 * @package Database\Seeders
 */
class MicroservicesSeeder extends Seeder
{
    protected UserService $userService;
    protected AppointmentService $appointmentService;
    protected MedicalRecordService $medicalRecordService;
    protected PharmacyService $pharmacyService;
    protected PaymentService $paymentService;
    protected PrescriptionService $prescriptionService;

    public function __construct(
        UserService $userService,
        AppointmentService $appointmentService,
        MedicalRecordService $medicalRecordService,
        PharmacyService $pharmacyService,
        PaymentService $paymentService,
        PrescriptionService $prescriptionService
    ) {
        $this->userService = $userService;
        $this->appointmentService = $appointmentService;
        $this->medicalRecordService = $medicalRecordService;
        $this->pharmacyService = $pharmacyService;
        $this->paymentService = $paymentService;
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * Run the database seeds via microservices.
     */
    public function run(): void
    {
        Log::info('🌱 Starting microservices-based seeding...');
        
        try {
            $this->seedUsers();
            $this->seedAppointments();
            $this->seedMedicalRecords();
            $this->seedPharmacy();
            $this->seedPayments();
            $this->seedPrescriptions();
            
            Log::info('✅ Microservices seeding completed successfully');
        } catch (\Exception $e) {
            Log::error('❌ Seeding failed', ['error' => $e->getMessage()]);
            $this->command->error("Seeding failed: {$e->getMessage()}");
        }
    }

    /**
     * Seed users via User microservice
     */
    private function seedUsers(): void
    {
        $this->command->info('Seeding users via User microservice...');

        // Admin user
        $adminData = [
            'name' => 'Admin MediTrack',
            'email' => 'admin@meditrack.com',
            'password' => 'password123',
            'phone' => '08123456789',
            'role' => 'admin',
            'status' => 'active',
        ];

        try {
            $adminResponse = $this->userService->createUser($adminData);
            Log::info('Admin user created', ['admin' => $adminResponse]);
            $this->command->info('  ✓ Admin user created');
        } catch (\Exception $e) {
            Log::warning('Admin user creation failed', ['error' => $e->getMessage()]);
            $this->command->warn("  ⚠ Admin user: {$e->getMessage()}");
        }

        // Doctors
        $doctors = [
            ['name' => 'Dr. Ahmad', 'specialty' => 'Umum', 'license' => 'LIC-DOC-0001'],
            ['name' => 'Dr. Budi', 'specialty' => 'Gigi', 'license' => 'LIC-DOC-0002'],
            ['name' => 'Dr. Citra', 'specialty' => 'Jantung', 'license' => 'LIC-DOC-0003'],
        ];

        foreach ($doctors as $i => $doctor) {
            $doctorData = [
                'name' => $doctor['name'],
                'email' => 'doctor' . ($i + 1) . '@meditrack.com',
                'password' => 'password123',
                'phone' => '0812345678' . ($i + 1),
                'role' => 'doctor',
                'specialty' => $doctor['specialty'],
                'license_number' => $doctor['license'],
                'status' => 'active',
            ];

            try {
                $response = $this->userService->createUser($doctorData);
                Log::info('Doctor created', ['doctor' => $doctor['name']]);
                $this->command->info("  ✓ {$doctor['name']} created");
            } catch (\Exception $e) {
                Log::warning('Doctor creation failed', ['doctor' => $doctor['name'], 'error' => $e->getMessage()]);
                $this->command->warn("  ⚠ {$doctor['name']}: {$e->getMessage()}");
            }
        }

        // Patients
        $patients = [
            ['name' => 'Pasien 1', 'age' => 30, 'gender' => 'male'],
            ['name' => 'Pasien 2', 'age' => 25, 'gender' => 'female'],
            ['name' => 'Pasien 3', 'age' => 45, 'gender' => 'male'],
        ];

        foreach ($patients as $i => $patient) {
            $patientData = [
                'name' => $patient['name'],
                'email' => 'patient' . ($i + 1) . '@meditrack.com',
                'password' => 'password123',
                'phone' => '0898765432' . ($i + 1),
                'role' => 'patient',
                'age' => $patient['age'],
                'gender' => $patient['gender'],
                'status' => 'active',
            ];

            try {
                $response = $this->userService->createUser($patientData);
                Log::info('Patient created', ['patient' => $patient['name']]);
                $this->command->info("  ✓ {$patient['name']} created");
            } catch (\Exception $e) {
                Log::warning('Patient creation failed', ['patient' => $patient['name'], 'error' => $e->getMessage()]);
                $this->command->warn("  ⚠ {$patient['name']}: {$e->getMessage()}");
            }
        }

        // Pharmacists
        for ($i = 1; $i <= 2; $i++) {
            $pharmacistData = [
                'name' => 'Pharmacist ' . $i,
                'email' => 'pharmacist' . $i . '@meditrack.com',
                'password' => 'password123',
                'phone' => '0877777777' . $i,
                'role' => 'pharmacist',
                'status' => 'active',
            ];

            try {
                $response = $this->userService->createUser($pharmacistData);
                Log::info('Pharmacist created', ['pharmacist' => 'Pharmacist ' . $i]);
                $this->command->info("  ✓ Pharmacist {$i} created");
            } catch (\Exception $e) {
                Log::warning('Pharmacist creation failed', ['error' => $e->getMessage()]);
                $this->command->warn("  ⚠ Pharmacist {$i}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Seed appointments
     */
    private function seedAppointments(): void
    {
        $this->command->info('Seeding appointments via Appointment microservice...');
        $this->command->info('  ℹ Appointments will be created during runtime when patients book');
    }

    /**
     * Seed medical records
     */
    private function seedMedicalRecords(): void
    {
        $this->command->info('Seeding medical records via Medical microservice...');
        $this->command->info('  ℹ Medical records will be created when doctors add notes');
    }

    /**
     * Seed pharmacy data (drug stock)
     */
    private function seedPharmacy(): void
    {
        $this->command->info('Seeding pharmacy data via Pharmacy microservice...');

        $drugs = [
            ['name' => 'Paracetamol', 'price' => 5000, 'quantity' => 100],
            ['name' => 'Amoxicillin', 'price' => 8000, 'quantity' => 50],
            ['name' => 'Ibuprofen', 'price' => 6000, 'quantity' => 75],
            ['name' => 'Aspirin', 'price' => 4000, 'quantity' => 150],
            ['name' => 'Omeprazole', 'price' => 12000, 'quantity' => 40],
        ];

        foreach ($drugs as $drug) {
            $drugData = [
                'name' => $drug['name'],
                'price' => $drug['price'],
                'quantity_in_stock' => $drug['quantity'],
                'status' => 'active',
            ];

            try {
                $response = $this->pharmacyService->addDrugStock($drugData);
                Log::info('Drug stock added', ['drug' => $drug['name']]);
                $this->command->info("  ✓ {$drug['name']} added to stock");
            } catch (\Exception $e) {
                Log::warning('Drug stock creation failed', ['drug' => $drug['name'], 'error' => $e->getMessage()]);
                $this->command->warn("  ⚠ {$drug['name']}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Seed payments
     */
    private function seedPayments(): void
    {
        $this->command->info('Seeding payments via Payment microservice...');
        $this->command->info('  ℹ Payments will be recorded during treatment/service usage');
    }

    /**
     * Seed prescriptions
     */
    private function seedPrescriptions(): void
    {
        $this->command->info('Seeding prescriptions via Prescription microservice...');
        $this->command->info('  ℹ Prescriptions will be created when doctors prescribe medications');
    }
}
