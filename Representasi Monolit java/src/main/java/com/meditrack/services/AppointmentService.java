package com.meditrack.services;

import com.meditrack.database.DatabaseConnection;
import com.meditrack.models.Appointment;
import com.meditrack.models.User;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.*;

/**
 * APPOINTMENT SERVICE - Monolithic Pattern
 * TIGHTLY COUPLED dengan UserService dan PaymentService
 * Memanggil method dari service lain secara langsung
 */
public class AppointmentService {
    private static AppointmentService instance;
    private DatabaseConnection db;
    private UserService userService; // Direct dependency
    private PaymentService paymentService; // Direct dependency
    
    private AppointmentService() {
        this.db = DatabaseConnection.getInstance();
        // TIGHT COUPLING: Service saling bergantung satu sama lain
        this.userService = UserService.getInstance();
        this.paymentService = PaymentService.getInstance();
    }
    
    public static synchronized AppointmentService getInstance() {
        if (instance == null) {
            instance = new AppointmentService();
        }
        return instance;
    }
    
    /**
     * CREATE APPOINTMENT - dengan multiple dependencies
     * Demonstrasi ketergantungan erat antar modul
     */
    public boolean createAppointment(Appointment appointment) {
        System.out.println("📋 Creating appointment (Monolithic: checking user + payment)");
        
        // Verifikasi user ada (DIRECT CALL ke UserService)
        User patient = userService.getUserById(appointment.getPatientId());
        if (patient == null) {
            System.err.println("✗ Patient not found");
            return false;
        }
        
        User doctor = userService.getUserById(appointment.getDoctorId());
        if (doctor == null) {
            System.err.println("✗ Doctor not found");
            return false;
        }
        
        // Cek payment status (DIRECT CALL ke PaymentService)
        if (!paymentService.isUserPaymentUpToDate(appointment.getPatientId())) {
            System.err.println("✗ User has pending payments");
            return false;
        }
        
        // Insert ke database yang SAMA
        String sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, created_at) " +
                     "VALUES (?, ?, ?, ?, ?)";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, appointment.getPatientId());
            stmt.setInt(2, appointment.getDoctorId());
            stmt.setObject(3, appointment.getAppointmentDate());
            stmt.setString(4, "scheduled");
            stmt.setObject(5, LocalDateTime.now());
            
            stmt.executeUpdate();
            stmt.close();
            
            System.out.println("✓ Appointment created (dengan validasi dari UserService & PaymentService)");
            return true;
        } catch (SQLException e) {
            System.err.println("Error creating appointment: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * CASCADING UPDATES - Monolithic Problem
     * Perubahan di appointment service mempengaruhi service lain
     */
    public boolean cancelAppointment(int appointmentId) {
        System.out.println("❌ Cancelling appointment (Monolithic: cascading to payment)");
        
        String sql = "UPDATE appointments SET status = ? WHERE id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setString(1, "cancelled");
            stmt.setInt(2, appointmentId);
            stmt.executeUpdate();
            stmt.close();
            
            // LANGSUNG AFFECT PAYMENT SERVICE
            paymentService.refundAppointmentPayment(appointmentId);
            
            return true;
        } catch (SQLException e) {
            System.err.println("Error cancelling appointment: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * SHARED DATA LOCK - Monolithic Problem
     * Database transaction besar yang melibatkan multiple services
     */
    public void cancelUserAppointments(int userId) {
        System.out.println("🔒 Cancelling all appointments for user (Monolithic: large transaction)");
        
        String sql = "UPDATE appointments SET status = 'cancelled' WHERE patient_id = ? OR doctor_id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, userId);
            stmt.setInt(2, userId);
            stmt.executeUpdate();
            stmt.close();
        } catch (SQLException e) {
            System.err.println("Error cancelling user appointments: " + e.getMessage());
        }
    }
    
    public Appointment getAppointmentById(int id) {
        String sql = "SELECT * FROM appointments WHERE id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                Appointment appointment = new Appointment();
                appointment.setId(rs.getInt("id"));
                appointment.setPatientId(rs.getInt("patient_id"));
                appointment.setDoctorId(rs.getInt("doctor_id"));
                appointment.setStatus(rs.getString("status"));
                return appointment;
            }
        } catch (SQLException e) {
            System.err.println("Error fetching appointment: " + e.getMessage());
        }
        return null;
    }
    
    public List<Appointment> getUserAppointments(int userId) {
        List<Appointment> appointments = new ArrayList<>();
        String sql = "SELECT * FROM appointments WHERE patient_id = ? OR doctor_id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, userId);
            stmt.setInt(2, userId);
            ResultSet rs = stmt.executeQuery();
            
            while (rs.next()) {
                Appointment appointment = new Appointment();
                appointment.setId(rs.getInt("id"));
                appointment.setPatientId(rs.getInt("patient_id"));
                appointment.setDoctorId(rs.getInt("doctor_id"));
                appointments.add(appointment);
            }
        } catch (SQLException e) {
            System.err.println("Error fetching appointments: " + e.getMessage());
        }
        return appointments;
    }
}
