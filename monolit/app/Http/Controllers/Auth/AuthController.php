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
}
