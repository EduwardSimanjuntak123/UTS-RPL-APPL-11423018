package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/analytics-service/config"
	"github.com/meditrack/analytics-service/internal/db"
	"github.com/meditrack/analytics-service/internal/handlers"
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

	// Metrics routes
	router.POST("/metrics", handlers.RecordServiceMetric)
	router.GET("/metrics/:service", handlers.GetServiceMetrics)

	// Health monitoring
	router.GET("/health-indicators", handlers.GetHealthIndicators)

	// Alerts
	router.GET("/alerts", handlers.GetActiveAlerts)
	router.POST("/alerts", handlers.CreateAlert)

	// Dashboard
	router.GET("/dashboard/summary", handlers.GetDashboardSummary)

	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 Analytics Service running on port %s", port)
	
	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
