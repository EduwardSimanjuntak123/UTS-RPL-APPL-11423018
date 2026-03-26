package seeders

import (
	"log"
	"time"

	"github.com/google/uuid"
	"gorm.io/gorm"
)

type Appointment struct {
	ID              string `gorm:"primaryKey"`
	PatientID       string
	DoctorID        string
	AppointmentDate int64
	Type            string
	Description     string
	Status          string
	Duration        int
	Location        string
	Notes           string
	CreatedAt       int64
	UpdatedAt       int64
}

type MedicalRecord struct {
	ID            string `gorm:"primaryKey"`
	PatientID     string
	DoctorID      string
	AppointmentID string
	Diagnosis     string
	Treatment     string
	LabResults    string
	Medications   string
	FollowUpDate  int64
	Notes         string
	CreatedAt     int64
	UpdatedAt     int64
}

// Seed appointments and medical records
func SeedAppointments(db *gorm.DB) error {
	log.Println("🌱 Starting appointment seeding...")

	if err := db.AutoMigrate(&Appointment{}, &MedicalRecord{}); err != nil {
		log.Printf("❌ Migration failed: %v\n", err)
		return err
	}

	now := time.Now().Unix()
	futureDate := time.Now().AddDate(0, 0, 7).Unix()

	// Sample data for doctors and patients
	doctorIDs := []string{
		"doc-001", "doc-002", "doc-003", "doc-004", "doc-005",
	}
	patientIDs := []string{
		"pat-001", "pat-002", "pat-003", "pat-004", "pat-005",
		"pat-006", "pat-007", "pat-008", "pat-009", "pat-010",
	}

	appointmentTypes := []string{"consultation", "follow-up", "general-checkup", "emergency"}
	statuses := []string{"scheduled", "completed", "cancelled", "no-show"}
	locations := []string{"Ruang Pemeriksaan A", "Ruang Pemeriksaan B", "Ruang Pemeriksaan C"}

	diagnoses := []string{
		"Demam Biasa",
		"Sakit Gigi",
		"Kolesterol Tinggi",
		"Hipertensi",
		"Diabetes Tipe 2",
		"Asma",
		"GERD",
		"Migren",
	}

	medications := []string{
		"Paracetamol 500mg",
		"Ibuprofen 400mg",
		"Amoxicillin 500mg",
		"Metformin 500mg",
		"Lisinopril 10mg",
		"Omeprazol 20mg",
		"Ventolin inhaler",
		"Fluticasone spray",
	}

	// Create 20 appointments
	for i := 1; i <= 20; i++ {
		appointment := Appointment{
			ID:              uuid.New().String(),
			PatientID:       patientIDs[(i-1)%len(patientIDs)],
			DoctorID:        doctorIDs[(i-1)%len(doctorIDs)],
			AppointmentDate: futureDate + int64((i-1)*86400),
			Type:            appointmentTypes[(i-1)%len(appointmentTypes)],
			Description:     "Pemeriksaan rutin",
			Status:          statuses[(i-1)%len(statuses)],
			Duration:        30 + ((i-1) % 3 * 15),
			Location:        locations[(i-1)%len(locations)],
			Notes:           "",
			CreatedAt:       now,
			UpdatedAt:       now,
		}

		if err := db.Create(&appointment).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create appointment: %v\n", err)
		} else {
			log.Printf("✅ Created appointment: %s\n", appointment.ID)
		}

		// Create medical record if appointment is completed
		if appointment.Status == "completed" {
			medicalRecord := MedicalRecord{
				ID:            uuid.New().String(),
				PatientID:     appointment.PatientID,
				DoctorID:      appointment.DoctorID,
				AppointmentID: appointment.ID,
				Diagnosis:     diagnoses[(i-1)%len(diagnoses)],
				Treatment:     "Resep obat dan istirahat",
				LabResults:    "Normal",
				Medications:   medications[(i-1)%len(medications)],
				FollowUpDate:  now + 604800, // 7 days later
				Notes:         "Pasien diminta kontrol ulang setelah 1 minggu",
				CreatedAt:     now,
				UpdatedAt:     now,
			}

			if err := db.Create(&medicalRecord).Error; err != nil {
				log.Printf("⚠️  Warning: Could not create medical record: %v\n", err)
			} else {
				log.Printf("✅ Created medical record: %s\n", medicalRecord.ID)
			}
		}
	}

	log.Println("✅ Appointment seeding completed successfully!")
	return nil
}
