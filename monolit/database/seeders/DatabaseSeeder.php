<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Pharmacy;
use App\Models\DrugStock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin MediTrack',
            'email' => 'admin@meditrack.com',
            'password' => Hash::make('password123'),
            'phone' => '08123456789',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create sample doctors
        $doctors = [];
        for ($i = 1; $i <= 3; $i++) {
            $doctor = User::create([
                'name' => "Dr. " . collect(['Ahmad', 'Budi', 'Citra'])->get($i - 1),
                'email' => "doctor{$i}@meditrack.com",
                'password' => Hash::make('password123'),
                'phone' => "0812345678" . $i,
                'role' => 'doctor',
                'specialty' => collect(['Umum', 'Gigi', 'Jantung'])->get($i - 1),
                'license_number' => "LIC-DOC-000{$i}",
                'status' => 'active',
            ]);
            $doctors[] = $doctor;
        }

        // Create sample patients
        $patients = [];
        for ($i = 1; $i <= 5; $i++) {
            $patient = User::create([
                'name' => "Patient " . $i,
                'email' => "patient{$i}@meditrack.com",
                'password' => Hash::make('password123'),
                'phone' => "0898765432{$i}",
                'role' => 'patient',
                'address' => "Jl. Kesehatan No. {$i}, Jakarta",
                'insurance_provider' => 'BPJS',
                'status' => 'active',
            ]);
            $patients[] = $patient;
        }

        // Create sample pharmacist
        $pharmacist = User::create([
            'name' => 'Pharmacist Rina',
            'email' => 'pharmacist@meditrack.com',
            'password' => Hash::make('password123'),
            'phone' => '08111111111',
            'role' => 'pharmacist',
            'status' => 'active',
        ]);

        // Create sample pharmacies
        $pharmacy = Pharmacy::create([
            'name' => 'Apotek Sehat Sejahtera',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'phone' => '02142134213',
            'email' => 'apotek@meditrack.com',
            'license_number' => 'APT-001-2024',
            'manager_id' => $pharmacist->id,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'active',
        ]);

        // Add drug stock
        $drugs = ['Paracetamol', 'Ibuprofen', 'Amoxicillin', 'Metformin', 'Lisinopril'];
        foreach ($drugs as $drug) {
            DrugStock::create([
                'pharmacy_id' => $pharmacy->id,
                'drug_name' => $drug,
                'quantity' => rand(50, 500),
                'unit_price' => rand(5000, 50000),
                'expiry_date' => now()->addYear(),
                'manufacturer' => 'PT. Farmasi Indonesia',
                'batch_number' => 'BATCH-' . uniqid(),
            ]);
        }

        // Create sample appointments
        foreach ($patients as $patient) {
            for ($i = 0; $i < 2; $i++) {
                Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctors[rand(0, 2)]->id,
                    'appointment_date' => now()->addDays(rand(1, 30)),
                    'type' => collect(['consultation', 'follow-up', 'general-checkup'])->random(),
                    'description' => 'Pemeriksaan rutin',
                    'status' => rand(0, 1) ? 'scheduled' : 'completed',
                    'duration' => 30,
                    'location' => 'Ruang Pemeriksaan A',
                ]);
            }
        }

        // Create sample medical records
        $appointments = Appointment::where('status', 'completed')->get();
        foreach ($appointments as $appointment) {
            MedicalRecord::create([
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_id' => $appointment->id,
                'diagnosis' => collect(['Demam Biasa', 'Sakit Gigi', 'Kolesterol Tinggi'])->random(),
                'treatment' => 'Resep obat dan istirahat',
                'lab_results' => 'Normal',
                'medications' => 'Paracetamol 500mg',
                'follow_up_date' => now()->addWeeks(1),
                'notes' => 'Pasien diminta kontrol ulang setelah 1 minggu',
            ]);
        }

        // Create sample prescriptions
        $medicalRecords = MedicalRecord::all();
        foreach ($medicalRecords->take(5) as $record) {
            Prescription::create([
                'patient_id' => $record->patient_id,
                'doctor_id' => $record->doctor_id,
                'appointment_id' => $record->appointment_id,
                'medication' => $drugs[rand(0, count($drugs) - 1)],
                'dosage' => '1 tablet',
                'frequency' => '2x sehari',
                'duration' => rand(3, 14),
                'instructions' => 'Diminum setelah makan',
                'status' => 'active',
                'issue_date' => now(),
                'expiry_date' => now()->addMonths(3),
                'pharmacy_id' => $pharmacy->id,
            ]);
        }
    }
}
