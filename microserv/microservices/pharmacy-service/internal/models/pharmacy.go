package models

import "time"

type Drug struct {
	ID                int       `json:"id"`
	DrugName          string    `json:"drug_name" binding:"required"`
	GenericName       string    `json:"generic_name"`
	Manufacturer      string    `json:"manufacturer"`
	Dosage            string    `json:"dosage" binding:"required"`
	FormType          string    `json:"form_type"`
	Price             float64   `json:"price" binding:"required"`
	LicenseNumber     string    `json:"license_number"`
	ExpiryDate        time.Time `json:"expiry_date"`
	StorageCondition  string    `json:"storage_condition"`
	Description       string    `json:"description"`
	CreatedAt         time.Time `json:"created_at"`
	UpdatedAt         time.Time `json:"updated_at"`
}

type DrugStock struct {
	ID           int       `json:"id"`
	DrugID       int       `json:"drug_id" binding:"required"`
	Quantity     int       `json:"quantity" binding:"required"`
	ReorderLevel int       `json:"reorder_level"`
	Location     string    `json:"location"`
	LastRestocked time.Time `json:"last_restocked"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}

type PharmacyOrder struct {
	ID            int       `json:"id"`
	PatientID     int       `json:"patient_id" binding:"required"`
	PrescriptionID int      `json:"prescription_id" binding:"required"`
	OrderDate     time.Time `json:"order_date"`
	Status        string    `json:"status"`
	TotalAmount   float64   `json:"total_amount"`
	PaymentStatus string    `json:"payment_status"`
	ReadyDate     *time.Time `json:"ready_date"`
	PickupDate    *time.Time `json:"pickup_date"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}

type OrderItem struct {
	ID       int    `json:"id"`
	OrderID  int    `json:"order_id"`
	DrugID   int    `json:"drug_id"`
	Quantity int    `json:"quantity"`
	UnitPrice float64 `json:"unit_price"`
	Subtotal float64 `json:"subtotal"`
}

type DrugInventory struct {
	ID               int       `json:"id"`
	DrugID           int       `json:"drug_id" binding:"required"`
	QuantityAdded    int       `json:"quantity_added" binding:"required"`
	QuantityConsumed int       `json:"quantity_consumed"`
	TransactionType  string    `json:"transaction_type"`
	Reason           string    `json:"reason"`
	CreatedAt        time.Time `json:"created_at"`
}

type CreateDrugRequest struct {
	DrugName         string    `json:"drug_name" binding:"required"`
	GenericName      string    `json:"generic_name"`
	Manufacturer     string    `json:"manufacturer"`
	Dosage           string    `json:"dosage" binding:"required"`
	FormType         string    `json:"form_type"`
	Price            float64   `json:"price" binding:"required"`
	LicenseNumber    string    `json:"license_number"`
	ExpiryDate       time.Time `json:"expiry_date"`
	StorageCondition string    `json:"storage_condition"`
}

type CreateStockRequest struct {
	DrugID       int    `json:"drug_id" binding:"required"`
	Quantity     int    `json:"quantity" binding:"required"`
	ReorderLevel int    `json:"reorder_level"`
	Location     string `json:"location"`
}
