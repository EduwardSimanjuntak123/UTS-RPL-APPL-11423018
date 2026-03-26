package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/medical-service/config"
	"github.com/meditrack/medical-service/internal/db"
	"github.com/meditrack/medical-service/internal/handlers"
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

	// Medical Records routes
	router.POST("/medical-records", handlers.CreateMedicalRecord)
	router.GET("/patients/:patient_id/medical-records", handlers.GetPatientMedicalRecords)

	// Prescriptions routes
	router.POST("/prescriptions", handlers.CreatePrescription)
	router.GET("/patients/:patient_id/prescriptions", handlers.GetPatientPrescriptions)

	// Lab Results routes
	router.POST("/lab-results", handlers.CreateLabResult)

	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 Medical Service running on port %s", port)
	
	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
