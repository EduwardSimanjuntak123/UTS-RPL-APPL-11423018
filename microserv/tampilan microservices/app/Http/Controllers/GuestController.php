<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{
    private $apiUrl = 'http://localhost:3000/api';

    /**
     * Display guest dashboard with stats from microservices
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => 0,
            'total_doctors' => 0,
            'total_patients' => 0,
            'total_pharmacists' => 0,
            'total_appointments' => 0,
            'total_medical_records' => 0,
            'total_pharmacies' => 0,
        ];

        try {
            // Fetch stats from API Gateway
            $response = Http::timeout(10)->get("{$this->apiUrl}/dashboard/stats");
            if ($response->successful()) {
                $apiStats = $response->json()['data'] ?? [];
                $stats = array_merge($stats, $apiStats);
                Log::info('Guest dashboard stats fetched from API');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch dashboard stats from API: ' . $e->getMessage());
            // Return view with default stats (zeros)
        }

        return view('guest.dashboard', $stats);
    }
}
