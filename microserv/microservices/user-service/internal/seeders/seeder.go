package seeders

import (
	"fmt"
	"log"

	"github.com/google/uuid"
	"gorm.io/gorm"
)

type User struct {
	ID        string `gorm:"primaryKey"`
	Name      string
	Email     string `gorm:"uniqueIndex"`
	Password  string
	Phone     string
	Address   string
	Role      string
	Status    string
	CreatedAt int64
	UpdatedAt int64
}

type Department struct {
	ID           string `gorm:"primaryKey"`
	Name         string
	Description  string
	HeadDoctorID string
	Status       string
	CreatedAt    int64
	UpdatedAt    int64
}

type Doctor struct {
	ID             string `gorm:"primaryKey"`
	UserID         string
	LicenseNumber  string `gorm:"uniqueIndex"`
	Specialization string
	DepartmentID   string
	ExperienceYears int
	ConsultationFee int
	Status         string
	CreatedAt      int64
	UpdatedAt      int64
}

type Patient struct {
	ID             string `gorm:"primaryKey"`
	UserID         string
	MedicalNumber  string `gorm:"uniqueIndex"`
	DateOfBirth    int64
	Gender         string
	BloodType      string
	EmergencyContact string
	Status         string
	CreatedAt      int64
	UpdatedAt      int64
}

type Pharmacist struct {
	ID             string `gorm:"primaryKey"`
	UserID         string
	LicenseNumber  string `gorm:"uniqueIndex"`
	PharmacyID     string
	ExperienceYears int
	Status         string
	CreatedAt      int64
	UpdatedAt      int64
}

type Admin struct {
	ID        string `gorm:"primaryKey"`
	UserID    string
	Role      string
	Status    string
	CreatedAt int64
	UpdatedAt int64
}

// Seed function to populate all data
func SeedDatabase(db *gorm.DB) error {
	log.Println("🌱 Starting database seeding...")

	// Auto migrate tables
	if err := db.AutoMigrate(&User{}, &Department{}, &Doctor{}, &Patient{}, &Pharmacist{}, &Admin{}); err != nil {
		log.Printf("❌ Migration failed: %v\n", err)
		return err
	}

	now := int64(1000000000)

	// 1. Seed Admin Users (2 admins)
	admins := []User{
		{
			ID:       uuid.New().String(),
			Name:     "Admin Utama",
			Email:    "admin@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO", // bcrypt hash of "password123"
			Phone:    "081234567890",
			Address:  "Jl. Admin No. 1",
			Role:     "admin",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Admin Sekunder",
			Email:    "admin2@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO", // bcrypt hash of "password123"
			Phone:    "081234567891",
			Address:  "Jl. Admin No. 2",
			Role:     "admin",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
	}

	for _, admin := range admins {
		if err := db.Create(&admin).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create admin: %v\n", err)
		} else {
			log.Printf("✅ Created admin: %s (%s)\n", admin.Name, admin.Email)
		}
	}

	// 2. Seed Departments
	deptIDs := []string{uuid.New().String(), uuid.New().String(), uuid.New().String()}
	departments := []Department{
		{
			ID:          deptIDs[0],
			Name:        "Kardiologi",
			Description: "Departemen Jantung dan Pembuluh Darah",
			Status:      "active",
			CreatedAt:   now,
			UpdatedAt:   now,
		},
		{
			ID:          deptIDs[1],
			Name:        "Neurologi",
			Description: "Departemen Saraf",
			Status:      "active",
			CreatedAt:   now,
			UpdatedAt:   now,
		},
		{
			ID:          deptIDs[2],
			Name:        "Umum",
			Description: "Praktik Umum",
			Status:      "active",
			CreatedAt:   now,
			UpdatedAt:   now,
		},
	}

	for _, dept := range departments {
		if err := db.Create(&dept).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create department: %v\n", err)
		} else {
			log.Printf("✅ Created department: %s\n", dept.Name)
		}
	}

	// 3. Seed Doctors (5 doctors)
	doctorUsers := []User{
		{
			ID:       uuid.New().String(),
			Name:     "Dr. Budi Hartono",
			Email:    "dr.budi@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567800",
			Address:  "Jl. Dokter No. 1",
			Role:     "doctor",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Dr. Siti Nurhaliza",
			Email:    "dr.siti@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567801",
			Address:  "Jl. Dokter No. 2",
			Role:     "doctor",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Dr. Ahmad Rizki",
			Email:    "dr.ahmad@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567802",
			Address:  "Jl. Dokter No. 3",
			Role:     "doctor",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Dr. Eka Putri",
			Email:    "dr.eka@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567803",
			Address:  "Jl. Dokter No. 4",
			Role:     "doctor",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Dr. Rinto Harahap",
			Email:    "dr.rinto@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567804",
			Address:  "Jl. Dokter No. 5",
			Role:     "doctor",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
	}

	doctors := make([]Doctor, len(doctorUsers))
	specializations := []string{"Kardiologi", "Neurologi", "Umum", "Penyakit Dalam", "Bedah"}

	for i, user := range doctorUsers {
		if err := db.Create(&user).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create doctor user: %v\n", err)
			continue
		}

		doctors[i] = Doctor{
			ID:              uuid.New().String(),
			UserID:          user.ID,
			LicenseNumber:   fmt.Sprintf("DR%05d", i+1001),
			Specialization:  specializations[i],
			DepartmentID:    deptIDs[i%len(deptIDs)],
			ExperienceYears: 5 + i,
			ConsultationFee: 100000 + (i * 50000),
			Status:          "active",
			CreatedAt:       now,
			UpdatedAt:       now,
		}

		if err := db.Create(&doctors[i]).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create doctor: %v\n", err)
		} else {
			log.Printf("✅ Created doctor: %s (%s)\n", user.Name, user.Email)
		}
	}

	// 4. Seed Patients (10 patients)
	patientUsers := make([]User, 10)
	for i := 0; i < 10; i++ {
		patientUsers[i] = User{
			ID:       uuid.New().String(),
			Name:     fmt.Sprintf("Pasien %d", i+1),
			Email:    fmt.Sprintf("pasien%d@meditrack.com", i+1),
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    fmt.Sprintf("0812345678%02d", i),
			Address:  fmt.Sprintf("Jl. Pasien No. %d", i+1),
			Role:     "patient",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		}

		if err := db.Create(&patientUsers[i]).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create patient user: %v\n", err)
			continue
		}

		patient := Patient{
			ID:             uuid.New().String(),
			UserID:         patientUsers[i].ID,
			MedicalNumber:  fmt.Sprintf("MED%05d", i+5001),
			DateOfBirth:    935635200,
			Gender:         []string{"M", "F"}[i%2],
			BloodType:      []string{"A", "B", "AB", "O"}[i%4],
			EmergencyContact: fmt.Sprintf("0821%08d", 10000000+i),
			Status:         "active",
			CreatedAt:      now,
			UpdatedAt:      now,
		}

		if err := db.Create(&patient).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create patient: %v\n", err)
		} else {
			log.Printf("✅ Created patient: %s (%s)\n", patientUsers[i].Name, patientUsers[i].Email)
		}
	}

	// 5. Seed Pharmacists (3 pharmacists)
	pharmacistUsers := []User{
		{
			ID:       uuid.New().String(),
			Name:     "Apt. Budi Setiawan",
			Email:    "apt.budi@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567900",
			Address:  "Jl. Apotek No. 1",
			Role:     "pharmacist",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Apt. Siti Handayani",
			Email:    "apt.siti@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567901",
			Address:  "Jl. Apotek No. 2",
			Role:     "pharmacist",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
		{
			ID:       uuid.New().String(),
			Name:     "Apt. Rudi Gunawan",
			Email:    "apt.rudi@meditrack.com",
			Password: "$2y$10$1QAxTsotgMFkvy6rlVND6O2qbUUrEwjqkjiOcL2UAeKAY9ykJRBjO",
			Phone:    "081234567902",
			Address:  "Jl. Apotek No. 3",
			Role:     "pharmacist",
			Status:   "active",
			CreatedAt: now,
			UpdatedAt: now,
		},
	}

	for i, user := range pharmacistUsers {
		if err := db.Create(&user).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create pharmacist user: %v\n", err)
			continue
		}

		pharmacist := Pharmacist{
			ID:              uuid.New().String(),
			UserID:          user.ID,
			LicenseNumber:   fmt.Sprintf("APT%05d", i+8001),
			PharmacyID:      uuid.New().String(),
			ExperienceYears: 3 + i,
			Status:          "active",
			CreatedAt:       now,
			UpdatedAt:       now,
		}

		if err := db.Create(&pharmacist).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create pharmacist: %v\n", err)
		} else {
			log.Printf("✅ Created pharmacist: %s (%s)\n", user.Name, user.Email)
		}
	}

	// 6. Create admin records
	for _, admin := range admins {
		adminRecord := Admin{
			ID:        uuid.New().String(),
			UserID:    admin.ID,
			Role:      "super_admin",
			Status:    "active",
			CreatedAt: now,
			UpdatedAt: now,
		}

		if err := db.Create(&adminRecord).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create admin record: %v\n", err)
		}
	}

	log.Println("✅ Database seeding completed successfully!")
	return nil
}


