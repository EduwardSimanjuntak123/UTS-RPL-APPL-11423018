<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * UserController - Updated untuk menggunakan UserService (Microservices)
 * 
 * Controller ini hanya menangani HTTP requests dan responses
 * Semua data operations dihandle oleh UserService yang call API Gateway
 */
class UserControllerMicroservices extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Get all users dengan optional filtering
     * GET /api/users?role=doctor&status=active&search=john
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            // Filter by role
            if ($request->has('role')) {
                $filters['role'] = $request->role;
            }

            // Filter by status
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            // Search by name or email
            if ($request->has('search')) {
                $filters['search'] = $request->search;
            }

            $users = $this->userService->getAllUsers($filters);

            return response()->json([
                'status' => 'success',
                'data' => $users,
                'message' => 'Users fetched successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single user by ID
     * GET /api/users/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch user {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new user
     * POST /api/users
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'role' => 'required|in:patient,doctor,pharmacist,admin',
                'specialty' => 'nullable|string',
                'license_number' => 'nullable|string|unique:users',
                'insurance_provider' => 'nullable|string',
            ]);

            $result = $this->userService->createUser($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update user
     * PUT /api/users/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'role' => 'nullable|in:patient,doctor,pharmacist,admin',
                'specialty' => 'nullable|string',
                'insurance_provider' => 'nullable|string',
            ]);

            $result = $this->userService->updateUser($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update user {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete user
     * DELETE /api/users/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->userService->deleteUser($id);

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete user {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * User login
     * POST /auth/login
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $result = $this->userService->login($validated['email'], $validated['password']);

            // Token sudah disimpan di session oleh UserService
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $result['token'],
                'user' => $result['user'] ?? [],
            ]);
        } catch (\Exception $e) {
            Log::warning('Login attempt failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * User register
     * POST /auth/register
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'role' => 'required|in:patient,doctor',
            ]);

            $result = $this->userService->register($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get all roles
     * GET /api/roles
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = $this->userService->getAllRoles();

            return response()->json([
                'status' => 'success',
                'data' => $roles,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch roles: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch roles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get audit logs
     * GET /api/audit-logs?user_id=1
     */
    public function auditLogs(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('user_id')) {
                $userId = $request->get('user_id');
                $logs = $this->userService->getUserAuditLogs($userId);
            } else {
                $logs = $this->userService->getAuditLogs();
            }

            return response()->json([
                'status' => 'success',
                'data' => $logs,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch audit logs: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch audit logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * User logout
     * POST /auth/logout
     */
    public function logout(): JsonResponse
    {
        session()->forget('api_token');
        session()->forget('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
