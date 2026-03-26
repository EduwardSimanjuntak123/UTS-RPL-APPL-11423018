<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_pharmacists' => User::where('role', 'pharmacist')->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'total_pharmacies' => Pharmacy::count(),
        ];

        return view('admin.dashboard', $stats);
    }

    /**
     * Users Management Index
     */
    public function usersIndex(): View
    {
        $allUsers = User::all();
        $doctors = User::where('role', 'doctor')->get();
        $patients = User::where('role', 'patient')->get();
        $pharmacists = User::where('role', 'pharmacist')->get();

        return view('admin.users', [
            'allUsers' => $allUsers,
            'doctors' => $doctors,
            'patients' => $patients,
            'pharmacists' => $pharmacists,
            'totalUsers' => $allUsers->count(),
            'totalDoctors' => $doctors->count(),
            'totalPatients' => $patients->count(),
            'totalPharmacists' => $pharmacists->count(),
        ]);
    }

    /**
     * Show Create User Form
     */
    public function createUser(): View
    {
        $roles = ['doctor', 'patient', 'pharmacist', 'admin'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store New User
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'role' => 'required|in:doctor,patient,pharmacist,admin',
            'password' => 'required|min:6|confirmed',
            'status' => 'in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $validated['status'] ?? 'active';

        User::create($validated);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    /**
     * Show User Details
     */
    public function showUser(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show Edit User Form
     */
    public function editUser(User $user): View
    {
        $roles = ['doctor', 'patient', 'pharmacist', 'admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update User
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'role' => 'required|in:doctor,patient,pharmacist,admin',
            'status' => 'in:active,inactive',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validated['password'] ?? null) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    /**
     * Delete User
     */
    public function destroyUser(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    /**
     * Pharmacies Index
     */
    public function pharmaciesIndex(): View
    {
        $pharmacies = Pharmacy::all();

        return view('admin.pharmacies', [
            'pharmacies' => $pharmacies,
            'totalPharmacies' => $pharmacies->count(),
        ]);
    }

    /**
     * Show Create Pharmacy Form
     */
    public function createPharmacy(): View
    {
        $managers = User::where('role', 'pharmacist')->get();
        return view('admin.pharmacies.create', compact('managers'));
    }

    /**
     * Store New Pharmacy
     */
    public function storePharmacy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'license_number' => 'required|unique:pharmacies',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        Pharmacy::create($validated);

        return redirect()->route('admin.pharmacies')->with('success', 'Pharmacy created successfully');
    }

    /**
     * Show Pharmacy Details
     */
    public function showPharmacy(Pharmacy $pharmacy): View
    {
        return view('admin.pharmacies.show', compact('pharmacy'));
    }

    /**
     * Show Edit Pharmacy Form
     */
    public function editPharmacy(Pharmacy $pharmacy): View
    {
        $managers = User::where('role', 'pharmacist')->get();
        return view('admin.pharmacies.edit', compact('pharmacy', 'managers'));
    }

    /**
     * Update Pharmacy
     */
    public function updatePharmacy(Request $request, Pharmacy $pharmacy): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'license_number' => 'required|unique:pharmacies,license_number,' . $pharmacy->id,
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $pharmacy->update($validated);

        return redirect()->route('admin.pharmacies')->with('success', 'Pharmacy updated successfully');
    }

    /**
     * Delete Pharmacy
     */
    public function destroyPharmacy(Pharmacy $pharmacy): RedirectResponse
    {
        $pharmacy->delete();
        return redirect()->route('admin.pharmacies')->with('success', 'Pharmacy deleted successfully');
    }

    /**
     * Analytics Dashboard
     */
    public function analytics(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_appointments' => \App\Models\Appointment::count(),
            'total_medical_records' => \App\Models\MedicalRecord::count(),
            'total_pharmacies' => Pharmacy::count(),
        ];

        return view('admin.analytics', $stats);
    }

    /**
     * Settings Page
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Update Settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        // Handle settings update
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
    }
}
