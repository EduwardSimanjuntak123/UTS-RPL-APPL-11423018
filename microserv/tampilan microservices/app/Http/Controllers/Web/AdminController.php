<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    private $apiUrl = 'http://localhost:3000/api/v1';

    /**
     * Admin Dashboard - Get stats from API
     */
    public function dashboard(): View
    {
        try {
            // Fetch stats from API instead of direct DB query
            $response = Http::timeout(10)->get("{$this->apiUrl}/stats/admin");
            $stats = $response->json()['data'] ?? [];
            
            Log::info('Dashboard stats fetched from API');
            return view('admin.dashboard', ['stats' => $stats]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard stats: ' . $e->getMessage());
            return view('admin.dashboard', ['stats' => []]);
        }
    }

    /**
     * Users Management Index - Get users from API
     */
    public function usersIndex(): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/users");
            $allUsers = $response->json()['data'] ?? [];
            
            $doctors = array_filter($allUsers, fn($u) => $u['role'] === 'doctor');
            $patients = array_filter($allUsers, fn($u) => $u['role'] === 'patient');
            $pharmacists = array_filter($allUsers, fn($u) => $u['role'] === 'pharmacist');

            Log::info('Users fetched from API');
            return view('admin.users', [
                'allUsers' => $allUsers,
                'doctors' => $doctors,
                'patients' => $patients,
                'pharmacists' => $pharmacists,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return view('admin.users', ['allUsers' => [], 'doctors' => [], 'patients' => [], 'pharmacists' => []]);
        }
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
     * Store New User via API
     */
    public function storeUser(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'role' => 'required|in:doctor,patient,pharmacist,admin',
                'password' => 'required|min:6|confirmed',
                'status' => 'in:active,inactive',
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            // Send to API instead of creating directly
            $response = Http::timeout(10)->post("{$this->apiUrl}/users", $validated);

            if ($response->successful()) {
                Log::info('User created via API');
                return redirect()->route('admin.users.index')->with('success', 'User created successfully');
            }

            return redirect()->back()->with('error', 'Failed to create user');
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create user');
        }
    }

    /**
     * Show User Details via API
     */
    public function showUser($userId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$userId}");
            
            if ($response->successful()) {
                $user = $response->json()['data'] ?? null;
                if ($user) {
                    return view('admin.users.show', compact('user'));
                }
            }
            
            Log::error('User not found via API');
            return view('admin.users.show', ['user' => null, 'error' => 'User not found']);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user: ' . $e->getMessage());
            return view('admin.users.show', ['user' => null, 'error' => 'Failed to fetch user']);
        }
    }

    /**
     * Show Edit User Form via API
     */
    public function editUser($userId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$userId}");
            $roles = ['doctor', 'patient', 'pharmacist', 'admin'];
            
            if ($response->successful()) {
                $user = $response->json()['data'] ?? null;
                if ($user) {
                    return view('admin.users.edit', compact('user', 'roles'));
                }
            }
            
            return view('admin.users.edit', ['user' => null, 'error' => 'User not found', 'roles' => $roles]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user for edit: ' . $e->getMessage());
            return view('admin.users.edit', ['user' => null, 'error' => 'Failed to fetch user', 'roles' => ['doctor', 'patient', 'pharmacist', 'admin']]);
        }
    }

    /**
     * Update User via API
     */
    public function updateUser(Request $request, $userId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'role' => 'required|in:doctor,patient,pharmacist,admin',
                'status' => 'in:active,inactive',
                'password' => 'nullable|min:6|confirmed',
            ]);

            if (!($validated['password'] ?? null)) {
                unset($validated['password']);
            }

            $response = Http::timeout(10)->put("{$this->apiUrl}/users/{$userId}", $validated);

            if ($response->successful()) {
                Log::info("User {$userId} updated via API");
                return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update user');
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update user');
        }
    }

    /**
     * Delete User via API
     */
    public function destroyUser($userId): RedirectResponse
    {
        try {
            $response = Http::timeout(10)->delete("{$this->apiUrl}/users/{$userId}");

            if ($response->successful()) {
                Log::info("User {$userId} deleted via API");
                return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
            }

            return redirect()->back()->with('error', 'Failed to delete user');
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete user');
        }
    }

    /**
     * Pharmacies Index via API (using drugs endpoint)
     */
    public function pharmaciesIndex(): View
    {
        try {
            // Get all drugs from pharmacy service
            $response = Http::timeout(10)->get("{$this->apiUrl}/drugs");
            $drugs = $response->json()['data'] ?? [];

            return view('admin.pharmacies', [
                'pharmacies' => $drugs,
                'totalPharmacies' => count($drugs),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch drugs: ' . $e->getMessage());
            return view('admin.pharmacies', ['pharmacies' => [], 'totalPharmacies' => 0]);
        }
    }

    /**
     * Show Create Pharmacy Form
     */
    public function createPharmacy(): View
    {
        $managers = [];
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/users?role=pharmacist");
            $managers = $response->json()['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch pharmacists: ' . $e->getMessage());
        }

        return view('admin.pharmacies.create', compact('managers'));
    }

    /**
     * Store New Pharmacy via API (using drugs endpoint)
     */
    public function storePharmacy(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'drug_name' => 'required|string|max:255',
                'generic_name' => 'nullable|string',
                'manufacturer' => 'required|string',
                'dosage' => 'required|string',
                'form_type' => 'nullable|string',
                'price' => 'required|numeric',
                'license_number' => 'required|string',
                'expiry_date' => 'required|date',
                'storage_condition' => 'nullable|string',
            ]);

            $response = Http::timeout(10)->post("{$this->apiUrl}/drugs", $validated);

            if ($response->successful()) {
                Log::info('Drug created via API');
                return redirect()->route('admin.pharmacies.index')->with('success', 'Drug/Pharmacy created successfully');
            }

            return redirect()->back()->with('error', 'Failed to create drug');
        } catch (\Exception $e) {
            Log::error('Failed to create drug: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create drug');
        }
    }

    /**
     * Show Pharmacy Details via API (using drugs endpoint)
     */
    public function showPharmacy($drugId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/drugs/{$drugId}");
            
            if ($response->successful()) {
                $pharmacy = $response->json()['data'] ?? $response->json();
                if ($pharmacy) {
                    return view('admin.pharmacies.show', compact('pharmacy'));
                }
            }
            
            return view('admin.pharmacies.show', ['pharmacy' => null, 'error' => 'Drug not found']);
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug: ' . $e->getMessage());
            return view('admin.pharmacies.show', ['pharmacy' => null, 'error' => 'Failed to fetch drug']);
        }
    }

    /**
     * Show Edit Pharmacy Form via API (using drugs endpoint)
     */
    public function editPharmacy($drugId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/drugs/{$drugId}");
            $pharmacy = $response->json()['data'] ?? $response->json();
            
            if (!$pharmacy) {
                return view('admin.pharmacies.edit', ['pharmacy' => null, 'managers' => [], 'error' => 'Drug not found']);
            }

            $managers = [];
            $managersResponse = Http::timeout(10)->get("{$this->apiUrl}/users?role=pharmacist");
            $managers = $managersResponse->json()['data'] ?? [];

            return view('admin.pharmacies.edit', compact('pharmacy', 'managers'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug for edit: ' . $e->getMessage());
            return view('admin.pharmacies.edit', ['pharmacy' => null, 'managers' => [], 'error' => 'Failed to fetch drug']);
        }
    }

    /**
     * Update Pharmacy via API (using drugs endpoint)
     */
    public function updatePharmacy(Request $request, $drugId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'drug_name' => 'required|string|max:255',
                'generic_name' => 'nullable|string',
                'manufacturer' => 'required|string',
                'dosage' => 'required|string',
                'form_type' => 'nullable|string',
                'price' => 'required|numeric',
                'license_number' => 'required|string',
                'expiry_date' => 'required|date',
                'storage_condition' => 'nullable|string',
            ]);

            // Note: Update endpoint might need to be added to microservices
            $response = Http::timeout(10)->put("{$this->apiUrl}/drugs/{$drugId}", $validated);

            if ($response->successful()) {
                Log::info("Drug {$drugId} updated via API");
                return redirect()->route('admin.pharmacies.index')->with('success', 'Drug updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update drug');
        } catch (\Exception $e) {
            Log::error('Failed to update drug: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update drug');
        }
    }

    /**
     * Delete Pharmacy via API (using drugs endpoint)
     */
    public function destroyPharmacy($drugId): RedirectResponse
    {
        try {
            // Note: Delete endpoint might need to be added to microservices
            $response = Http::timeout(10)->delete("{$this->apiUrl}/drugs/{$drugId}");

            if ($response->successful()) {
                Log::info("Drug {$drugId} deleted via API");
                return redirect()->route('admin.pharmacies.index')->with('success', 'Drug deleted successfully');
            }

            return redirect()->back()->with('error', 'Failed to delete drug');
        } catch (\Exception $e) {
            Log::error('Failed to delete drug: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete drug');
        }
    }

    /**
     * Analytics Dashboard via API
     */
    public function analytics(): View
    {
        $stats = [
            'total_users' => 0,
            'total_appointments' => 0,
            'total_medical_records' => 0,
            'total_pharmacies' => 0,
        ];

        try {
            // Fetch stats from API
            $response = Http::timeout(10)->get("{$this->apiUrl}/stats");
            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $stats = array_merge($stats, $data);
            }
            
            Log::info('Analytics stats fetched from API');
        } catch (\Exception $e) {
            Log::error('Failed to fetch analytics stats: ' . $e->getMessage());
        }

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
