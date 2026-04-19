package com.meditrack.services;

import com.meditrack.database.DatabaseConnection;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.*;

/**
 * PHARMACY SERVICE - Monolithic Pattern
 * TIGHTLY COUPLED dengan UserService dan PaymentService
 */
public class PharmacyService {
    private static PharmacyService instance;
    private DatabaseConnection db;
    private UserService userService;
    private PaymentService paymentService;
    
    private PharmacyService() {
        this.db = DatabaseConnection.getInstance();
        this.userService = UserService.getInstance();
        this.paymentService = PaymentService.getInstance();
    }
    
    public static synchronized PharmacyService getInstance() {
        if (instance == null) {
            instance = new PharmacyService();
        }
        return instance;
    }
    
    /**
     * CREATE PHARMACY ORDER - dengan multiple dependencies
     */
    public boolean createPharmacyOrder(int patientId, List<String> drugs, double totalAmount) {
        System.out.println("💊 Creating pharmacy order (Monolithic: checking user + payment)");
        
        // DIRECT CALL ke UserService
        if (userService.getUserById(patientId) == null) {
            System.err.println("✗ Patient not found");
            return false;
        }
        
        // DIRECT CALL ke PaymentService
        if (!paymentService.isUserPaymentUpToDate(patientId)) {
            System.err.println("✗ User has pending payments");
            return false;
        }
        
        // Create payment record (TIGHT COUPLING)
        paymentService.createPayment(patientId, totalAmount, "Pharmacy Order");
        
        String sql = "INSERT INTO pharmacy_orders (patient_id, drugs, total_amount, status, created_at) " +
                     "VALUES (?, ?, ?, ?, ?)";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, patientId);
            stmt.setString(2, String.join(",", drugs));
            stmt.setDouble(3, totalAmount);
            stmt.setString(4, "pending");
            stmt.setObject(5, LocalDateTime.now());
            
            stmt.executeUpdate();
            stmt.close();
            
            System.out.println("✓ Pharmacy order created");
            return true;
        } catch (SQLException e) {
            System.err.println("Error creating pharmacy order: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * CHECK DRUG STOCK - SHARED TABLE
     * Multiple services could update stock causing race conditions
     */
    public boolean checkDrugStock(String drugName, int quantity) {
        System.out.println("  → Checking drug stock (shared table - potential race condition)");
        
        String sql = "SELECT stock FROM drug_inventory WHERE name = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setString(1, drugName);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                int availableStock = rs.getInt("stock");
                return availableStock >= quantity;
            }
        } catch (SQLException e) {
            System.err.println("Error checking drug stock: " + e.getMessage());
        }
        return false;
    }
    
    /**
     * UPDATE STOCK - RACE CONDITION
     * Monolithic apps sering mengalami race conditions pada shared resources
     */
    public void updateDrugStock(String drugName, int quantityUsed) {
        System.out.println("  ⚠ Updating stock (potential race condition in monolithic)");
        
        String sql = "UPDATE drug_inventory SET stock = stock - ? WHERE name = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, quantityUsed);
            stmt.setString(2, drugName);
            stmt.executeUpdate();
            stmt.close();
        } catch (SQLException e) {
            System.err.println("Error updating stock: " + e.getMessage());
        }
    }
    
    public List<Map<String, Object>> getDrugInventory() {
        List<Map<String, Object>> inventory = new ArrayList<>();
        String sql = "SELECT * FROM drug_inventory";
        
        try {
            Statement stmt = db.getConnection().createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            
            while (rs.next()) {
                Map<String, Object> drug = new HashMap<>();
                drug.put("id", rs.getInt("id"));
                drug.put("name", rs.getString("name"));
                drug.put("stock", rs.getInt("stock"));
                inventory.add(drug);
            }
        } catch (SQLException e) {
            System.err.println("Error fetching inventory: " + e.getMessage());
        }
        
        return inventory;
    }
    
    public Map<String, Object> getPatientOrders(int patientId) {
        Map<String, Object> orders = new HashMap<>();
        
        // Get user info (DIRECT CALL)
        orders.put("patient", userService.getUserById(patientId));
        
        String sql = "SELECT * FROM pharmacy_orders WHERE patient_id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, patientId);
            ResultSet rs = stmt.executeQuery();
            
            List<Map<String, Object>> orderList = new ArrayList<>();
            while (rs.next()) {
                Map<String, Object> order = new HashMap<>();
                order.put("id", rs.getInt("id"));
                order.put("drugs", rs.getString("drugs"));
                order.put("amount", rs.getDouble("total_amount"));
                orderList.add(order);
            }
            
            orders.put("orders", orderList);
        } catch (SQLException e) {
            System.err.println("Error fetching patient orders: " + e.getMessage());
        }
        
        return orders;
    }
}
