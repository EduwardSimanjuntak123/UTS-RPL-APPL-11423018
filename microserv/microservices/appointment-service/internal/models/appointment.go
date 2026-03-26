package models

import "time"

type Appointment struct {
	ID              int       `json:"id"`
	PatientID       int       `json:"patient_id" binding:"required"`
	DoctorID        int       `json:"doctor_id" binding:"required"`
	AppointmentDate time.Time `json:"appointment_date" binding:"required"`
	Status          string    `json:"status"`
	Type            string    `json:"type"`
	Location        string    `json:"location"`
	Duration        int       `json:"duration"`
	Description     string    `json:"description"`
	Notes           string    `json:"notes"`
	CreatedAt       time.Time `json:"created_at"`
	UpdatedAt       time.Time `json:"updated_at"`
}

type CreateAppointmentRequest struct {
	PatientID       int       `json:"patient_id" binding:"required"`
	DoctorID        int       `json:"doctor_id" binding:"required"`
	AppointmentDate time.Time `json:"appointment_date" binding:"required"`
	Type            string    `json:"type" binding:"required"`
	Location        string    `json:"location" binding:"required"`
	Duration        int       `json:"duration" binding:"required"`
	Description     string    `json:"description"`
}

type UpdateAppointmentRequest struct {
	AppointmentDate time.Time `json:"appointment_date"`
	Status          string    `json:"status"`
	Notes           string    `json:"notes"`
}

type AppointmentSlot struct {
	ID           int       `json:"id"`
	DoctorID     int       `json:"doctor_id"`
	StartTime    time.Time `json:"start_time"`
	EndTime      time.Time `json:"end_time"`
	IsAvailable  bool      `json:"is_available"`
	BookedByID   *int      `json:"booked_by_id"`
	CreatedAt    time.Time `json:"created_at"`
}

type DoctorAvailability struct {
	DoctorID      int              `json:"doctor_id"`
	AvailableSlots []AppointmentSlot `json:"available_slots"`
}
