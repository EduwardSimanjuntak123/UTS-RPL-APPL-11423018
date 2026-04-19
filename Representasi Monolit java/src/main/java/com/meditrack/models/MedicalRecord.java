package com.meditrack.models;

import java.time.LocalDateTime;

/**
 * SHARED MEDICAL RECORD MODEL
 */
public class MedicalRecord {
    private int id;
    private int patientId;
    private int doctorId;
    private String diagnosis;
    private String treatment;
    private String notes;
    private LocalDateTime recordDate;
    private LocalDateTime createdAt;
    
    public MedicalRecord() {}
    
    public MedicalRecord(int patientId, int doctorId, String diagnosis) {
        this.patientId = patientId;
        this.doctorId = doctorId;
        this.diagnosis = diagnosis;
        this.recordDate = LocalDateTime.now();
        this.createdAt = LocalDateTime.now();
    }
    
    // Getters & Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    
    public int getPatientId() { return patientId; }
    public void setPatientId(int patientId) { this.patientId = patientId; }
    
    public int getDoctorId() { return doctorId; }
    public void setDoctorId(int doctorId) { this.doctorId = doctorId; }
    
    public String getDiagnosis() { return diagnosis; }
    public void setDiagnosis(String diagnosis) { this.diagnosis = diagnosis; }
    
    public String getTreatment() { return treatment; }
    public void setTreatment(String treatment) { this.treatment = treatment; }
    
    public String getNotes() { return notes; }
    public void setNotes(String notes) { this.notes = notes; }
    
    public LocalDateTime getRecordDate() { return recordDate; }
    public void setRecordDate(LocalDateTime recordDate) { this.recordDate = recordDate; }
    
    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }
}
