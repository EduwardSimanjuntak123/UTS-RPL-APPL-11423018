package models

import "time"

type Payment struct {
	ID              int       `json:"id"`
	PatientID       int       `json:"patient_id" binding:"required"`
	InvoiceID       int       `json:"invoice_id" binding:"required"`
	Amount          float64   `json:"amount" binding:"required"`
	PaymentMethod   string    `json:"payment_method" binding:"required"`
	TransactionID   string    `json:"transaction_id"`
	Status          string    `json:"status"`
	Description     string    `json:"description"`
	CreatedAt       time.Time `json:"created_at"`
	UpdatedAt       time.Time `json:"updated_at"`
}

type Invoice struct {
	ID              int       `json:"id"`
	PatientID       int       `json:"patient_id" binding:"required"`
	InvoiceNumber   string    `json:"invoice_number"`
	ServiceType     string    `json:"service_type"`
	TotalAmount     float64   `json:"total_amount" binding:"required"`
	PaidAmount      float64   `json:"paid_amount"`
	DueDate         time.Time `json:"due_date"`
	Status          string    `json:"status"`
	ItemDetails     string    `json:"item_details"`
	CreatedAt       time.Time `json:"created_at"`
	UpdatedAt       time.Time `json:"updated_at"`
}

type InsuranceClaim struct {
	ID              int       `json:"id"`
	PatientID       int       `json:"patient_id" binding:"required"`
	InsuranceID     int       `json:"insurance_id" binding:"required"`
	InvoiceID       int       `json:"invoice_id" binding:"required"`
	ClaimAmount     float64   `json:"claim_amount" binding:"required"`
	ClaimDate       time.Time `json:"claim_date"`
	ApprovalDate    *time.Time `json:"approval_date"`
	Status          string    `json:"status"`
	RejectReason    string    `json:"reject_reason"`
	CreatedAt       time.Time `json:"created_at"`
	UpdatedAt       time.Time `json:"updated_at"`
}

type PaymentProof struct {
	ID              int       `json:"id"`
	PaymentID       int       `json:"payment_id" binding:"required"`
	ProofURL        string    `json:"proof_url" binding:"required"`
	FileType        string    `json:"file_type"`
	UploadedAt      time.Time `json:"uploaded_at"`
}

type CreatePaymentRequest struct {
	PatientID     int     `json:"patient_id" binding:"required"`
	InvoiceID     int     `json:"invoice_id" binding:"required"`
	Amount        float64 `json:"amount" binding:"required"`
	PaymentMethod string  `json:"payment_method" binding:"required"`
	Description   string  `json:"description"`
}

type CreateInvoiceRequest struct {
	PatientID   int       `json:"patient_id" binding:"required"`
	ServiceType string    `json:"service_type" binding:"required"`
	TotalAmount float64   `json:"total_amount" binding:"required"`
	DueDate     time.Time `json:"due_date" binding:"required"`
	ItemDetails string    `json:"item_details"`
}

type CreateClaimRequest struct {
	PatientID   int     `json:"patient_id" binding:"required"`
	InsuranceID int     `json:"insurance_id" binding:"required"`
	InvoiceID   int     `json:"invoice_id" binding:"required"`
	ClaimAmount float64 `json:"claim_amount" binding:"required"`
}
