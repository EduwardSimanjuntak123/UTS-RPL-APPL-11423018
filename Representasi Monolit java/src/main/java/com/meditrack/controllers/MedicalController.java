package com.meditrack.controllers;

import com.meditrack.services.*;

/**
 * MEDICAL CONTROLLER - HTTP Endpoints
 */
public class MedicalController {
    private MedicalService medicalService;
    
    public MedicalController() {
        this.medicalService = MedicalService.getInstance();
    }
    
    // GET /api/users/{userId}/medical-history
    public java.util.Map<String, Object> getPatientMedicalHistory(int patientId) {
        System.out.println("GET /api/users/" + patientId + "/medical-history");
        return medicalService.getPatientMedicalHistory(patientId);
    }
    
    // GET /api/medical-records/{id}
    public Object getMedicalRecord(int id) {
        System.out.println("GET /api/medical-records/" + id);
        return medicalService.getMedicalRecordById(id);
    }
    
    // GET /api/users/{userId}/medical-records
    public java.util.List<?> getPatientRecords(int patientId) {
        System.out.println("GET /api/users/" + patientId + "/medical-records");
        return medicalService.getPatientRecords(patientId);
    }
}
