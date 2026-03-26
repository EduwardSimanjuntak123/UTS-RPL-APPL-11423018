<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all users with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    /**
     * Create new user
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    /**
     * Get user by ID
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $user->load([
                'patientAppointments',
                'doctorAppointments',
                'medicalRecords',
                'prescriptions',
                'payments'
            ]),
        ]);
    }

    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();
        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Get all doctors
     */
    public function getDoctors(): JsonResponse
    {
        $doctors = User::where('role', 'doctor')
                       ->where('status', 'active')
                       ->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors,
        ]);
    }

    /**
     * Get all patients
     */
    public function getPatients(): JsonResponse
    {
        $patients = User::where('role', 'patient')
                        ->where('status', 'active')
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $patients,
        ]);
    }

    /**
     * Get all pharmacists
     */
    public function getPharmacists(): JsonResponse
    {
        $pharmacists = User::where('role', 'pharmacist')
                           ->where('status', 'active')
                           ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pharmacists,
        ]);
    }
}
