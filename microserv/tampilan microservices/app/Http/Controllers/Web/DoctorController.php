<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    private $apiUrl = 'http://localhost:3000/api/v1';

    /**
     * Doctor Dashboard - Fetch from API
     */
    public function dashboard(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/doctors/{$doctorId}/dashboard");
            $data = $response->json()['data'] ?? [];
            
            Log::info('Doctor dashboard fetched from API');
            return view('doctor.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Failed to fetch doctor dashboard: ' . $e->getMessage());
            return view('doctor.dashboard', []);
        }
    }

    /**
     * List Appointments - Fetch from API
     */
    public function appointmentsIndex(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/doctors/{$doctorId}/appointments");
            $appointments = $response->json()['data'] ?? [];
            
            Log::info('Doctor appointments fetched from API');
            return view('doctor.appointments', compact('appointments'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointments: ' . $e->getMessage());
            return view('doctor.appointments', ['appointments' => []]);
        }
    }

    /**
     * Show Appointment Details
     */
    public function showAppointment(string $appointmentId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/appointments/{$appointmentId}");
            $appointment = $response->json()['data'] ?? [];
            
            return view('doctor.appointments.show', compact('appointment'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointment: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Update Appointment via API
     */
    public function updateAppointment(Request $request, string $appointmentId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:completed,cancelled',
                'notes' => 'nullable|string',
            ]);

            Http::timeout(10)->put("{$this->apiUrl}/appointments/{$appointmentId}", $validated);
            Log::info("Appointment {$appointmentId} updated via API");
            return redirect()->route('doctor.appointments')->with('success', 'Appointment updated');
        } catch (\Exception $e) {
            Log::error('Failed to update appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update appointment');
        }
    }

    /**
     * List Patients via API
     */
    public function patientsIndex(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/doctors/{$doctorId}/patients");
            $patients = $response->json()['data'] ?? [];
            
            return view('doctor.patients', compact('patients'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch patients: ' . $e->getMessage());
            return view('doctor.patients', ['patients' => []]);
        }
    }

    /**
     * Show Patient Details via API
     */
    public function showPatient(string $patientId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients/{$patientId}");
            $patient = $response->json()['data'] ?? [];
            
            return view('doctor.patients.show', compact('patient'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch patient: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * List Medical Records via API
     */
    public function medicalRecordsIndex(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/doctors/{$doctorId}/medical-records");
            $medicalRecords = $response->json()['data'] ?? [];
            
            return view('doctor.medical-records', compact('medicalRecords'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical records: ' . $e->getMessage());
            return view('doctor.medical-records', ['medicalRecords' => []]);
        }
    }

    /**
     * Show Create Medical Record Form
     */
    public function createMedicalRecord(): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients?role=patient");
            $patients = $response->json()['data'] ?? [];
            return view('doctor.medical-records.create', compact('patients'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch patients: ' . $e->getMessage());
            return view('doctor.medical-records.create', ['patients' => []]);
        }
    }

    /**
     * Store New Medical Record via API
     */
    public function storeMedicalRecord(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required',
                'diagnosis' => 'required|string',
                'treatment' => 'required|string',
                'lab_results' => 'nullable|string',
                'medications' => 'nullable|string',
                'follow_up_date' => 'nullable|date',
            ]);

            $validated['doctor_id'] = Auth::id();

            Http::timeout(10)->post("{$this->apiUrl}/medical-records", $validated);
            Log::info('Medical record created via API');
            return redirect()->route('doctor.medical-records')->with('success', 'Medical record created');
        } catch (\Exception $e) {
            Log::error('Failed to create medical record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create medical record');
        }
    }

    /**
     * Show Medical Record Details via API
     */
    public function showMedicalRecord($recordId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/medical-records/{$recordId}");
            $record = $response->json()['data'] ?? $response->json();
            
            if ($record && $record['doctor_id'] === Auth::id()) {
                return view('doctor.medical-records.show', ['record' => $record]);
            }
            
            abort(403);
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical record: ' . $e->getMessage());
            return view('doctor.medical-records.show', ['record' => null, 'error' => 'Failed to fetch record']);
        }
    }

    /**
     * Show Edit Medical Record Form via API
     */
    public function editMedicalRecord($recordId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/medical-records/{$recordId}");
            $record = $response->json()['data'] ?? $response->json();
            
            if ($record && $record['doctor_id'] === Auth::id()) {
                $patients = [];
                $patientResponse = Http::timeout(10)->get("{$this->apiUrl}/users?role=patient");
                $patients = $patientResponse->json()['data'] ?? [];
                
                return view('doctor.medical-records.edit', compact('record', 'patients'));
            }
            
            abort(403);
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical record: ' . $e->getMessage());
            return view('doctor.medical-records.edit', ['record' => null, 'patients' => [], 'error' => 'Failed to fetch record']);
        }
    }

    /**
     * Update Medical Record via API
     */
    public function updateMedicalRecord(Request $request, $recordId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'diagnosis' => 'required|string',
                'treatment' => 'required|string',
                'lab_results' => 'nullable|string',
                'medications' => 'nullable|string',
                'follow_up_date' => 'nullable|date',
            ]);

            $response = Http::timeout(10)->put("{$this->apiUrl}/medical-records/{$recordId}", $validated);
            
            if ($response->successful()) {
                Log::info("Medical record {$recordId} updated via API");
                return redirect()->route('doctor.medical-records')->with('success', 'Medical record updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update medical record');
        } catch (\Exception $e) {
            Log::error('Failed to update medical record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update medical record');
        }
    }

    /**
     * Delete Medical Record via API
     */
    public function destroyMedicalRecord($recordId): RedirectResponse
    {
        try {
            $response = Http::timeout(10)->delete("{$this->apiUrl}/medical-records/{$recordId}");
            
            if ($response->successful()) {
                Log::info("Medical record {$recordId} deleted via API");
                return redirect()->route('doctor.medical-records')->with('success', 'Medical record deleted successfully');
            }

            return redirect()->back()->with('error', 'Failed to delete medical record');
        } catch (\Exception $e) {
            Log::error('Failed to delete medical record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete medical record');
        }
    }

    /**
     * List Prescriptions via API
     */
    public function prescriptionsIndex(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/doctors/{$doctorId}/prescriptions");
            $prescriptions = $response->json()['data'] ?? [];

            return view('doctor.prescriptions', compact('prescriptions'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch prescriptions: ' . $e->getMessage());
            return view('doctor.prescriptions', ['prescriptions' => []]);
        }
    }

    /**
     * Show Prescription Details via API
     */
    public function showPrescription($prescriptionId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/prescriptions/{$prescriptionId}");
            $prescription = $response->json()['data'] ?? $response->json();
            
            return view('doctor.prescriptions.show', ['prescription' => $prescription]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch prescription: ' . $e->getMessage());
            return view('doctor.prescriptions.show', ['prescription' => null, 'error' => 'Failed to fetch prescription']);
        }
    }

    /**
     * Doctor Profile via API
     */
    public function profile(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$doctorId}");
            $doctor = $response->json()['data'] ?? $response->json();
            
            return view('doctor.profile', ['doctor' => $doctor]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return view('doctor.profile', ['doctor' => null, 'error' => 'Failed to fetch profile']);
        }
    }

    /**
     * Show Edit Profile Form via API
     */
    public function editProfile(): View
    {
        try {
            $doctorId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$doctorId}");
            $doctor = $response->json()['data'] ?? $response->json();
            
            return view('doctor.profile-edit', ['doctor' => $doctor]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return view('doctor.profile-edit', ['doctor' => null, 'error' => 'Failed to fetch profile']);
        }
    }

    /**
     * Update Doctor Profile via API
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        try {
            $doctorId = Auth::id();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'specialty' => 'nullable|string',
                'license_number' => 'nullable|string',
            ]);

            $response = Http::timeout(10)->put("{$this->apiUrl}/users/{$doctorId}", $validated);
            
            if ($response->successful()) {
                Log::info("Doctor {$doctorId} profile updated via API");
                return redirect()->route('doctor.profile')->with('success', 'Profile updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update profile');
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update profile');
        }
    }
}
