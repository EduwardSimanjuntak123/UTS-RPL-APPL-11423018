package com.meditrack.services;

import com.meditrack.database.DatabaseConnection;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.*;

/**
 * PAYMENT SERVICE - Monolithic Pattern
 * TIGHTLY COUPLED dengan AppointmentService, PharmacyService, UserService
 */
public class PaymentService {
    private static PaymentService instance;
    private DatabaseConnection db;
    private UserService userService;
    
    private PaymentService() {
        this.db = DatabaseConnection.getInstance();
        this.userService = UserService.getInstance();
    }
    
    public static synchronized PaymentService getInstance() {
        if (instance == null) {
            instance = new PaymentService();
        }
        return instance;
    }
    
    /**
     * CREATE PAYMENT - dengan user validation
     */
    public boolean createPayment(int userId, double amount, String description) {
        System.out.println("💳 Creating payment (Monolithic: validating user)");
        
        // DIRECT CALL ke UserService
        if (userService.getUserById(userId) == null) {
            System.err.println("✗ User not found");
            return false;
        }
        
        String sql = "INSERT INTO payments (user_id, amount, description, status, created_at) " +
                     "VALUES (?, ?, ?, ?, ?)";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, userId);
            stmt.setDouble(2, amount);
            stmt.setString(3, description);
            stmt.setString(4, "pending");
            stmt.setObject(5, LocalDateTime.now());
            
            stmt.executeUpdate();
            stmt.close();
            
            System.out.println("✓ Payment created");
            return true;
        } catch (SQLException e) {
            System.err.println("Error creating payment: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * CHECK PAYMENT STATUS - used by AppointmentService
     */
    public boolean isUserPaymentUpToDate(int userId) {
        System.out.println("  → Checking payment status (from AppointmentService)");
        
        String sql = "SELECT COUNT(*) as count FROM payments WHERE user_id = ? AND status = 'overdue'";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                int overdueCount = rs.getInt("count");
                return overdueCount == 0;
            }
        } catch (SQLException e) {
            System.err.println("Error checking payment status: " + e.getMessage());
        }
        return true;
    }
    
    /**
     * REFUND - called by AppointmentService
     */
    public void refundAppointmentPayment(int appointmentId) {
        System.out.println("  → Processing refund (called from AppointmentService)");
        
        String sql = "UPDATE payments SET status = 'refunded' WHERE appointment_id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, appointmentId);
            stmt.executeUpdate();
            stmt.close();
        } catch (SQLException e) {
            System.err.println("Error processing refund: " + e.getMessage());
        }
    }
    
    /**
     * CANCEL PAYMENTS - called by UserService
     */
    public void cancelUserPayments(int userId) {
        System.out.println("  → Cancelling user payments (called from UserService)");
        
        String sql = "UPDATE payments SET status = 'cancelled' WHERE user_id = ? AND status != 'completed'";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, userId);
            stmt.executeUpdate();
            stmt.close();
        } catch (SQLException e) {
            System.err.println("Error cancelling payments: " + e.getMessage());
        }
    }
    
    public double getUserTotalPayments(int userId) {
        String sql = "SELECT SUM(amount) as total FROM payments WHERE user_id = ? AND status = 'completed'";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                return rs.getDouble("total");
            }
        } catch (SQLException e) {
            System.err.println("Error getting user total payments: " + e.getMessage());
        }
        return 0.0;
    }
}
