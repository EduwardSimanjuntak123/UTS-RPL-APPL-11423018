package seeders

import (
	"log"
	"time"

	"github.com/google/uuid"
	"gorm.io/gorm"
)

type Pharmacy struct {
	ID            string `gorm:"primaryKey"`
	Name          string
	Address       string
	Phone         string
	Email         string
	LicenseNumber string `gorm:"uniqueIndex"`
	ManagerID     string
	Latitude      float64
	Longitude     float64
	Status        string
	CreatedAt     int64
	UpdatedAt     int64
}

type Drug struct {
	ID           string `gorm:"primaryKey"`
	Name         string
	Dosage       string
	Category     string
	Manufacturer string
	Description  string
	Status       string
	CreatedAt    int64
	UpdatedAt    int64
}

type DrugStock struct {
	ID          string `gorm:"primaryKey"`
	PharmacyID  string
	DrugID      string
	Quantity    int
	UnitPrice   int
	ExpiryDate  int64
	BatchNumber string
	Manufacturer string
	Status      string
	CreatedAt   int64
	UpdatedAt   int64
}

type PrescriptionOrder struct {
	ID          string `gorm:"primaryKey"`
	PatientID   string
	PharmacyID  string
	MedicineList string
	TotalPrice  int
	Status      string
	OrderDate   int64
	CompletedDate int64
	CreatedAt   int64
	UpdatedAt   int64
}

// Seed pharmacy data
func SeedPharmacy(db *gorm.DB) error {
	log.Println("🌱 Starting pharmacy seeding...")

	if err := db.AutoMigrate(&Pharmacy{}, &Drug{}, &DrugStock{}, &PrescriptionOrder{}); err != nil {
		log.Printf("❌ Migration failed: %v\n", err)
		return err
	}

	now := time.Now().Unix()
	expiryDate := time.Now().AddDate(1, 0, 0).Unix() // 1 year from now

	// 1. Create Pharmacies
	pharmacies := []Pharmacy{
		{
			ID:            uuid.New().String(),
			Name:          "Apotek Sehat Sejahtera",
			Address:       "Jl. Merdeka No. 123, Jakarta",
			Phone:         "02142134213",
			Email:         "apotek@meditrack.com",
			LicenseNumber: "APT-001-2024",
			ManagerID:     "pharm-001",
			Latitude:      -6.2088,
			Longitude:     106.8456,
			Status:        "active",
			CreatedAt:     now,
			UpdatedAt:     now,
		},
		{
			ID:            uuid.New().String(),
			Name:          "Apotek Mitra Kesehatan",
			Address:       "Jl. Sudirman No. 456, Jakarta",
			Phone:         "02187654321",
			Email:         "mitra@meditrack.com",
			LicenseNumber: "APT-002-2024",
			ManagerID:     "pharm-002",
			Latitude:      -6.2156,
			Longitude:     106.8000,
			Status:        "active",
			CreatedAt:     now,
			UpdatedAt:     now,
		},
		{
			ID:            uuid.New().String(),
			Name:          "Apotek Farmasi Modern",
			Address:       "Jl. Gatot Subroto No. 789, Jakarta",
			Phone:         "02156789012",
			Email:         "modern@meditrack.com",
			LicenseNumber: "APT-003-2024",
			ManagerID:     "pharm-003",
			Latitude:      -6.2200,
			Longitude:     106.7900,
			Status:        "active",
			CreatedAt:     now,
			UpdatedAt:     now,
		},
	}

	pharmacyIDs := make([]string, len(pharmacies))
	for i, pharmacy := range pharmacies {
		if err := db.Create(&pharmacy).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create pharmacy: %v\n", err)
		} else {
			log.Printf("✅ Created pharmacy: %s\n", pharmacy.Name)
			pharmacyIDs[i] = pharmacy.ID
		}
	}

	// 2. Create Drugs
	drugs := []Drug{
		{
			ID:           uuid.New().String(),
			Name:         "Paracetamol",
			Dosage:       "500mg",
			Category:     "Analgesik",
			Manufacturer: "PT. Farmasi Indonesia",
			Description:  "Obat penurun demam dan penghilang rasa sakit",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Ibuprofen",
			Dosage:       "400mg",
			Category:     "Anti-inflamasi",
			Manufacturer: "PT. Kimia Farma",
			Description:  "Obat anti radang dan penghilang nyeri",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Amoxicillin",
			Dosage:       "500mg",
			Category:     "Antibiotik",
			Manufacturer: "PT. Dexa Medica",
			Description:  "Antibiotik untuk infeksi bakteri",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Metformin",
			Dosage:       "500mg",
			Category:     "Antidiabetes",
			Manufacturer: "PT. Pharos Indonesia",
			Description:  "Obat diabetes tipe 2",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Lisinopril",
			Dosage:       "10mg",
			Category:     "ACE Inhibitor",
			Manufacturer: "PT. Novell Pharma",
			Description:  "Obat tekanan darah tinggi",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Omeprazol",
			Dosage:       "20mg",
			Category:     "Proton Pump Inhibitor",
			Manufacturer: "PT. Tempo Scan",
			Description:  "Obat maag dan GERD",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Cetirizine",
			Dosage:       "10mg",
			Category:     "Antihistamin",
			Manufacturer: "PT. Sanbe Farma",
			Description:  "Obat alergi",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
		{
			ID:           uuid.New().String(),
			Name:         "Vitamin C",
			Dosage:       "1000mg",
			Category:     "Vitamin",
			Manufacturer: "PT. Merck Indonesia",
			Description:  "Suplemen vitamin C",
			Status:       "active",
			CreatedAt:    now,
			UpdatedAt:    now,
		},
	}

	drugIDs := make([]string, len(drugs))
	for i, drug := range drugs {
		if err := db.Create(&drug).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create drug: %v\n", err)
		} else {
			log.Printf("✅ Created drug: %s\n", drug.Name)
			drugIDs[i] = drug.ID
		}
	}

	// 3. Create Drug Stock for each pharmacy
	prices := []int{5000, 15000, 25000, 30000, 40000, 20000, 12000, 8000}

	for _, pharmacyID := range pharmacyIDs {
		for i, drugID := range drugIDs {
			stock := DrugStock{
				ID:           uuid.New().String(),
				PharmacyID:   pharmacyID,
				DrugID:       drugID,
				Quantity:     50 + (i * 20),
				UnitPrice:    prices[i],
				ExpiryDate:   expiryDate,
				BatchNumber:  "BATCH-" + uuid.New().String()[:8],
				Manufacturer: "PT. Farmasi Indonesia",
				Status:       "active",
				CreatedAt:    now,
				UpdatedAt:    now,
			}

			if err := db.Create(&stock).Error; err != nil {
				log.Printf("⚠️  Warning: Could not create drug stock: %v\n", err)
			}
		}
		log.Printf("✅ Created drug stocks for pharmacy: %s\n", pharmacyID)
	}

	// 4. Create Prescription Orders
	patientIDs := []string{
		"pat-001", "pat-002", "pat-003", "pat-004", "pat-005",
		"pat-006", "pat-007", "pat-008", "pat-009", "pat-010",
	}

	orderStatuses := []string{"pending", "ready", "completed", "cancelled"}

	for i := 1; i <= 15; i++ {
		order := PrescriptionOrder{
			ID:           uuid.New().String(),
			PatientID:    patientIDs[(i-1)%len(patientIDs)],
			PharmacyID:   pharmacyIDs[(i-1)%len(pharmacyIDs)],
			MedicineList: "Paracetamol 500mg x10, Ibuprofen 400mg x5",
			TotalPrice:   75000,
			Status:       orderStatuses[(i-1)%len(orderStatuses)],
			OrderDate:    now - int64((i-1)*86400),
			CompletedDate: now,
			CreatedAt:    now,
			UpdatedAt:    now,
		}

		if err := db.Create(&order).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create prescription order: %v\n", err)
		} else {
			log.Printf("✅ Created prescription order: %s\n", order.ID)
		}
	}

	log.Println("✅ Pharmacy seeding completed successfully!")
	return nil
}
