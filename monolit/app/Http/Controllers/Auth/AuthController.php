<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on role
            return match ($user->role) {
                'patient' => redirect()->route('patient.dashboard'),
                'doctor' => redirect()->route('doctor.dashboard'),
                'pharmacist' => redirect()->route('pharmacist.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
                default => redirect()->route('home')
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Dashboard redirect based on role
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        return match ($user->role) {
            'patient' => redirect()->route('patient.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'pharmacist' => redirect()->route('pharmacist.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home')
        };
    }

    /**
     * API: Register User
     */
    public function apiRegister(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'required|string|max:20',
                'role' => 'required|in:patient,doctor,pharmacist,admin',
                'status' => 'nullable|in:active,inactive,suspended',
                'specialization' => 'nullable|string|max:255',
                'license_number' => 'nullable|string|max:255',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'role' => $validated['role'],
                'status' => $validated['status'] ?? 'active',
                'specialty' => $validated['specialization'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'status' => $user->status,
                    'created_at' => $user->created_at
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Login User
     */
    public function apiLogin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if (!Auth::attempt($validated)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid email or password'
                ], 401);
            }

            $user = Auth::user();

            // Generate simple token (bisa dikembangkan jadi JWT)
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'status' => $user->status
                    ],
                    'token' => $token
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Logout User
     */
    public function apiLogout(Request $request)
    {
        try {
            // Revoke token
            if ($request->user()) {
                $request->user()->tokens()->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
