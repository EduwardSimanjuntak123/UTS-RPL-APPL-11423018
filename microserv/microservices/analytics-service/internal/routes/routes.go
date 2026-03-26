package routes

import (
	"github.com/gin-gonic/gin"
	"github.com/meditrack/analytics-service/internal/handlers"
)

// SetupAnalyticsRoutes sets up all analytics service routes
func SetupAnalyticsRoutes(router *gin.Engine) {
	// Health check
	router.GET("/health", handlers.HealthCheck)

	// Service Metrics endpoints
	metricRoutes := router.Group("/metrics")
	{
		metricRoutes.GET("", handlers.GetServiceMetrics)               // GET /metrics - Get all service metrics
		metricRoutes.GET("/:service", handlers.GetServiceMetrics)      // GET /metrics/:service - Get specific service
		metricRoutes.POST("", handlers.RecordServiceMetric)            // POST /metrics - Record new metric
	}

	// Health Indicators endpoints
	healthRoutes := router.Group("/health-indicators")
	{
		healthRoutes.GET("", handlers.GetHealthIndicators)             // GET /health-indicators - Get all
		healthRoutes.GET("/:service", handlers.GetServiceHealth)       // GET /health-indicators/:service - Specific service
	}

	// System Alerts endpoints
	alertRoutes := router.Group("/alerts")
	{
		alertRoutes.GET("", handlers.GetSystemAlerts)                  // GET /alerts - Get all alerts
		alertRoutes.POST("", handlers.CreateSystemAlert)               // POST /alerts - Create new alert
		alertRoutes.GET("/:id", handlers.GetAlert)                     // GET /alerts/:id - Get specific alert
		alertRoutes.PUT("/:id/resolve", handlers.ResolveAlert)         // PUT /alerts/:id/resolve - Resolve alert
		alertRoutes.PUT("/:id", handlers.UpdateAlert)                  // PUT /alerts/:id - Update alert
		alertRoutes.DELETE("/:id", handlers.DeleteAlert)               // DELETE /alerts/:id - Delete alert
	}

	// User Analytics endpoints
	userAnalyticsRoutes := router.Group("/user-analytics")
	{
		userAnalyticsRoutes.GET("", handlers.GetUserAnalytics)         // GET /user-analytics - Get user stats
		userAnalyticsRoutes.GET("/:day", handlers.GetUserAnalyticsDay) // GET /user-analytics/:day - Specific day
	}

	// Appointment Analytics endpoints
	appointmentAnalyticsRoutes := router.Group("/appointment-analytics")
	{
		appointmentAnalyticsRoutes.GET("", handlers.GetAppointmentAnalytics) // GET /appointment-analytics
		appointmentAnalyticsRoutes.GET("/:day", handlers.GetAppointmentAnalyticsDay) // GET /appointment-analytics/:day
	}

	// Revenue Analytics endpoints
	revenueAnalyticsRoutes := router.Group("/revenue-analytics")
	{
		revenueAnalyticsRoutes.GET("", handlers.GetRevenueAnalytics)   // GET /revenue-analytics
		revenueAnalyticsRoutes.GET("/:day", handlers.GetRevenueAnalyticsDay) // GET /revenue-analytics/:day
	}

	// Dashboard Summary
	dashboardRoutes := router.Group("/dashboard")
	{
		dashboardRoutes.GET("/summary", handlers.GetDashboardSummary)  // GET /dashboard/summary - Overview
		dashboardRoutes.GET("/overview", handlers.GetDashboardOverview) // GET /dashboard/overview - Detailed overview
	}

	// Reports
	reportRoutes := router.Group("/reports")
	{
		reportRoutes.GET("/daily", handlers.GetDailyReport)            // GET /reports/daily
		reportRoutes.GET("/weekly", handlers.GetWeeklyReport)          // GET /reports/weekly
		reportRoutes.GET("/monthly", handlers.GetMonthlyReport)        // GET /reports/monthly
		reportRoutes.GET("/custom", handlers.GetCustomReport)          // GET /reports/custom
	}
}
