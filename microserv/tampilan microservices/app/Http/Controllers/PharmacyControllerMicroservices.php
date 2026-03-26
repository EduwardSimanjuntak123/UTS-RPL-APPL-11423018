<?php

namespace App\Http\Controllers;

use App\Services\PharmacyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * PharmacyController - Updated untuk menggunakan PharmacyService (Microservices)
 * 
 * Menangani pharmacy management, drug inventory, dan ordering
 * melalui microservices API
 */
class PharmacyControllerMicroservices extends Controller
{
    public function __construct(private PharmacyService $pharmacyService)
    {
    }

    /**
     * ==================== PHARMACIES ====================
     */

    /**
     * Get all pharmacies
     * GET /api/pharmacies?status=active&search=keyword
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('search')) {
                $filters['search'] = $request->search;
            }

            $pharmacies = $this->pharmacyService->getAllPharmacies($filters);

            return response()->json([
                'status' => 'success',
                'data' => $pharmacies,
                'message' => 'Pharmacies fetched successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch pharmacies: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch pharmacies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single pharmacy
     * GET /api/pharmacies/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $pharmacy = $this->pharmacyService->getPharmacyById($id);

            if (!$pharmacy) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pharmacy not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $pharmacy,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch pharmacy {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch pharmacy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new pharmacy
     * POST /api/pharmacies
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'license_number' => 'required|string|max:100|unique:pharmacies',
                'manager_id' => 'nullable|uuid',
            ]);

            $result = $this->pharmacyService->createPharmacy($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Pharmacy created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create pharmacy: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create pharmacy',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update pharmacy
     * PUT /api/pharmacies/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'status' => 'nullable|in:active,inactive,suspended',
            ]);

            $result = $this->pharmacyService->updatePharmacy($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Pharmacy updated successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update pharmacy {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update pharmacy',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete pharmacy
     * DELETE /api/pharmacies/{id}
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->pharmacyService->deletePharmacy($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Pharmacy deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete pharmacy {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete pharmacy',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ==================== DRUG STOCK ====================
     */

    /**
     * Get all drug stock
     * GET /api/drug-stock?pharmacy_id=uuid&status=available
     */
    public function getDrugStock(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('pharmacy_id')) {
                $filters['pharmacy_id'] = $request->pharmacy_id;
            }
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('search')) {
                $filters['search'] = $request->search;
            }

            $stock = $this->pharmacyService->getDrugStock($filters);

            return response()->json([
                'status' => 'success',
                'data' => $stock,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug stock: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch drug stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add drug to stock
     * POST /api/drug-stock
     */
    public function addDrugStock(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pharmacy_id' => 'required|uuid',
                'drug_name' => 'required|string|max:255',
                'drug_code' => 'required|string|max:50',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0.01',
                'expiry_date' => 'required|date',
                'manufacturer' => 'nullable|string|max:255',
                'batch_number' => 'nullable|string|max:100',
            ]);

            $result = $this->pharmacyService->addDrugStock($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Drug added to stock successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to add drug stock: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add drug stock',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update drug stock quantity
     * PUT /api/drug-stock/{id}
     */
    public function updateDrugStock(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'nullable|integer|min:0',
                'unit_price' => 'nullable|numeric|min:0.01',
                'status' => 'nullable|in:available,out_of_stock,discontinued',
            ]);

            $result = $this->pharmacyService->updateDrugStock($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Drug stock updated successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update drug stock {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update drug stock',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ==================== DRUG ORDERS ====================
     */

    /**
     * Get all drug orders
     * GET /api/drug-orders?status=pending&pharmacy_id=uuid
     */
    public function getDrugOrders(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('pharmacy_id')) {
                $filters['pharmacy_id'] = $request->pharmacy_id;
            }

            $orders = $this->pharmacyService->getDrugOrders($filters);

            return response()->json([
                'status' => 'success',
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch drug orders: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch drug orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create drug order
     * POST /api/drug-orders
     */
    public function createDrugOrder(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pharmacy_id' => 'required|uuid',
                'supplier_id' => 'required|uuid',
                'items' => 'required|array',
                'items.*.drug_id' => 'required|uuid',
                'items.*.quantity' => 'required|integer|min:1',
                'expected_delivery_date' => 'required|date',
                'total_cost' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string',
            ]);

            $order = $this->pharmacyService->createDrugOrder($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Drug order created successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create drug order: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create drug order',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update drug order status
     * PUT /api/drug-orders/{id}
     */
    public function updateDrugOrderStatus(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|in:pending,confirmed,shipped,delivered,cancelled',
                'delivery_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $order = $this->pharmacyService->updateDrugOrder($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Drug order updated successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update drug order {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update drug order',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get low stock drugs
     * GET /api/pharmacies/{pharmacy_id}/low-stock
     */
    public function getLowStockDrugs($pharmacyId): JsonResponse
    {
        try {
            $drugs = $this->pharmacyService->getLowStockDrugs($pharmacyId);

            return response()->json([
                'status' => 'success',
                'data' => $drugs,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch low stock drugs for pharmacy {$pharmacyId}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch low stock drugs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
