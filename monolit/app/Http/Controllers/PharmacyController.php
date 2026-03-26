<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\DrugStock;
use App\Http\Requests\StorePharmacyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    /**
     * Get all pharmacies
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pharmacy::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pharmacies = $query->with(['manager', 'drugStock'])
                            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $pharmacies,
        ]);
    }

    /**
     * Create new pharmacy
     */
    public function store(StorePharmacyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['status'] = 'active';

        $pharmacy = Pharmacy::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Pharmacy created successfully',
            'data' => $pharmacy->load(['manager', 'drugStock']),
        ], 201);
    }

    /**
     * Get pharmacy by ID
     */
    public function show(Pharmacy $pharmacy): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $pharmacy->load(['manager', 'drugStock', 'prescriptionOrders']),
        ]);
    }

    /**
     * Update pharmacy
     */
    public function update(Request $request, Pharmacy $pharmacy): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|unique:pharmacies,email,' . $pharmacy->id,
            'status' => 'nullable|in:active,inactive,suspended',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $pharmacy->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Pharmacy updated successfully',
            'data' => $pharmacy,
        ]);
    }

    /**
     * Delete pharmacy
     */
    public function destroy(Pharmacy $pharmacy): JsonResponse
    {
        $pharmacy->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pharmacy deleted successfully',
        ]);
    }

    /**
     * Manage drug stock
     */
    public function addDrugStock(Request $request, Pharmacy $pharmacy): JsonResponse
    {
        $validated = $request->validate([
            'drug_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0.01',
            'expiry_date' => 'required|date',
            'manufacturer' => 'required|string',
            'batch_number' => 'required|string|unique:drug_stock',
        ]);

        $validated['pharmacy_id'] = $pharmacy->id;
        $stock = DrugStock::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Drug stock added successfully',
            'data' => $stock,
        ], 201);
    }

    /**
     * Get drug stock
     */
    public function getDrugStock(Pharmacy $pharmacy): JsonResponse
    {
        $stock = DrugStock::where('pharmacy_id', $pharmacy->id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $stock,
        ]);
    }

    /**
     * Update drug stock quantity
     */
    public function updateDrugStock(Request $request, Pharmacy $pharmacy, DrugStock $drugStock): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        if ($drugStock->pharmacy_id != $pharmacy->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Drug stock does not belong to this pharmacy',
            ], 403);
        }

        $drugStock->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Drug stock updated successfully',
            'data' => $drugStock,
        ]);
    }

    /**
     * Delete drug stock
     */
    public function deleteDrugStock(Pharmacy $pharmacy, DrugStock $drugStock): JsonResponse
    {
        if ($drugStock->pharmacy_id != $pharmacy->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Drug stock does not belong to this pharmacy',
            ], 403);
        }

        $drugStock->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Drug stock deleted successfully',
        ]);
    }

    /**
     * Get nearby pharmacies (using coordinates)
     */
    public function getNearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'distance' => 'nullable|numeric|min:1',
        ]);

        $distance = $validated['distance'] ?? 5; // Default 5 km
        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];

        // Haversine formula for distance calculation
        $pharmacies = Pharmacy::selectRaw(
            "*, (6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
        )
        ->having('distance', '<', $distance)
        ->orderBy('distance')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pharmacies,
        ]);
    }
}
