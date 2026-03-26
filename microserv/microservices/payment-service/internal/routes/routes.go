package routes

import (
	"github.com/gin-gonic/gin"
	"github.com/meditrack/payment-service/internal/handlers"
)

// SetupPaymentRoutes sets up all payment service routes
func SetupPaymentRoutes(router *gin.Engine) {
	// Health check
	router.GET("/health", handlers.HealthCheck)

	// Invoices endpoints
	invoiceRoutes := router.Group("/invoices")
	{
		invoiceRoutes.POST("", handlers.CreateInvoice)                 // POST /invoices - Create invoice
		invoiceRoutes.GET("", handlers.GetAllInvoices)                 // GET /invoices - List all
		invoiceRoutes.GET("/:id", handlers.GetInvoice)                 // GET /invoices/:id - Get by ID
		invoiceRoutes.PUT("/:id", handlers.UpdateInvoice)              // PUT /invoices/:id - Update
		invoiceRoutes.DELETE("/:id", handlers.DeleteInvoice)           // DELETE /invoices/:id - Delete
	}

	// Patient invoices
	patientInvoiceRoutes := router.Group("/patients/:patient_id/invoices")
	{
		patientInvoiceRoutes.GET("", handlers.GetPatientInvoices)      // GET /patients/:patient_id/invoices
	}

	// Payments endpoints
	paymentRoutes := router.Group("/payments")
	{
		paymentRoutes.POST("", handlers.CreatePayment)                 // POST /payments - Create payment
		paymentRoutes.GET("", handlers.GetAllPayments)                 // GET /payments - List all
		paymentRoutes.GET("/:id", handlers.GetPayment)                 // GET /payments/:id - Get by ID
		paymentRoutes.PUT("/:id", handlers.UpdatePayment)              // PUT /payments/:id - Update
		paymentRoutes.DELETE("/:id", handlers.DeletePayment)           // DELETE /payments/:id - Delete
	}

	// Payment confirmation
	paymentConfirmRoutes := router.Group("/payments/:id")
	{
		paymentConfirmRoutes.POST("/confirm", handlers.ConfirmPayment) // POST /payments/:id/confirm
		paymentConfirmRoutes.POST("/verify", handlers.VerifyPayment)   // POST /payments/:id/verify
	}

	// Insurance Claims endpoints
	claimRoutes := router.Group("/insurance-claims")
	{
		claimRoutes.POST("", handlers.CreateInsuranceClaim)            // POST /insurance-claims - Create claim
		claimRoutes.GET("", handlers.GetAllInsuranceClaims)            // GET /insurance-claims - List all
		claimRoutes.GET("/:id", handlers.GetInsuranceClaim)            // GET /insurance-claims/:id - Get by ID
		claimRoutes.PUT("/:id", handlers.UpdateInsuranceClaim)         // PUT /insurance-claims/:id - Update
		claimRoutes.DELETE("/:id", handlers.DeleteInsuranceClaim)      // DELETE /insurance-claims/:id - Delete
	}

	// Refunds endpoints
	refundRoutes := router.Group("/refunds")
	{
		refundRoutes.POST("", handlers.CreateRefund)                   // POST /refunds - Create refund
		refundRoutes.GET("", handlers.GetAllRefunds)                   // GET /refunds - List all
		refundRoutes.GET("/:id", handlers.GetRefund)                   // GET /refunds/:id - Get by ID
		refundRoutes.PUT("/:id", handlers.UpdateRefund)                // PUT /refunds/:id - Update
	}

	// Reports
	reportRoutes := router.Group("/reports")
	{
		reportRoutes.GET("/revenue", handlers.GetRevenueReport)        // GET /reports/revenue
		reportRoutes.GET("/pending", handlers.GetPendingPayments)      // GET /reports/pending
	}
}
