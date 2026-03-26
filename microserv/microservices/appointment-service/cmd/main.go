package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/appointment-service/config"
	"github.com/meditrack/appointment-service/internal/db"
	"github.com/meditrack/appointment-service/internal/handlers"
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

	// Appointment routes
	router.POST("/appointments", handlers.CreateAppointment)
	router.GET("/appointments/:id", handlers.GetAppointment)
	router.GET("/appointments", handlers.GetAllAppointments)
	router.GET("/patients/:patient_id/appointments", handlers.GetPatientAppointments)
	router.PUT("/appointments/:id/status", handlers.UpdateAppointmentStatus)
	router.DELETE("/appointments/:id", handlers.CancelAppointment)

	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 Appointment Service running on port %s", port)
	
	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
