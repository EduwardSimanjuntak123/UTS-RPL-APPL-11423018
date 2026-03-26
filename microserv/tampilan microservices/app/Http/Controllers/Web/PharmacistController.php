<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PharmacistController extends Controller
{
    private $apiUrl = 'http://localhost:3000/api/v1';
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Pharmacist Dashboard - Fetch from API
     */
    public function dashboard(): View
    {
        try {
            $pharmacistId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/pharmacists/{$pharmacistId}/dashboard");
            $data = $response->json()['data'] ?? [];
            
            Log::info('Pharmacist dashboard fetched from API');
            return view('pharmacist.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Failed to fetch pharmacist dashboard: ' . $e->getMessage());
            return view('pharmacist.dashboard', []);
        }
    }

    /**
     * List Drug Inventory via API
     */
    public function inventoryIndex(): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/pharmacy/drugs");
            $inventory = $response->json()['data'] ?? [];
            
            Log::info('Drug inventory fetched from API');
            return view('pharmacist.inventory', compact('inventory'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch inventory: ' . $e->getMessage());
            return view('pharmacist.inventory', ['inventory' => []]);
        }
    }

    /**
     * Update Drug Stock via API
     */
    public function updateInventory(Request $request, string $drugId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'expiry_date' => 'required|date',
                'reorder_level' => 'required|integer|min:0',
            ]);

            Http::timeout(10)->put("{$this->apiUrl}/pharmacy/drugs/{$drugId}", $validated);
            Log::info("Drug {$drugId} stock updated via API");
            return redirect()->route('pharmacist.inventory')->with('success', 'Drug stock updated');
        } catch (\Exception $e) {
            Log::error('Failed to update inventory: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update inventory');
        }
    }

    /**
     * Delete Drug Stock via API
     */
    public function destroyInventory(string $drugId): RedirectResponse
    {
        try {
            Http::timeout(10)->delete("{$this->apiUrl}/pharmacy/drugs/{$drugId}");
            Log::info("Drug {$drugId} deleted via API");
            return redirect()->route('pharmacist.inventory')->with('success', 'Drug removed successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete drug: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete drug');
        }
    }

    /**
     * List Prescription Orders via API
     */
    public function ordersIndex(): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/pharmacy/orders");
            $orders = $response->json()['data'] ?? [];
            
            Log::info('Prescription orders fetched from API');
            return view('pharmacist.orders', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return view('pharmacist.orders', ['orders' => []]);
        }
    }

    /**
     * Show Order Details via API
     */
    public function showOrder($orderId): View
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/pharmacy/orders/{$orderId}");
            $order = $response->json()['data'] ?? $response->json();
            
            return view('pharmacist.orders.show', ['order' => $order]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch order: ' . $e->getMessage());
            return view('pharmacist.orders.show', ['order' => null, 'error' => 'Failed to fetch order']);
        }
    }

    /**
     * Update Order Status via API
     */
    public function updateOrder(Request $request, $orderId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,ready,completed,cancelled',
            ]);

            $response = Http::timeout(10)->put("{$this->apiUrl}/pharmacy/orders/{$orderId}", $validated);
            
            if ($response->successful()) {
                Log::info("Order {$orderId} status updated via API");
                return redirect()->route('pharmacist.orders')->with('success', 'Order status updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update order');
        } catch (\Exception $e) {
            Log::error('Failed to update order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order');
        }
    }

    /**
     * Pharmacist Profile via API
     */
    public function profile(): View
    {
        try {
            $pharmacistId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$pharmacistId}");
            $pharmacist = $response->json()['data'] ?? $response->json();
            
            return view('pharmacist.profile', ['pharmacist' => $pharmacist]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return view('pharmacist.profile', ['pharmacist' => null, 'error' => 'Failed to fetch profile']);
        }
    }

    /**
     * Show Edit Pharmacist Profile Form via API
     */
    public function editProfile(): View
    {
        try {
            $pharmacistId = Auth::id();
            $response = Http::timeout(10)->get("{$this->apiUrl}/users/{$pharmacistId}");
            $pharmacist = $response->json()['data'] ?? $response->json();
            
            return view('pharmacist.profile-edit', ['pharmacist' => $pharmacist]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch profile: ' . $e->getMessage());
            return view('pharmacist.profile-edit', ['pharmacist' => null, 'error' => 'Failed to fetch profile']);
        }
    }

    /**
     * Update Pharmacist Profile via API
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        try {
            $pharmacistId = Auth::id();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            $response = Http::timeout(10)->put("{$this->apiUrl}/users/{$pharmacistId}", $validated);
            
            if ($response->successful()) {
                Log::info("Pharmacist {$pharmacistId} profile updated via API");
                return redirect()->route('pharmacist.profile')->with('success', 'Profile updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update profile');
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update profile');
        }
    }
}
