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
     * Handle login - Authenticate via User Service API
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            // Call User Service API to authenticate with timeout
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->retry(2, 500)
                ->post('http://localhost:3000/auth/login', [
                    'email' => $credentials['email'],
                    'password' => $credentials['password']
                ]);

            \Log::info('Auth API Response Status: ' . $response->status());
            
            if ($response->successful()) {
                $userData = $response->json(); // Response is not wrapped in 'data' key
                
                \Log::info('User authenticated: ' . $userData['email']);
                
                // Create session user from API response
                $user = User::updateOrCreate(
                    ['email' => $credentials['email']],
                    [
                        'name' => $userData['name'] ?? '',
                        'email' => $userData['email'],
                        'role' => $userData['role'] ?? 'patient',
                        'phone' => $userData['phone'] ?? '',
                        'address' => $userData['address'] ?? '',
                        'status' => 'active',
                    ]
                );

                // Manually authenticate
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();

                \Log::info('User logged in: ' . $user->email . ' (' . $user->role . ')');

                // Redirect based on role
                return match ($user->role) {
                    'patient' => redirect()->route('patient.dashboard'),
                    'doctor' => redirect()->route('doctor.dashboard'),
                    'pharmacist' => redirect()->route('pharmacist.dashboard'),
                    'admin' => redirect()->route('admin.dashboard'),
                    default => redirect()->route('home')
                };
            } else {
                \Log::warning('Authentication failed: Invalid credentials for ' . $credentials['email']);
                return back()->withErrors([
                    'email' => 'Invalid email or password.',
                ])->onlyInput('email');
            }
        } catch (\Throwable $e) {
            \Log::error('Login error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()->withErrors([
                'email' => 'Authentication service unavailable. Please try again.',
            ])->onlyInput('email');
        }
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
}
