package seeders

import (
	"log"
	"time"

	"github.com/google/uuid"
	"gorm.io/gorm"
)

type Prescription struct {
	ID              string `gorm:"primaryKey"`
	PatientID       string
	DoctorID        string
	AppointmentID   string
	Medication      string
	Dosage          string
	Frequency       string
	Duration        int
	Instructions    string
	Status          string
	IssueDate       int64
	ExpiryDate      int64
	PharmacyID      string
	CreatedAt       int64
	UpdatedAt       int64
}

type LabResult struct {
	ID            string `gorm:"primaryKey"`
	PatientID     string
	DoctorID      string
	TestName      string
	TestType      string
	Result        string
	Unit          string
	ReferenceRange string
	Status        string
	TestDate      int64
	CreatedAt     int64
	UpdatedAt     int64
}

type ClinicalNote struct {
	ID            string `gorm:"primaryKey"`
	PatientID     string
	DoctorID      string
	AppointmentID string
	NoteContent   string
	Findings      string
	Assessments   string
	Plans         string
	Status        string
	CreatedAt     int64
	UpdatedAt     int64
}

// Seed medical data
func SeedMedical(db *gorm.DB) error {
	log.Println("🌱 Starting medical seeding...")

	if err := db.AutoMigrate(&Prescription{}, &LabResult{}, &ClinicalNote{}); err != nil {
		log.Printf("❌ Migration failed: %v\n", err)
		return err
	}

	now := time.Now().Unix()
	expiryDate := time.Now().AddDate(0, 3, 0).Unix() // 3 months from now

	patientIDs := []string{
		"pat-001", "pat-002", "pat-003", "pat-004", "pat-005",
		"pat-006", "pat-007", "pat-008", "pat-009", "pat-010",
	}

	doctorIDs := []string{
		"doc-001", "doc-002", "doc-003", "doc-004", "doc-005",
	}

	// 1. Create Prescriptions
	medications := []string{
		"Paracetamol", "Ibuprofen", "Amoxicillin", "Metformin",
		"Lisinopril", "Omeprazol", "Cetirizine", "Vitamin C",
	}

	dosages := []string{
		"500mg", "400mg", "500mg", "500mg",
		"10mg", "20mg", "10mg", "1000mg",
	}

	frequencies := []string{
		"1x sehari", "2x sehari", "3x sehari", "4x sehari",
	}

	prescriptionStatuses := []string{"active", "inactive", "refilled", "expired"}

	for i := 1; i <= 20; i++ {
		prescription := Prescription{
			ID:            uuid.New().String(),
			PatientID:     patientIDs[(i-1)%len(patientIDs)],
			DoctorID:      doctorIDs[(i-1)%len(doctorIDs)],
			AppointmentID: uuid.New().String(),
			Medication:    medications[(i-1)%len(medications)],
			Dosage:        dosages[(i-1)%len(dosages)],
			Frequency:     frequencies[(i-1)%len(frequencies)],
			Duration:      7 + ((i-1) % 3 * 7),
			Instructions:  "Diminum setelah makan",
			Status:        prescriptionStatuses[(i-1)%len(prescriptionStatuses)],
			IssueDate:     now - int64((i-1)*86400),
			ExpiryDate:    expiryDate - int64((i-1)*86400),
			PharmacyID:    "pharm-" + string(rune('0'+(i%3))),
			CreatedAt:     now - int64((i-1)*86400),
			UpdatedAt:     now - int64((i-1)*86400),
		}

		if err := db.Create(&prescription).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create prescription: %v\n", err)
		} else {
			log.Printf("✅ Created prescription: %s\n", prescription.ID)
		}
	}

	// 2. Create Lab Results
	testNames := []string{
		"Complete Blood Count", "Metabolic Panel", "Lipid Panel",
		"Urinalysis", "Blood Glucose", "Thyroid Function",
		"Liver Function", "Kidney Function",
	}

	testTypes := []string{
		"Blood", "Urine", "Saliva", "Tissue",
	}

	labStatuses := []string{"pending", "completed", "reviewed"}

	for i := 1; i <= 15; i++ {
		labResult := LabResult{
			ID:            uuid.New().String(),
			PatientID:     patientIDs[(i-1)%len(patientIDs)],
			DoctorID:      doctorIDs[(i-1)%len(doctorIDs)],
			TestName:      testNames[(i-1)%len(testNames)],
			TestType:      testTypes[(i-1)%len(testTypes)],
			Result:        []string{"Normal", "Abnormal", "Critical"}[(i-1)%3],
			Unit:          []string{"mg/dL", "g/dL", "µmol/L", "mmol/L"}[(i-1)%4],
			ReferenceRange: "70-100 mg/dL",
			Status:        labStatuses[(i-1)%len(labStatuses)],
			TestDate:      now - int64((i-1)*86400),
			CreatedAt:     now - int64((i-1)*86400),
			UpdatedAt:     now - int64((i-1)*86400),
		}

		if err := db.Create(&labResult).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create lab result: %v\n", err)
		} else {
			log.Printf("✅ Created lab result: %s\n", labResult.ID)
		}
	}

	// 3. Create Clinical Notes
	noteStatuses := []string{"draft", "finalized", "archived"}

	for i := 1; i <= 12; i++ {
		clinicalNote := ClinicalNote{
			ID:            uuid.New().String(),
			PatientID:     patientIDs[(i-1)%len(patientIDs)],
			DoctorID:      doctorIDs[(i-1)%len(doctorIDs)],
			AppointmentID: uuid.New().String(),
			NoteContent:   "Pasien datang dengan keluhan sakit kepala yang hilang timbul selama 3 hari terakhir.",
			Findings:      "Tekanan darah normal, suhu tubuh 36.5°C, tidak ada kelainan fisik yang signifikan.",
			Assessments:   "Migren episodik, perlu monitoring dan pemberian terapi suportif.",
			Plans:         "Resep obat analgesik, istirahat cukup, kontrol kembali dalam 1 minggu.",
			Status:        noteStatuses[(i-1)%len(noteStatuses)],
			CreatedAt:     now - int64((i-1)*86400),
			UpdatedAt:     now - int64((i-1)*86400),
		}

		if err := db.Create(&clinicalNote).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create clinical note: %v\n", err)
		} else {
			log.Printf("✅ Created clinical note: %s\n", clinicalNote.ID)
		}
	}

	log.Println("✅ Medical seeding completed successfully!")
	return nil
}
