package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/shared/logging"
	"github.com/meditrack/shared/metrics"
	"github.com/meditrack/shared/middleware"
	"github.com/meditrack/user-service/config"
	"github.com/meditrack/user-service/internal/db"
	"github.com/meditrack/user-service/internal/handlers"
)

func main() {
	// Initialize logging
	logging.Initialize()
	log.Println("✅ Logging initialized")

	// Register metrics
	metrics.RegisterMetrics()
	log.Println("✅ Metrics registered")

	// Load configuration
	cfg, err := config.LoadConfig()
	if err != nil {
		log.Fatalf("Failed to load config: %v", err)
	}

	// Initialize database
	_, err = db.InitDB(cfg)
	if err != nil {
		log.Fatalf("Failed to initialize database: %v", err)
	}
	defer db.CloseDB()

	// Create tables
	if err := db.CreateTables(); err != nil {
		log.Fatalf("Failed to create tables: %v", err)
	}

	// Create Gin router
	router := gin.Default()

	// Add middleware
	router.Use(middleware.CorrelationIDMiddleware())
	router.Use(metrics.MetricsMiddleware())

	// Health check route
	router.GET("/health", handlers.HealthCheck)

	// Metrics endpoint
	router.GET("/metrics", gin.WrapH(metrics.MetricsHandler()))

	// User routes
	router.POST("/users", handlers.CreateUser)
	router.GET("/users/:id", handlers.GetUser)
	router.GET("/users", handlers.GetAllUsers)
	router.PUT("/users/:id", handlers.UpdateUser)
	router.DELETE("/users/:id", handlers.DeleteUser)

	// Auth routes
	router.POST("/auth/login", handlers.Login)

	// Set service health to healthy
	metrics.SetServiceHealth("user-service", true)

	// Start server
	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 User Service running on port %s", port)

	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
