package com.meditrack.services;

import com.meditrack.database.DatabaseConnection;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.*;

/**
 * ANALYTICS SERVICE - Monolithic Pattern
 * Mengakses data dari semua table via SHARED DATABASE
 * MASALAH: Perubahan schema di module lain bisa break analytics
 */
public class AnalyticsService {
    private static AnalyticsService instance;
    private DatabaseConnection db;
    
    private AnalyticsService() {
        this.db = DatabaseConnection.getInstance();
    }
    
    public static synchronized AnalyticsService getInstance() {
        if (instance == null) {
            instance = new AnalyticsService();
        }
        return instance;
    }
    
    /**
     * CROSS-MODULE ANALYTICS
     * Analytics service membaca dari banyak table yang berbeda
     * MASALAH: Tergantung pada struktur table dari service lain
     */
    public Map<String, Object> getDashboardMetrics() {
        System.out.println("📊 Generating dashboard (Monolithic: reading from all tables)");
        
        Map<String, Object> metrics = new HashMap<>();
        
        try {
            // Total users
            String userSql = "SELECT COUNT(*) as count FROM users";
            Statement userStmt = db.getConnection().createStatement();
            ResultSet userRs = userStmt.executeQuery(userSql);
            if (userRs.next()) {
                metrics.put("total_users", userRs.getInt("count"));
            }
            
            // Total appointments
            String appointmentSql = "SELECT COUNT(*) as count FROM appointments WHERE status != 'cancelled'";
            Statement appointmentStmt = db.getConnection().createStatement();
            ResultSet appointmentRs = appointmentStmt.executeQuery(appointmentSql);
            if (appointmentRs.next()) {
                metrics.put("active_appointments", appointmentRs.getInt("count"));
            }
            
            // Total payments
            String paymentSql = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed'";
            Statement paymentStmt = db.getConnection().createStatement();
            ResultSet paymentRs = paymentStmt.executeQuery(paymentSql);
            if (paymentRs.next()) {
                metrics.put("total_revenue", paymentRs.getDouble("total"));
            }
            
            // Drug inventory status
            String drugSql = "SELECT COUNT(*) as low_stock FROM drug_inventory WHERE stock < 10";
            Statement drugStmt = db.getConnection().createStatement();
            ResultSet drugRs = drugStmt.executeQuery(drugSql);
            if (drugRs.next()) {
                metrics.put("low_stock_items", drugRs.getInt("low_stock"));
            }
            
            System.out.println("✓ Dashboard metrics generated");
            return metrics;
            
        } catch (SQLException e) {
            System.err.println("Error generating metrics: " + e.getMessage());
            return metrics;
        }
    }
    
    /**
     * USER ACTIVITY ANALYTICS
     * MASALAH: Harus menggabung data dari multiple services
     */
    public Map<String, Object> getUserActivityReport(int userId) {
        System.out.println("👤 Generating user activity report (Monolithic: complex joins)");
        
        Map<String, Object> report = new HashMap<>();
        
        try {
            // User info
            String userSql = "SELECT * FROM users WHERE id = ?";
            PreparedStatement userStmt = db.getConnection().prepareStatement(userSql);
            userStmt.setInt(1, userId);
            ResultSet userRs = userStmt.executeQuery();
            if (userRs.next()) {
                report.put("user", userRs.getString("name"));
                report.put("email", userRs.getString("email"));
            }
            
            // Appointments count
            String appointSql = "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ? OR doctor_id = ?";
            PreparedStatement appointStmt = db.getConnection().prepareStatement(appointSql);
            appointStmt.setInt(1, userId);
            appointStmt.setInt(2, userId);
            ResultSet appointRs = appointStmt.executeQuery();
            if (appointRs.next()) {
                report.put("total_appointments", appointRs.getInt("count"));
            }
            
            // Medical records count
            String medicalSql = "SELECT COUNT(*) as count FROM medical_records WHERE patient_id = ? OR doctor_id = ?";
            PreparedStatement medicalStmt = db.getConnection().prepareStatement(medicalSql);
            medicalStmt.setInt(1, userId);
            medicalStmt.setInt(2, userId);
            ResultSet medicalRs = medicalStmt.executeQuery();
            if (medicalRs.next()) {
                report.put("medical_records", medicalRs.getInt("count"));
            }
            
            // Payments
            String paymentSql = "SELECT COUNT(*) as count, SUM(amount) as total FROM payments WHERE user_id = ?";
            PreparedStatement paymentStmt = db.getConnection().prepareStatement(paymentSql);
            paymentStmt.setInt(1, userId);
            ResultSet paymentRs = paymentStmt.executeQuery();
            if (paymentRs.next()) {
                report.put("total_payments", paymentRs.getInt("count"));
                report.put("amount_spent", paymentRs.getDouble("total"));
            }
            
            System.out.println("✓ Activity report generated");
            return report;
            
        } catch (SQLException e) {
            System.err.println("Error generating activity report: " + e.getMessage());
            return report;
        }
    }
    
    /**
     * SERVICE HEALTH CHECK
     * Monolithic: semua service dalam satu aplikasi, satu database
     */
    public Map<String, Object> getSystemHealth() {
        System.out.println("🔍 Checking system health (Monolithic: single database)");
        
        Map<String, Object> health = new HashMap<>();
        health.put("application", "RUNNING");
        health.put("database", "CONNECTED");
        health.put("architecture", "MONOLITHIC");
        health.put("scaling", "VERTICAL ONLY");
        health.put("failure_domain", "ENTIRE_APPLICATION");
        
        return health;
    }
}
