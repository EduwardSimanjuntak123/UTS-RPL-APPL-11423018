<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Patient Dashboard
     */
    public function dashboard(): View
    {
        $patient = Auth::user();
        $appointments = $patient->patientAppointments()->latest()->limit(5)->get();
        $medicalRecords = $patient->medicalRecords()->latest()->limit(5)->get();
        $prescriptions = $patient->patientPrescriptionRecords()->latest()->limit(5)->get();
        $payments = $patient->payments()->latest()->limit(5)->get();

        return view('patient.dashboard', compact('patient', 'appointments', 'medicalRecords', 'prescriptions', 'payments'));
    }

    /**
     * List Patient Appointments
     */
    public function appointmentsIndex(): View
    {
        $patient = Auth::user();
        $appointments = $patient->patientAppointments()->latest()->paginate(10);

        return view('patient.appointments', compact('appointments'));
    }

    /**
     * Show Create Appointment Form
     */
    public function createAppointment(): View
    {
        $doctors = \App\Models\User::where('role', 'doctor')->get();
        return view('patient.appointments.create', compact('doctors'));
    }

    /**
     * Store New Appointment
     */
    public function storeAppointment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'reason' => 'required|string',
            'appointment_type' => 'required|string',
        ]);

        $validated['patient_id'] = Auth::id();
        $validated['status'] = 'scheduled';

        Appointment::create($validated);

        return redirect()->route('patient.appointments')->with('success', 'Appointment booked successfully');
    }

    /**
     * Show Appointment Details
     */
    public function showAppointment(Appointment $appointment): View
    {
        // Verify this is the logged-in patient's appointment
        abort_if($appointment->patient_id !== Auth::id(), 403);

        return view('patient.appointments.show', compact('appointment'));
    }

    /**
     * Show Edit Appointment Form
     */
    public function editAppointment(Appointment $appointment): View
    {
        abort_if($appointment->patient_id !== Auth::id(), 403);

        $doctors = \App\Models\User::where('role', 'doctor')->get();
        return view('patient.appointments.edit', compact('appointment', 'doctors'));
    }

    /**
     * Update Appointment
     */
    public function updateAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_if($appointment->patient_id !== Auth::id(), 403);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'reason' => 'required|string',
            'appointment_type' => 'required|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('patient.appointments')->with('success', 'Appointment updated successfully');
    }

    /**
     * Delete Appointment
     */
    public function destroyAppointment(Appointment $appointment): RedirectResponse
    {
        abort_if($appointment->patient_id !== Auth::id(), 403);

        $appointment->delete();
        return redirect()->route('patient.appointments')->with('success', 'Appointment cancelled successfully');
    }

    /**
     * List Patient Medical Records
     */
    public function medicalRecordsIndex(): View
    {
        $patient = Auth::user();
        $medicalRecords = $patient->medicalRecords()->latest()->paginate(10);

        return view('patient.medical-records', compact('medicalRecords'));
    }

    /**
     * Show Medical Record Details
     */
    public function showMedicalRecord(MedicalRecord $record): View
    {
        abort_if($record->patient_id !== Auth::id(), 403);

        return view('patient.medical-records.show', compact('record'));
    }

    /**
     * List Prescriptions
     */
    public function prescriptionsIndex(): View
    {
        $patient = Auth::user();
        $prescriptions = $patient->patientPrescriptionRecords()->latest()->paginate(10);

        return view('patient.prescriptions', compact('prescriptions'));
    }

    /**
     * Show Prescription Details
     */
    public function showPrescription($id): View
    {
        $prescription = \App\Models\Prescription::findOrFail($id);
        abort_if($prescription->patient_id !== Auth::id(), 403);

        return view('patient.prescriptions.show', compact('prescription'));
    }

    /**
     * List Payments
     */
    public function paymentsIndex(): View
    {
        $patient = Auth::user();
        $payments = $patient->payments()->latest()->paginate(10);

        return view('patient.payments', compact('payments'));
    }

    /**
     * Show Payment Details
     */
    public function showPayment($id): View
    {
        $payment = \App\Models\Payment::findOrFail($id);
        abort_if($payment->patient_id !== Auth::id(), 403);

        return view('patient.payments.show', compact('payment'));
    }

    /**
     * Patient Profile
     */
    public function profile(): View
    {
        $patient = Auth::user();
        return view('patient.profile', compact('patient'));
    }

    /**
     * Show Edit Patient Profile Form
     */
    public function editProfile(): View
    {
        $patient = Auth::user();
        return view('patient.profile-edit', compact('patient'));
    }

    /**
     * Update Patient Profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $patient = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully');
    }
}
