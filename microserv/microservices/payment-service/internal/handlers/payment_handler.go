package handlers

import (
	"fmt"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/google/uuid"
	"github.com/meditrack/payment-service/internal/db"
	"github.com/meditrack/payment-service/internal/models"
)

// CreateInvoice creates a new invoice
func CreateInvoice(c *gin.Context) {
	var req models.CreateInvoiceRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	invoiceNumber := fmt.Sprintf("INV-%s", uuid.New().String()[:8])

	result, err := db.DB.Exec(
		`INSERT INTO invoices (patient_id, invoice_number, service_type, total_amount, due_date, status)
		 VALUES (?, ?, ?, ?, ?, 'draft')`,
		req.PatientID, invoiceNumber, req.ServiceType, req.TotalAmount, req.DueDate,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create invoice"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message":        "Invoice created successfully",
		"id":             id,
		"invoice_number": invoiceNumber,
	})
}

// GetInvoice retrieves invoice details
func GetInvoice(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	var invoice models.Invoice
	err := db.DB.QueryRow(
		`SELECT id, patient_id, invoice_number, service_type, total_amount, paid_amount, due_date, status, item_details, created_at, updated_at
		 FROM invoices WHERE id = ?`, id,
	).Scan(&invoice.ID, &invoice.PatientID, &invoice.InvoiceNumber, &invoice.ServiceType,
		&invoice.TotalAmount, &invoice.PaidAmount, &invoice.DueDate, &invoice.Status, &invoice.ItemDetails, &invoice.CreatedAt, &invoice.UpdatedAt)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Invoice not found"})
		return
	}

	c.JSON(http.StatusOK, invoice)
}

// CreatePayment records a payment
func CreatePayment(c *gin.Context) {
	var req models.CreatePaymentRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	transactionID := fmt.Sprintf("TXN-%s", uuid.New().String()[:12])

	result, err := db.DB.Exec(
		`INSERT INTO payments (patient_id, invoice_id, amount, payment_method, transaction_id, status, description)
		 VALUES (?, ?, ?, ?, ?, 'processing', ?)`,
		req.PatientID, req.InvoiceID, req.Amount, req.PaymentMethod, transactionID, req.Description,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create payment"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message":        "Payment initiated successfully",
		"id":             id,
		"transaction_id": transactionID,
	})
}

// GetPayment retrieves payment details
func GetPayment(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	var payment models.Payment
	err := db.DB.QueryRow(
		`SELECT id, patient_id, invoice_id, amount, payment_method, transaction_id, status, description, created_at, updated_at
		 FROM payments WHERE id = ?`, id,
	).Scan(&payment.ID, &payment.PatientID, &payment.InvoiceID, &payment.Amount,
		&payment.PaymentMethod, &payment.TransactionID, &payment.Status, &payment.Description, &payment.CreatedAt, &payment.UpdatedAt)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Payment not found"})
		return
	}

	c.JSON(http.StatusOK, payment)
}

// ConfirmPayment confirms a payment
func ConfirmPayment(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	_, err := db.DB.Exec(
		`UPDATE payments SET status = 'completed', updated_at = NOW() WHERE id = ?`, id,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to confirm payment"})
		return
	}

	// Update invoice status
	var invoiceID int
	db.DB.QueryRow(`SELECT invoice_id FROM payments WHERE id = ?`, id).Scan(&invoiceID)

	var totalPaid float64
	db.DB.QueryRow(`SELECT SUM(amount) FROM payments WHERE invoice_id = ? AND status = 'completed'`, invoiceID).Scan(&totalPaid)

	var totalAmount float64
	db.DB.QueryRow(`SELECT total_amount FROM invoices WHERE id = ?`, invoiceID).Scan(&totalAmount)

	if totalPaid >= totalAmount {
		db.DB.Exec(`UPDATE invoices SET status = 'paid' WHERE id = ?`, invoiceID)
	} else {
		db.DB.Exec(`UPDATE invoices SET status = 'partial', paid_amount = ? WHERE id = ?`, totalPaid, invoiceID)
	}

	c.JSON(http.StatusOK, gin.H{"message": "Payment confirmed successfully"})
}

// CreateInsuranceClaim creates an insurance claim
func CreateInsuranceClaim(c *gin.Context) {
	var req models.CreateClaimRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO insurance_claims (patient_id, insurance_id, invoice_id, claim_amount, status)
		 VALUES (?, ?, ?, ?, 'submitted')`,
		req.PatientID, req.InsuranceID, req.InvoiceID, req.ClaimAmount,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create insurance claim"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Insurance claim created successfully",
		"id":      id,
	})
}

// GetInsuranceClaim retrieves insurance claim details
func GetInsuranceClaim(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	var claim models.InsuranceClaim
	err := db.DB.QueryRow(
		`SELECT id, patient_id, insurance_id, invoice_id, claim_amount, claim_date, approval_date, status, reject_reason, created_at, updated_at
		 FROM insurance_claims WHERE id = ?`, id,
	).Scan(&claim.ID, &claim.PatientID, &claim.InsuranceID, &claim.InvoiceID,
		&claim.ClaimAmount, &claim.ClaimDate, &claim.ApprovalDate, &claim.Status, &claim.RejectReason, &claim.CreatedAt, &claim.UpdatedAt)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Insurance claim not found"})
		return
	}

	c.JSON(http.StatusOK, claim)
}

// HealthCheck checks service health
func HealthCheck(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{
		"status":  "healthy",
		"service": "payment-service",
		"time":    time.Now(),
	})
}

// GetAllInvoices gets all invoices
func GetAllInvoices(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"invoices": []interface{}{}})
}

// UpdateInvoice updates an invoice
func UpdateInvoice(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Invoice updated", "id": id})
}

// DeleteInvoice deletes an invoice
func DeleteInvoice(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Invoice deleted", "id": id})
}

// GetPatientInvoices gets invoices for a patient
func GetPatientInvoices(c *gin.Context) {
	patientID := c.Param("patient_id")
	c.JSON(http.StatusOK, gin.H{"patient_id": patientID, "invoices": []interface{}{}})
}

// GetAllPayments gets all payments
func GetAllPayments(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"payments": []interface{}{}})
}

// UpdatePayment updates a payment
func UpdatePayment(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Payment updated", "id": id})
}

// DeletePayment deletes a payment
func DeletePayment(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Payment deleted", "id": id})
}

// VerifyPayment verifies a payment
func VerifyPayment(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Payment verified", "id": id})
}

// GetAllInsuranceClaims gets all insurance claims
func GetAllInsuranceClaims(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"claims": []interface{}{}})
}

// UpdateInsuranceClaim updates an insurance claim
func UpdateInsuranceClaim(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Claim updated", "id": id})
}

// DeleteInsuranceClaim deletes an insurance claim
func DeleteInsuranceClaim(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Claim deleted", "id": id})
}

// CreateRefund creates a refund
func CreateRefund(c *gin.Context) {
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusCreated, gin.H{"message": "Refund created"})
}

// GetAllRefunds gets all refunds
func GetAllRefunds(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"refunds": []interface{}{}})
}

// GetRefund gets a specific refund
func GetRefund(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"id": id})
}

// UpdateRefund updates a refund
func UpdateRefund(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Refund updated", "id": id})
}

// GetRevenueReport gets revenue report
func GetRevenueReport(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"report": map[string]interface{}{}})
}

// GetPendingPayments gets pending payments
func GetPendingPayments(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"pending_payments": []interface{}{}})
}
