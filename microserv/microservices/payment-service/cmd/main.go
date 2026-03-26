package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/payment-service/config"
	"github.com/meditrack/payment-service/internal/db"
	"github.com/meditrack/payment-service/internal/handlers"
)

func main() {
	cfg, err := config.LoadConfig()
	if err != nil {
		log.Fatalf("Failed to load config: %v", err)
	}

	_, err = db.InitDB(cfg)
	if err != nil {
		log.Fatalf("Failed to initialize database: %v", err)
	}
	defer db.CloseDB()

	if err := db.CreateTables(); err != nil {
		log.Fatalf("Failed to create tables: %v", err)
	}

	router := gin.Default()

	// Health check
	router.GET("/health", handlers.HealthCheck)

	// Invoice routes
	router.POST("/invoices", handlers.CreateInvoice)
	router.GET("/invoices/:id", handlers.GetInvoice)

	// Payment routes
	router.POST("/payments", handlers.CreatePayment)
	router.GET("/payments/:id", handlers.GetPayment)
	router.POST("/payments/:id/confirm", handlers.ConfirmPayment)

	// Insurance claim routes
	router.POST("/insurance-claims", handlers.CreateInsuranceClaim)
	router.GET("/insurance-claims/:id", handlers.GetInsuranceClaim)

	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 Payment Service running on port %s", port)
	
	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
