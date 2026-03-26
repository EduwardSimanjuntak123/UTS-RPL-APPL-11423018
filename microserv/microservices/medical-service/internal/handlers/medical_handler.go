package handlers

import (
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/medical-service/internal/db"
	"github.com/meditrack/medical-service/internal/models"
)

// CreateMedicalRecord creates a new medical record
func CreateMedicalRecord(c *gin.Context) {
	var req models.CreateMedicalRecordRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO medical_records (patient_id, doctor_id, record_type, diagnosis, treatment, notes, is_confidential)
		 VALUES (?, ?, ?, ?, ?, ?, ?)`,
		req.PatientID, req.DoctorID, req.RecordType, req.Diagnosis, req.Treatment, req.Notes, req.IsConfidential,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create medical record"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Medical record created successfully",
		"id":      id,
	})
}

// GetPatientMedicalRecords gets all medical records for a patient
func GetPatientMedicalRecords(c *gin.Context) {
	patientID, _ := strconv.Atoi(c.Param("patient_id"))

	rows, err := db.DB.Query(
		`SELECT id, patient_id, doctor_id, record_type, diagnosis, treatment, notes, attachment, is_confidential, created_at, updated_at
		 FROM medical_records WHERE patient_id = ? ORDER BY created_at DESC`, patientID,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch records"})
		return
	}
	defer rows.Close()

	var records []models.MedicalRecord
	for rows.Next() {
		var record models.MedicalRecord
		rows.Scan(&record.ID, &record.PatientID, &record.DoctorID, &record.RecordType, &record.Diagnosis,
			&record.Treatment, &record.Notes, &record.Attachment, &record.IsConfidential, &record.CreatedAt, &record.UpdatedAt)
		records = append(records, record)
	}

	c.JSON(http.StatusOK, records)
}

// CreatePrescription creates a new prescription
func CreatePrescription(c *gin.Context) {
	var req models.CreatePrescriptionRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO prescriptions (medical_record_id, patient_id, doctor_id, drug_name, dosage, frequency, duration, quantity, instructions, status)
		 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')`,
		req.MedicalRecordID, req.PatientID, req.DoctorID, req.DrugName, req.Dosage, req.Frequency, req.Duration, req.Quantity, req.Instructions,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create prescription"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Prescription created successfully",
		"id":      id,
	})
}

// GetPatientPrescriptions gets all prescriptions for a patient
func GetPatientPrescriptions(c *gin.Context) {
	patientID, _ := strconv.Atoi(c.Param("patient_id"))

	rows, err := db.DB.Query(
		`SELECT id, medical_record_id, patient_id, doctor_id, drug_name, dosage, frequency, duration, quantity, instructions, status, created_at, updated_at
		 FROM prescriptions WHERE patient_id = ? ORDER BY created_at DESC`, patientID,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch prescriptions"})
		return
	}
	defer rows.Close()

	var prescriptions []models.Prescription
	for rows.Next() {
		var p models.Prescription
		rows.Scan(&p.ID, &p.MedicalRecordID, &p.PatientID, &p.DoctorID, &p.DrugName, &p.Dosage, &p.Frequency,
			&p.Duration, &p.Quantity, &p.Instructions, &p.Status, &p.CreatedAt, &p.UpdatedAt)
		prescriptions = append(prescriptions, p)
	}

	c.JSON(http.StatusOK, prescriptions)
}

// CreateLabResult creates a new lab result
func CreateLabResult(c *gin.Context) {
	var req struct {
		PatientID      int    `json:"patient_id" binding:"required"`
		DoctorID       int    `json:"doctor_id" binding:"required"`
		MedicalRecordID *int  `json:"medical_record_id"`
		TestName       string `json:"test_name" binding:"required"`
		Result         string `json:"result" binding:"required"`
		Unit           string `json:"unit"`
		ReferenceRange string `json:"reference_range"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO lab_results (patient_id, doctor_id, medical_record_id, test_name, result, unit, reference_range, status)
		 VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')`,
		req.PatientID, req.DoctorID, req.MedicalRecordID, req.TestName, req.Result, req.Unit, req.ReferenceRange,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create lab result"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Lab result created successfully",
		"id":      id,
	})
}

// HealthCheck checks service health
func HealthCheck(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{
		"status":  "healthy",
		"service": "medical-service",
		"time":    time.Now(),
	})
}
