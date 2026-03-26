package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/api-gateway/config"
	"github.com/meditrack/api-gateway/internal/handlers"
	"github.com/meditrack/api-gateway/middleware"
)

func main() {
	cfg, err := config.LoadConfig()
	if err != nil {
		log.Fatalf("Failed to load config: %v", err)
	}

	router := gin.Default()

	// Middleware
	router.Use(middleware.CORSMiddleware(cfg))
	router.Use(middleware.RateLimitMiddleware(cfg))
	router.Use(middleware.RequestLoggingMiddleware())

	// Public endpoints
	router.GET("/health", handlers.HealthCheck(cfg))
	router.GET("/status", handlers.ServiceStatus(cfg))

	// Auth routes (proxy to user service)
	router.POST("/auth/login", handlers.ProxyRequest(cfg.UserServiceURL))
	router.POST("/auth/register", handlers.ProxyRequest(cfg.UserServiceURL))

	// User Service routes
	apiV1 := router.Group("/api/v1")
	apiV1.Use(middleware.AuthMiddleware())
	{
		// Users
		apiV1.POST("/users", handlers.ProxyRequest(cfg.UserServiceURL))
		apiV1.GET("/users/:id", handlers.ProxyRequest(cfg.UserServiceURL))
		apiV1.GET("/users", handlers.ProxyRequest(cfg.UserServiceURL))
		apiV1.PUT("/users/:id", handlers.ProxyRequest(cfg.UserServiceURL))
		apiV1.DELETE("/users/:id", handlers.ProxyRequest(cfg.UserServiceURL))

		// Appointments
		apiV1.POST("/appointments", handlers.ProxyRequest(cfg.AppointmentServiceURL))
		apiV1.GET("/appointments/:id", handlers.ProxyRequest(cfg.AppointmentServiceURL))
		apiV1.GET("/appointments", handlers.ProxyRequest(cfg.AppointmentServiceURL))
		apiV1.GET("/patients/:patient_id/appointments", handlers.ProxyRequest(cfg.AppointmentServiceURL))
		apiV1.PUT("/appointments/:id/status", handlers.ProxyRequest(cfg.AppointmentServiceURL))
		apiV1.DELETE("/appointments/:id", handlers.ProxyRequest(cfg.AppointmentServiceURL))

		// Medical Records
		apiV1.POST("/medical-records", handlers.ProxyRequest(cfg.MedicalServiceURL))
		apiV1.GET("/patients/:patient_id/medical-records", handlers.ProxyRequest(cfg.MedicalServiceURL))

		// Prescriptions
		apiV1.POST("/prescriptions", handlers.ProxyRequest(cfg.MedicalServiceURL))
		apiV1.GET("/patients/:patient_id/prescriptions", handlers.ProxyRequest(cfg.MedicalServiceURL))

		// Lab Results
		apiV1.POST("/lab-results", handlers.ProxyRequest(cfg.MedicalServiceURL))

		// Drugs
		apiV1.POST("/drugs", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.GET("/drugs", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.GET("/drugs/:id", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.PUT("/drugs/:id", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.DELETE("/drugs/:id", handlers.ProxyRequest(cfg.PharmacyServiceURL))

		// Stock
		apiV1.POST("/stocks", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.GET("/stocks/:drug_id", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.GET("/low-stock", handlers.ProxyRequest(cfg.PharmacyServiceURL))

		// Orders
		apiV1.POST("/orders", handlers.ProxyRequest(cfg.PharmacyServiceURL))
		apiV1.GET("/orders/:id", handlers.ProxyRequest(cfg.PharmacyServiceURL))

		// Invoices
		apiV1.POST("/invoices", handlers.ProxyRequest(cfg.PaymentServiceURL))
		apiV1.GET("/invoices/:id", handlers.ProxyRequest(cfg.PaymentServiceURL))

		// Payments
		apiV1.POST("/payments", handlers.ProxyRequest(cfg.PaymentServiceURL))
		apiV1.GET("/payments/:id", handlers.ProxyRequest(cfg.PaymentServiceURL))
		apiV1.POST("/payments/:id/confirm", handlers.ProxyRequest(cfg.PaymentServiceURL))

		// Insurance Claims
		apiV1.POST("/insurance-claims", handlers.ProxyRequest(cfg.PaymentServiceURL))
		apiV1.GET("/insurance-claims/:id", handlers.ProxyRequest(cfg.PaymentServiceURL))

		// Analytics
		apiV1.POST("/metrics", handlers.ProxyRequest(cfg.AnalyticsServiceURL))
		apiV1.GET("/metrics/:service", handlers.ProxyRequest(cfg.AnalyticsServiceURL))
		apiV1.GET("/health-indicators", handlers.ProxyRequest(cfg.AnalyticsServiceURL))
		apiV1.GET("/alerts", handlers.ProxyRequest(cfg.AnalyticsServiceURL))
		apiV1.POST("/alerts", handlers.ProxyRequest(cfg.AnalyticsServiceURL))
		apiV1.GET("/dashboard/summary", handlers.ProxyRequest(cfg.AnalyticsServiceURL))
	}

	port := fmt.Sprintf(":%d", cfg.ServicePort)
	log.Printf("🚀 API Gateway running on port %s", port)
	log.Printf("📊 Services orchestrated and routed")

	if err := router.Run(port); err != nil {
		log.Fatalf("Failed to start gateway: %v", err)
	}
}
