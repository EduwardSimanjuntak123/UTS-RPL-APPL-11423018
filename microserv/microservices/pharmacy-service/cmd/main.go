package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/pharmacy-service/config"
	"github.com/meditrack/pharmacy-service/internal/db"
	"github.com/meditrack/pharmacy-service/internal/handlers"
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

	// Drug management
	router.POST("/drugs", handlers.CreateDrug)
	router.GET("/drugs", handlers.GetAllDrugs)
	router.GET("/drugs/:id", handlers.GetDrug)
	router.PUT("/drugs/:id", handlers.UpdateDrug)
	router.DELETE("/drugs/:id", handlers.DeleteDrug)

	// Stock management
	router.POST("/stocks", handlers.CreateStock)
	router.GET("/stocks/:drug_id", handlers.GetStock)
	router.GET("/low-stock", handlers.GetLowStock)

	// Order management
	router.POST("/orders", handlers.CreateOrder)
	router.GET("/orders/:id", handlers.GetOrder)

	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 Pharmacy Service running on port %s", port)
	
	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
