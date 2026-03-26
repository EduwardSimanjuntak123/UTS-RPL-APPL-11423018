package seeders

import (
	"log"
	"time"

	"github.com/google/uuid"
	"gorm.io/gorm"
)

type Payment struct {
	ID              string `gorm:"primaryKey"`
	PatientID       string
	DoctorID        string
	Amount          int
	Method          string
	Status          string
	Description     string
	ReferenceNumber string
	TransactionDate int64
	CreatedAt       int64
	UpdatedAt       int64
}

type Invoice struct {
	ID              string `gorm:"primaryKey"`
	PaymentID       string
	PatientID       string
	InvoiceNumber   string `gorm:"uniqueIndex"`
	Amount          int
	TaxAmount       int
	TotalAmount     int
	Status          string
	IssuedDate      int64
	DueDate         int64
	PaidDate        int64
	Notes           string
	CreatedAt       int64
	UpdatedAt       int64
}

type InsuranceClaim struct {
	ID              string `gorm:"primaryKey"`
	PatientID       string
	InsuranceCode   string
	ClaimType       string
	Amount          int
	Status          string
	Description     string
	SubmittedDate   int64
	ApprovedDate    int64
	CreatedAt       int64
	UpdatedAt       int64
}

// Seed payment data
func SeedPayments(db *gorm.DB) error {
	log.Println("🌱 Starting payment seeding...")

	if err := db.AutoMigrate(&Payment{}, &Invoice{}, &InsuranceClaim{}); err != nil {
		log.Printf("❌ Migration failed: %v\n", err)
		return err
	}

	now := time.Now().Unix()
	dueDate := time.Now().AddDate(0, 0, 30).Unix() // 30 days from now

	// 1. Create Payments
	paymentMethods := []string{"credit_card", "bank_transfer", "cash", "insurance"}
	paymentStatuses := []string{"pending", "completed", "failed", "refunded"}

	patientIDs := []string{
		"pat-001", "pat-002", "pat-003", "pat-004", "pat-005",
		"pat-006", "pat-007", "pat-008", "pat-009", "pat-010",
	}

	doctorIDs := []string{
		"doc-001", "doc-002", "doc-003", "doc-004", "doc-005",
	}

	paymentIDs := make([]string, 15)

	for i := 1; i <= 15; i++ {
		payment := Payment{
			ID:              uuid.New().String(),
			PatientID:       patientIDs[(i-1)%len(patientIDs)],
			DoctorID:        doctorIDs[(i-1)%len(doctorIDs)],
			Amount:          100000 + (i * 50000),
			Method:          paymentMethods[(i-1)%len(paymentMethods)],
			Status:          paymentStatuses[(i-1)%len(paymentStatuses)],
			Description:     "Pembayaran konsultasi medis",
			ReferenceNumber: "REF-" + uuid.New().String()[:12],
			TransactionDate: now - int64((i-1)*86400),
			CreatedAt:       now - int64((i-1)*86400),
			UpdatedAt:       now - int64((i-1)*86400),
		}

		if err := db.Create(&payment).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create payment: %v\n", err)
		} else {
			log.Printf("✅ Created payment: %s\n", payment.ID)
			paymentIDs[i-1] = payment.ID
		}

		// Create Invoice for completed payments
		if payment.Status == "completed" {
			taxAmount := (payment.Amount * 10) / 100
			invoice := Invoice{
				ID:            uuid.New().String(),
				PaymentID:     payment.ID,
				PatientID:     payment.PatientID,
				InvoiceNumber: "INV-" + uuid.New().String()[:12],
				Amount:        payment.Amount,
				TaxAmount:     taxAmount,
				TotalAmount:   payment.Amount + taxAmount,
				Status:        "paid",
				IssuedDate:    now - int64((i-1)*86400),
				DueDate:       dueDate - int64((i-1)*86400),
				PaidDate:      now - int64((i-1)*86400),
				Notes:         "Invoice untuk pembayaran konsultasi",
				CreatedAt:     now - int64((i-1)*86400),
				UpdatedAt:     now - int64((i-1)*86400),
			}

			if err := db.Create(&invoice).Error; err != nil {
				log.Printf("⚠️  Warning: Could not create invoice: %v\n", err)
			} else {
				log.Printf("✅ Created invoice: %s\n", invoice.ID)
			}
		}
	}

	// 2. Create Insurance Claims
	claimTypes := []string{"consultation", "medication", "diagnostic", "hospitalization"}
	insuranceCodes := []string{"ASURANSI-001", "ASURANSI-002", "ASURANSI-003", "ASURANSI-004"}

	for i := 1; i <= 10; i++ {
		claim := InsuranceClaim{
			ID:            uuid.New().String(),
			PatientID:     patientIDs[(i-1)%len(patientIDs)],
			InsuranceCode: insuranceCodes[(i-1)%len(insuranceCodes)],
			ClaimType:     claimTypes[(i-1)%len(claimTypes)],
			Amount:        150000 + (i * 50000),
			Status:        []string{"pending", "approved", "rejected"}[(i-1)%3],
			Description:   "Klaim asuransi kesehatan",
			SubmittedDate: now - int64((i-1)*172800), // 2 days apart
			ApprovedDate:  now - int64((i-1)*86400),
			CreatedAt:     now - int64((i-1)*172800),
			UpdatedAt:     now - int64((i-1)*86400),
		}

		if err := db.Create(&claim).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create insurance claim: %v\n", err)
		} else {
			log.Printf("✅ Created insurance claim: %s\n", claim.ID)
		}
	}

	log.Println("✅ Payment seeding completed successfully!")
	return nil
}
