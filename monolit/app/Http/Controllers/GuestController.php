<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pharmacy;
use Illuminate\View\View;

class GuestController extends Controller
{
    /**
     * Display guest dashboard
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_pharmacists' => User::where('role', 'pharmacist')->count(),
            'total_appointments' => Appointment::count(),
            'total_medical_records' => MedicalRecord::count(),
            'total_pharmacies' => Pharmacy::count(),
        ];

        return view('guest.dashboard', $stats);
    }
}
