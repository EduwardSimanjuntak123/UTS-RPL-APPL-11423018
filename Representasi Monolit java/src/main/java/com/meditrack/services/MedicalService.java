package com.meditrack.services;

import com.meditrack.database.DatabaseConnection;
import com.meditrack.models.MedicalRecord;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.*;

/**
 * MEDICAL SERVICE - Monolithic Pattern
 * TIGHTLY COUPLED dengan UserService
 */
public class MedicalService {
    private static MedicalService instance;
    private DatabaseConnection db;
    private UserService userService; // Direct dependency
    
    private MedicalService() {
        this.db = DatabaseConnection.getInstance();
        this.userService = UserService.getInstance();
    }
    
    public static synchronized MedicalService getInstance() {
        if (instance == null) {
            instance = new MedicalService();
        }
        return instance;
    }
    
    /**
     * CREATE MEDICAL RECORD - dengan user validation
     */
    public boolean createMedicalRecord(MedicalRecord record) {
        System.out.println("🏥 Creating medical record (Monolithic: validating user)");
        
        // DIRECT CALL ke UserService
        if (userService.getUserById(record.getPatientId()) == null) {
            System.err.println("✗ Patient not found");
            return false;
        }
        
        if (userService.getUserById(record.getDoctorId()) == null) {
            System.err.println("✗ Doctor not found");
            return false;
        }
        
        String sql = "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, created_at) " +
                     "VALUES (?, ?, ?, ?, ?)";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, record.getPatientId());
            stmt.setInt(2, record.getDoctorId());
            stmt.setString(3, record.getDiagnosis());
            stmt.setString(4, record.getNotes());
            stmt.setObject(5, LocalDateTime.now());
            
            stmt.executeUpdate();
            stmt.close();
            
            System.out.println("✓ Medical record created");
            return true;
        } catch (SQLException e) {
            System.err.println("Error creating medical record: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * GET PATIENT HISTORY - dengan user data
     */
    public Map<String, Object> getPatientMedicalHistory(int patientId) {
        System.out.println("📄 Fetching medical history (Monolithic: fetching from same DB)");
        
        Map<String, Object> history = new HashMap<>();
        
        // Get user info (DIRECT CALL)
        history.put("patient", userService.getUserById(patientId));
        
        // Get medical records
        String sql = "SELECT * FROM medical_records WHERE patient_id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, patientId);
            ResultSet rs = stmt.executeQuery();
            
            List<MedicalRecord> records = new ArrayList<>();
            while (rs.next()) {
                MedicalRecord record = new MedicalRecord();
                record.setId(rs.getInt("id"));
                record.setPatientId(rs.getInt("patient_id"));
                record.setDiagnosis(rs.getString("diagnosis"));
                records.add(record);
            }
            
            history.put("records", records);
        } catch (SQLException e) {
            System.err.println("Error fetching medical records: " + e.getMessage());
        }
        
        return history;
    }
    
    public MedicalRecord getMedicalRecordById(int id) {
        String sql = "SELECT * FROM medical_records WHERE id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                MedicalRecord record = new MedicalRecord();
                record.setId(rs.getInt("id"));
                record.setPatientId(rs.getInt("patient_id"));
                record.setDiagnosis(rs.getString("diagnosis"));
                return record;
            }
        } catch (SQLException e) {
            System.err.println("Error fetching medical record: " + e.getMessage());
        }
        return null;
    }
    
    public List<MedicalRecord> getPatientRecords(int patientId) {
        List<MedicalRecord> records = new ArrayList<>();
        String sql = "SELECT * FROM medical_records WHERE patient_id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, patientId);
            ResultSet rs = stmt.executeQuery();
            
            while (rs.next()) {
                MedicalRecord record = new MedicalRecord();
                record.setId(rs.getInt("id"));
                record.setPatientId(rs.getInt("patient_id"));
                record.setDiagnosis(rs.getString("diagnosis"));
                records.add(record);
            }
        } catch (SQLException e) {
            System.err.println("Error fetching patient records: " + e.getMessage());
        }
        return records;
    }
}
