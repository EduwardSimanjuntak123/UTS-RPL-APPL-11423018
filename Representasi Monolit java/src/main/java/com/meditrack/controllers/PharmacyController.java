package com.meditrack.controllers;

import com.meditrack.services.*;

/**
 * PHARMACY CONTROLLER - HTTP Endpoints
 */
public class PharmacyController {
    private PharmacyService pharmacyService;
    
    public PharmacyController() {
        this.pharmacyService = PharmacyService.getInstance();
    }
    
    // POST /api/pharmacy/orders
    public boolean createOrder(int patientId, java.util.List<String> drugs, double amount) {
        System.out.println("POST /api/pharmacy/orders");
        return pharmacyService.createPharmacyOrder(patientId, drugs, amount);
    }
    
    // GET /api/pharmacy/inventory
    public java.util.List<java.util.Map<String, Object>> getDrugInventory() {
        System.out.println("GET /api/pharmacy/inventory");
        return pharmacyService.getDrugInventory();
    }
    
    // GET /api/users/{userId}/pharmacy-orders
    public java.util.Map<String, Object> getPatientOrders(int patientId) {
        System.out.println("GET /api/users/" + patientId + "/pharmacy-orders");
        return pharmacyService.getPatientOrders(patientId);
    }
}
