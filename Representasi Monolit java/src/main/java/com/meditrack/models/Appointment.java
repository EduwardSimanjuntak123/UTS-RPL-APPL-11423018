package com.meditrack.models;

import java.time.LocalDateTime;

/**
 * SHARED APPOINTMENT MODEL
 */
public class Appointment {
    private int id;
    private int patientId;
    private int doctorId;
    private LocalDateTime appointmentDate;
    private String type;
    private String location;
    private int duration;
    private String description;
    private String status;
    private LocalDateTime createdAt;
    
    public Appointment() {}
    
    public Appointment(int patientId, int doctorId, LocalDateTime appointmentDate) {
        this.patientId = patientId;
        this.doctorId = doctorId;
        this.appointmentDate = appointmentDate;
        this.status = "scheduled";
        this.createdAt = LocalDateTime.now();
    }
    
    // Getters & Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    
    public int getPatientId() { return patientId; }
    public void setPatientId(int patientId) { this.patientId = patientId; }
    
    public int getDoctorId() { return doctorId; }
    public void setDoctorId(int doctorId) { this.doctorId = doctorId; }
    
    public LocalDateTime getAppointmentDate() { return appointmentDate; }
    public void setAppointmentDate(LocalDateTime appointmentDate) { this.appointmentDate = appointmentDate; }
    
    public String getType() { return type; }
    public void setType(String type) { this.type = type; }
    
    public String getLocation() { return location; }
    public void setLocation(String location) { this.location = location; }
    
    public int getDuration() { return duration; }
    public void setDuration(int duration) { this.duration = duration; }
    
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    
    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }
    
    @Override
    public String toString() {
        return String.format("Appointment{id=%d, patient=%d, doctor=%d, date=%s, status='%s'}", 
            id, patientId, doctorId, appointmentDate, status);
    }
}
