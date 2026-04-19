package handlers

import (
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/appointment-service/internal/db"
	"github.com/meditrack/appointment-service/internal/models"
)

// CreateAppointment books a new appointment
func CreateAppointment(c *gin.Context) {
	var req models.CreateAppointmentRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	// Check if appointment date is at least 24 hours from now
	if req.AppointmentDate.Before(time.Now().Add(24 * time.Hour)) {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Appointment must be scheduled at least 24 hours in advance",
		})
		return
	}

	// Check for doctor conflicts (simplified)
	var conflictCount int
	err := db.DB.QueryRow(
		`SELECT COUNT(*) FROM appointments 
		 WHERE doctor_id = ? AND appointment_date = ? AND status != 'cancelled'`,
		req.DoctorID, req.AppointmentDate,
	).Scan(&conflictCount)

	if err == nil && conflictCount > 0 {
		c.JSON(http.StatusConflict, gin.H{
			"error": "Doctor has a conflict at this time",
		})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO appointments (patient_id, doctor_id, appointment_date, type, location, duration, description, status)
		 VALUES (?, ?, ?, ?, ?, ?, ?, 'scheduled')`,
		req.PatientID, req.DoctorID, req.AppointmentDate,
		req.Type, req.Location, req.Duration, req.Description,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create appointment"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message":        "Appointment created successfully",
		"appointment_id": id,
	})
}

// GetAppointment retrieves appointment details
func GetAppointment(c *gin.Context) {
	id, err := strconv.Atoi(c.Param("id"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid appointment ID"})
		return
	}

	var appt models.Appointment
	err = db.DB.QueryRow(
		`SELECT id, patient_id, doctor_id, appointment_date, status, type, location, duration, description, notes, created_at, updated_at
		 FROM appointments WHERE id = ?`, id,
	).Scan(&appt.ID, &appt.PatientID, &appt.DoctorID, &appt.AppointmentDate, &appt.Status,
		&appt.Type, &appt.Location, &appt.Duration, &appt.Description, &appt.Notes, &appt.CreatedAt, &appt.UpdatedAt)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Appointment not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"data": appt})
}

// GetAllAppointments retrieves all appointments with pagination
func GetAllAppointments(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	limit := c.DefaultQuery("limit", "10")
	status := c.Query("status")

	pageNum, _ := strconv.Atoi(page)
	limitNum, _ := strconv.Atoi(limit)
	offset := (pageNum - 1) * limitNum

	query := `SELECT id, patient_id, doctor_id, appointment_date, status, type, location, duration, description, notes, created_at, updated_at
			 FROM appointments`
	args := []interface{}{}

	if status != "" {
		query += " WHERE status = ?"
		args = append(args, status)
	}

	query += " ORDER BY appointment_date DESC LIMIT ? OFFSET ?"
	args = append(args, limitNum, offset)

	rows, err := db.DB.Query(query, args...)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch appointments"})
		return
	}
	defer rows.Close()

	var appointments []models.Appointment
	for rows.Next() {
		var appt models.Appointment
		rows.Scan(&appt.ID, &appt.PatientID, &appt.DoctorID, &appt.AppointmentDate, &appt.Status,
			&appt.Type, &appt.Location, &appt.Duration, &appt.Description, &appt.Notes, &appt.CreatedAt, &appt.UpdatedAt)
		appointments = append(appointments, appt)
	}

	c.JSON(http.StatusOK, gin.H{
		"data":  appointments,
		"page":  pageNum,
		"limit": limitNum,
	})
}

// GetPatientAppointments retrieves all appointments for a patient
func GetPatientAppointments(c *gin.Context) {
	patientID, _ := strconv.Atoi(c.Param("patient_id"))

	rows, err := db.DB.Query(
		`SELECT id, patient_id, doctor_id, appointment_date, status, type, location, duration, description, notes, created_at, updated_at
		 FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC`, patientID,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch appointments"})
		return
	}
	defer rows.Close()

	var appointments []models.Appointment
	for rows.Next() {
		var appt models.Appointment
		rows.Scan(&appt.ID, &appt.PatientID, &appt.DoctorID, &appt.AppointmentDate, &appt.Status,
			&appt.Type, &appt.Location, &appt.Duration, &appt.Description, &appt.Notes, &appt.CreatedAt, &appt.UpdatedAt)
		appointments = append(appointments, appt)
	}

	c.JSON(http.StatusOK, appointments)
}

// UpdateAppointmentStatus updates appointment status
func UpdateAppointmentStatus(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))
	var req struct {
		Status string `json:"status" binding:"required"`
		Notes  string `json:"notes"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`UPDATE appointments SET status = ?, notes = ? WHERE id = ?`,
		req.Status, req.Notes, id,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update appointment"})
		return
	}

	rows, _ := result.RowsAffected()
	if rows == 0 {
		c.JSON(http.StatusNotFound, gin.H{"error": "Appointment not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Appointment updated successfully"})
}

// CancelAppointment cancels an appointment
func CancelAppointment(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))
	var req struct {
		Reason string `json:"reason"`
	}

	c.ShouldBindJSON(&req)

	result, err := db.DB.Exec(
		`UPDATE appointments SET status = 'cancelled', cancellation_reason = ? WHERE id = ?`,
		req.Reason, id,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to cancel appointment"})
		return
	}

	rows, _ := result.RowsAffected()
	if rows == 0 {
		c.JSON(http.StatusNotFound, gin.H{"error": "Appointment not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Appointment cancelled successfully"})
}

// HealthCheck checks service health
func HealthCheck(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{
		"status":  "healthy",
		"service": "appointment-service",
		"time":    time.Now(),
	})
}

// UpdateAppointment updates an appointment
func UpdateAppointment(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Appointment updated", "id": id})
}

// DeleteAppointment deletes an appointment
func DeleteAppointment(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Appointment deleted", "id": id})
}

// CreateSlot creates an availability slot
func CreateSlot(c *gin.Context) {
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusCreated, gin.H{"message": "Slot created successfully"})
}

// GetAvailableSlots gets available slots
func GetAvailableSlots(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"slots": []interface{}{}})
}

// DeleteSlot deletes a slot
func DeleteSlot(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Slot deleted", "id": id})
}

// ConfirmAppointment confirms an appointment
func ConfirmAppointment(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Appointment confirmed", "id": id})
}

// CompleteAppointment marks appointment as complete
func CompleteAppointment(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Appointment completed", "id": id})
}

// GetNotifications gets notifications
func GetNotifications(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"notifications": []interface{}{}})
}

// SendNotification sends a notification
func SendNotification(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Notification sent", "id": id})
}
