package models

import "time"

type MedicalRecord struct {
	ID          int       `json:"id"`
	PatientID   int       `json:"patient_id" binding:"required"`
	DoctorID    int       `json:"doctor_id" binding:"required"`
	RecordType  string    `json:"record_type"`
	Diagnosis   string    `json:"diagnosis" binding:"required"`
	Treatment   string    `json:"treatment"`
	Notes       string    `json:"notes"`
	Attachment  string    `json:"attachment"`
	IsConfidential bool   `json:"is_confidential"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}

type Prescription struct {
	ID             int       `json:"id"`
	MedicalRecordID int      `json:"medical_record_id" binding:"required"`
	PatientID      int       `json:"patient_id" binding:"required"`
	DoctorID       int       `json:"doctor_id" binding:"required"`
	DrugName       string    `json:"drug_name" binding:"required"`
	Dosage         string    `json:"dosage" binding:"required"`
	Frequency      string    `json:"frequency" binding:"required"`
	Duration       int       `json:"duration"`
	Quantity       int       `json:"quantity"`
	Instructions   string    `json:"instructions"`
	Status         string    `json:"status"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`
}

type LabResult struct {
	ID             int       `json:"id"`
	PatientID      int       `json:"patient_id" binding:"required"`
	DoctorID       int       `json:"doctor_id" binding:"required"`
	MedicalRecordID int      `json:"medical_record_id"`
	TestName       string    `json:"test_name" binding:"required"`
	Result         string    `json:"result" binding:"required"`
	Unit           string    `json:"unit"`
	ReferenceRange string    `json:"reference_range"`
	Status         string    `json:"status"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`
}

type ClinicalNote struct {
	ID            int       `json:"id"`
	PatientID     int       `json:"patient_id" binding:"required"`
	DoctorID      int       `json:"doctor_id" binding:"required"`
	AppointmentID int       `json:"appointment_id"`
	Note          string    `json:"note" binding:"required"`
	Vitals        string    `json:"vitals"`
	Symptoms      string    `json:"symptoms"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}

type CreateMedicalRecordRequest struct {
	PatientID      int    `json:"patient_id" binding:"required"`
	DoctorID       int    `json:"doctor_id" binding:"required"`
	RecordType     string `json:"record_type"`
	Diagnosis      string `json:"diagnosis" binding:"required"`
	Treatment      string `json:"treatment"`
	Notes          string `json:"notes"`
	IsConfidential bool   `json:"is_confidential"`
}

type CreatePrescriptionRequest struct {
	MedicalRecordID int    `json:"medical_record_id" binding:"required"`
	PatientID       int    `json:"patient_id" binding:"required"`
	DoctorID        int    `json:"doctor_id" binding:"required"`
	DrugName        string `json:"drug_name" binding:"required"`
	Dosage          string `json:"dosage" binding:"required"`
	Frequency       string `json:"frequency" binding:"required"`
	Duration        int    `json:"duration"`
	Quantity        int    `json:"quantity"`
	Instructions    string `json:"instructions"`
}
