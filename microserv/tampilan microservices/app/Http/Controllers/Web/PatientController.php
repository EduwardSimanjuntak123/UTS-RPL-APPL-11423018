<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    private $apiUrl = 'http://localhost:3000/api/v1';

    /**
     * Patient Dashboard - Fetch from API
     */
    public function dashboard(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients/{$patientId}/dashboard");
            $data = $response->json()['data'] ?? [];
            
            Log::info('Patient dashboard fetched from API');
            return view('patient.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Failed to fetch patient dashboard: ' . $e->getMessage());
            return view('patient.dashboard', []);
        }
    }

    /**
     * List Patient Appointments via API
     */
    public function appointmentsIndex(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients/{$patientId}/appointments");
            $appointments = $response->json()['data'] ?? [];
            
            return view('patient.appointments', compact('appointments'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointments: ' . $e->getMessage());
            return view('patient.appointments', ['appointments' => []]);
        }
    }

    /**
     * Show Create Appointment Form
     */
    public function createAppointment(): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/users?role=doctor");
            $doctors = $response->json()['data'] ?? [];
            return view('patient.appointments.create', compact('doctors'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch doctors: ' . $e->getMessage());
            return view('patient.appointments.create', ['doctors' => []]);
        }
    }

    /**
     * Store New Appointment via API
     */
    public function storeAppointment(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'doctor_id' => 'required',
                'appointment_date' => 'required|date',
                'reason' => 'required|string',
                'appointment_type' => 'required|string',
            ]);

            $validated['patient_id'] = Auth::id();
            $validated['status'] = 'scheduled';

            Http::timeout(10)->post("{$this->apiUrl}/appointments", $validated);
            Log::info('Appointment booked via API');
            return redirect()->route('patient.appointments')->with('success', 'Appointment booked');
        } catch (\Exception $e) {
            Log::error('Failed to book appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to book appointment');
        }
    }

    /**
     * Show Appointment Details via API
     */
    public function showAppointment($appointmentId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/appointments/{$appointmentId}");
            $appointment = $response->json()['data'] ?? $response->json();
            
            if ($appointment && $appointment['patient_id'] === Auth::id()) {
                return view('patient.appointments.show', compact('appointment'));
            }
            
            abort(403);
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointment: ' . $e->getMessage());
            return view('patient.appointments.show', ['appointment' => null, 'error' => 'Failed to fetch appointment']);
        }
    }

    /**
     * Show Edit Appointment Form via API
     */
    public function editAppointment($appointmentId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/appointments/{$appointmentId}");
            $appointment = $response->json()['data'] ?? $response->json();
            
            if ($appointment && $appointment['patient_id'] === Auth::id()) {
                $doctors = [];
                $doctorResponse = Http::timeout(10)->get("{$this->apiUrl}/users?role=doctor");
                $doctors = $doctorResponse->json()['data'] ?? [];
                
                return view('patient.appointments.edit', compact('appointment', 'doctors'));
            }
            
            abort(403);
        } catch (\Exception $e) {
            Log::error('Failed to fetch appointment: ' . $e->getMessage());
            return view('patient.appointments.edit', ['appointment' => null, 'doctors' => [], 'error' => 'Failed to fetch appointment']);
        }
    }

    /**
     * Update Appointment via API
     */
    public function updateAppointment(Request $request, $appointmentId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'doctor_id' => 'required',
                'appointment_date' => 'required|date',
                'reason' => 'required|string',
                'appointment_type' => 'required|string',
            ]);

            $response = Http::timeout(10)->put("{$this->apiUrl}/appointments/{$appointmentId}", $validated);
            
            if ($response->successful()) {
                Log::info("Appointment {$appointmentId} updated via API");
                return redirect()->route('patient.appointments')->with('success', 'Appointment updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update appointment');
        } catch (\Exception $e) {
            Log::error('Failed to update appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update appointment');
        }
    }

    /**
     * Delete Appointment via API
     */
    public function destroyAppointment($appointmentId): RedirectResponse
    {
        try {
            $response = Http::timeout(10)->delete("{$this->apiUrl}/appointments/{$appointmentId}");
            
            if ($response->successful()) {
                Log::info("Appointment {$appointmentId} deleted via API");
                return redirect()->route('patient.appointments')->with('success', 'Appointment cancelled successfully');
            }

            return redirect()->back()->with('error', 'Failed to cancel appointment');
        } catch (\Exception $e) {
            Log::error('Failed to cancel appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel appointment');
        }
    }

    /**
     * List Patient Medical Records via API
     */
    public function medicalRecordsIndex(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients/{$patientId}/medical-records");
            $medicalRecords = $response->json()['data'] ?? [];

            return view('patient.medical-records', compact('medicalRecords'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical records: ' . $e->getMessage());
            return view('patient.medical-records', ['medicalRecords' => []]);
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
            
            return view('patient.medical-records.show', ['record' => $record]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch medical record: ' . $e->getMessage());
            return view('patient.medical-records.show', ['record' => null, 'error' => 'Failed to fetch record']);
        }
    }

    /**
     * List Prescriptions via API
     */
    public function prescriptionsIndex(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients/{$patientId}/prescriptions");
            $prescriptions = $response->json()['data'] ?? [];

            return view('patient.prescriptions', compact('prescriptions'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch prescriptions: ' . $e->getMessage());
            return view('patient.prescriptions', ['prescriptions' => []]);
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
            
            return view('patient.prescriptions.show', ['prescription' => $prescription]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch prescription: ' . $e->getMessage());
            return view('patient.prescriptions.show', ['prescription' => null, 'error' => 'Failed to fetch prescription']);
        }
    }

    /**
     * List Payments via API
     */
    public function paymentsIndex(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/patients/{$patientId}/payments");
            $payments = $response->json()['data'] ?? [];

            return view('patient.payments', compact('payments'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch payments: ' . $e->getMessage());
            return view('patient.payments', ['payments' => []]);
        }
    }

    /**
     * Show Payment Details via API
     */
    public function showPayment($paymentId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/payments/{$paymentId}");
            $payment = $response->json()['data'] ?? $response->json();
            
            return view('patient.payments.show', ['payment' => $payment]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment: ' . $e->getMessage());
            return view('patient.payments.show', ['payment' => null, 'error' => 'Failed to fetch payment']);
        }
    }

    /**
     * Patient Profile via API
     */
    public function profile(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$patientId}");
            $patient = $response->json()['data'] ?? $response->json();
            
            return view('patient.profile', ['patient' => $patient]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return view('patient.profile', ['patient' => null, 'error' => 'Failed to fetch profile']);
        }
    }

    /**
     * Show Edit Patient Profile Form via API
     */
    public function editProfile(): View
    {
        try {
            $patientId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$patientId}");
            $patient = $response->json()['data'] ?? $response->json();
            
            return view('patient.profile-edit', ['patient' => $patient]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return view('patient.profile-edit', ['patient' => null, 'error' => 'Failed to fetch profile']);
        }
    }

    /**
     * Update Patient Profile via API
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        try {
            $patientId = Auth::id();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string',
            ]);

            $response = Http::timeout(10)->put("{$this->apiUrl}/users/{$patientId}", $validated);
            
            if ($response->successful()) {
                Log::info("Patient {$patientId} profile updated via API");
                return redirect()->route('patient.profile')->with('success', 'Profile updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update profile');
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update profile');
        }
    }
}
