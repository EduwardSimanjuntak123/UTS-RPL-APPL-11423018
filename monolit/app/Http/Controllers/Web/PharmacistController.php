<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DrugStock;
use App\Models\PrescriptionOrder;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacistController extends Controller
{
    /**
     * Pharmacist Dashboard
     */
    public function dashboard(): View
    {
        $pharmacist = Auth::user();
        $lowStockItems = DrugStock::where('quantity', '<', $this->getReorderLevel())
            ->limit(5)
            ->get();
        $pendingOrders = PrescriptionOrder::where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();
        $totalInventoryValue = DrugStock::sum(DB::raw('quantity * price'));

        return view('pharmacist.dashboard', compact('pharmacist', 'lowStockItems', 'pendingOrders', 'totalInventoryValue'));
    }

    /**
     * List Drug Inventory
     */
    public function inventoryIndex(): View
    {
        $inventory = DrugStock::paginate(15);
        $lowStockItems = DrugStock::where('quantity', '<', 10)->count();

        return view('pharmacist.inventory', compact('inventory', 'lowStockItems'));
    }

    /**
     * Update Drug Stock
     */
    public function updateInventory(Request $request, DrugStock $drug): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after:now',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $drug->update($validated);

        return redirect()->route('pharmacist.inventory')->with('success', 'Drug stock updated successfully');
    }

    /**
     * Delete Drug Stock
     */
    public function destroyInventory(DrugStock $drug): RedirectResponse
    {
        $drug->delete();
        return redirect()->route('pharmacist.inventory')->with('success', 'Drug removed from inventory successfully');
    }

    /**
     * List Prescription Orders
     */
    public function ordersIndex(): View
    {
        $orders = PrescriptionOrder::paginate(15);
        $pendingCount = PrescriptionOrder::where('status', 'pending')->count();
        $readyCount = PrescriptionOrder::where('status', 'ready')->count();

        return view('pharmacist.orders', compact('orders', 'pendingCount', 'readyCount'));
    }

    /**
     * Show Order Details
     */
    public function showOrder(PrescriptionOrder $order): View
    {
        return view('pharmacist.orders.show', compact('order'));
    }

    /**
     * Update Order Status
     */
    public function updateOrder(Request $request, PrescriptionOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,ready,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('pharmacist.orders')->with('success', 'Order status updated successfully');
    }

    /**
     * Pharmacist Profile
     */
    public function profile(): View
    {
        $pharmacist = Auth::user();
        return view('pharmacist.profile', compact('pharmacist'));
    }

    /**
     * Show Edit Pharmacist Profile Form
     */
    public function editProfile(): View
    {
        $pharmacist = Auth::user();
        return view('pharmacist.profile-edit', compact('pharmacist'));
    }

    /**
     * Update Pharmacist Profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $pharmacist = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $pharmacist->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $pharmacist->update($validated);

        return redirect()->route('pharmacist.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Get reorder level
     */
    private function getReorderLevel(): int
    {
        return 10; // Default reorder level
    }
}
