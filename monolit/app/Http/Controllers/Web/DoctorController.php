<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    /**
     * Doctor Dashboard
     */
    public function dashboard(): View
    {
        $doctor = Auth::user();
        $todayAppointments = $doctor->doctorAppointments()
            ->whereDate('appointment_date', today())
            ->latest()
            ->get();
        $upcomingAppointments = $doctor->doctorAppointments()
            ->where('appointment_date', '>', now())
            ->latest()
            ->limit(5)
            ->get();
        $medicalRecords = $doctor->doctorMedicalRecords()->latest()->limit(5)->get();
        $patients = $doctor->patients()->limit(10)->get();

        return view('doctor.dashboard', compact('doctor', 'todayAppointments', 'upcomingAppointments', 'medicalRecords', 'patients'));
    }

    /**
     * List Appointments
     */
    public function appointmentsIndex(): View
    {
        $doctor = Auth::user();
        $appointments = $doctor->doctorAppointments()->latest()->paginate(10);

        return view('doctor.appointments', compact('appointments'));
    }

    /**
     * Show Appointment Details
     */
    public function showAppointment(Appointment $appointment): View
    {
        abort_if($appointment->doctor_id !== Auth::id(), 403);

        return view('doctor.appointments.show', compact('appointment'));
    }

    /**
     * Update Appointment (Mark as Complete)
     */
    public function updateAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_if($appointment->doctor_id !== Auth::id(), 403);

        $validated = $request->validate([
            'status' => 'required|in:completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);
        if ($validated['notes']) {
            $appointment->notes = $validated['notes'];
            $appointment->save();
        }

        return redirect()->route('doctor.appointments')->with('success', 'Appointment updated successfully');
    }

    /**
     * List Patients
     */
    public function patientsIndex(): View
    {
        $doctor = Auth::user();
        $patients = $doctor->patients()->paginate(10);

        return view('doctor.patients', compact('patients'));
    }

    /**
     * Show Patient Details
     */
    public function showPatient(User $patient): View
    {
        abort_if($patient->role !== 'patient', 403);

        $appointments = $patient->patientAppointments()
            ->where('doctor_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get();
        $medicalRecords = $patient->medicalRecords()->latest()->limit(10)->get();

        return view('doctor.patients.show', compact('patient', 'appointments', 'medicalRecords'));
    }

    /**
     * List Medical Records
     */
    public function medicalRecordsIndex(): View
    {
        $doctor = Auth::user();
        $medicalRecords = $doctor->doctorMedicalRecords()->latest()->paginate(10);

        return view('doctor.medical-records', compact('medicalRecords'));
    }

    /**
     * Show Create Medical Record Form
     */
    public function createMedicalRecord(): View
    {
        $patients = User::where('role', 'patient')->get();
        return view('doctor.medical-records.create', compact('patients'));
    }

    /**
     * Store New Medical Record
     */
    public function storeMedicalRecord(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'lab_results' => 'nullable|string',
            'medications' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $validated['doctor_id'] = Auth::id();

        MedicalRecord::create($validated);

        return redirect()->route('doctor.medical-records')->with('success', 'Medical record created successfully');
    }

    /**
     * Show Medical Record Details
     */
    public function showMedicalRecord(MedicalRecord $record): View
    {
        abort_if($record->doctor_id !== Auth::id(), 403);

        return view('doctor.medical-records.show', compact('record'));
    }

    /**
     * Show Edit Medical Record Form
     */
    public function editMedicalRecord(MedicalRecord $record): View
    {
        abort_if($record->doctor_id !== Auth::id(), 403);

        $patients = User::where('role', 'patient')->get();
        return view('doctor.medical-records.edit', compact('record', 'patients'));
    }

    /**
     * Update Medical Record
     */
    public function updateMedicalRecord(Request $request, MedicalRecord $record): RedirectResponse
    {
        abort_if($record->doctor_id !== Auth::id(), 403);

        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'lab_results' => 'nullable|string',
            'medications' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $record->update($validated);

        return redirect()->route('doctor.medical-records')->with('success', 'Medical record updated successfully');
    }

    /**
     * Delete Medical Record
     */
    public function destroyMedicalRecord(MedicalRecord $record): RedirectResponse
    {
        abort_if($record->doctor_id !== Auth::id(), 403);

        $record->delete();
        return redirect()->route('doctor.medical-records')->with('success', 'Medical record deleted successfully');
    }

    /**
     * List Prescriptions
     */
    public function prescriptionsIndex(): View
    {
        $doctor = Auth::user();
        $prescriptions = $doctor->prescriptions()->latest()->paginate(10);

        return view('doctor.prescriptions', compact('prescriptions'));
    }

    /**
     * Show Prescription Details
     */
    public function showPrescription(Prescription $prescription): View
    {
        abort_if($prescription->doctor_id !== Auth::id(), 403);

        return view('doctor.prescriptions.show', compact('prescription'));
    }

    /**
     * Doctor Profile
     */
    public function profile(): View
    {
        $doctor = Auth::user();
        return view('doctor.profile', compact('doctor'));
    }

    /**
     * Show Edit Profile Form
     */
    public function editProfile(): View
    {
        $doctor = Auth::user();
        return view('doctor.profile-edit', compact('doctor'));
    }

    /**
     * Update Doctor Profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $doctor = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'specialty' => 'nullable|string',
            'license_number' => 'nullable|string|unique:users,license_number,' . $doctor->id,
        ]);

        $doctor->update($validated);

        return redirect()->route('doctor.profile')->with('success', 'Profile updated successfully');
    }
}
