package handlers

import (
	"database/sql"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/analytics-service/internal/db"
	"github.com/meditrack/analytics-service/internal/models"
)

// RecordServiceMetric records a service metric
func RecordServiceMetric(c *gin.Context) {
	var metric models.ServiceMetric

	if err := c.ShouldBindJSON(&metric); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	_, err := db.DB.Exec(
		`INSERT INTO service_metrics (service_name, response_time, request_count, error_count, throughput_kbs, status)
		 VALUES (?, ?, ?, ?, ?, ?)`,
		metric.ServiceName, metric.ResponseTime, metric.RequestCount, metric.ErrorCount, metric.ThroughputKBs, metric.Status,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to record metric"})
		return
	}

	c.JSON(http.StatusCreated, gin.H{"message": "Metric recorded successfully"})
}

// GetServiceMetrics gets metrics for a service
func GetServiceMetrics(c *gin.Context) {
	serviceName := c.Param("service")

	rows, err := db.DB.Query(
		`SELECT id, service_name, timestamp, response_time, request_count, error_count, throughput_kbs, status, created_at
		 FROM service_metrics WHERE service_name = ? ORDER BY created_at DESC LIMIT 100`, serviceName,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch metrics"})
		return
	}
	defer rows.Close()

	var metrics []models.ServiceMetric
	for rows.Next() {
		var metric models.ServiceMetric
		rows.Scan(&metric.ID, &metric.ServiceName, &metric.Timestamp, &metric.ResponseTime,
			&metric.RequestCount, &metric.ErrorCount, &metric.ThroughputKBs, &metric.Status, &metric.CreatedAt)
		metrics = append(metrics, metric)
	}

	c.JSON(http.StatusOK, metrics)
}

// GetHealthIndicators gets system health status
func GetHealthIndicators(c *gin.Context) {
	rows, err := db.DB.Query(
		`SELECT id, timestamp, service_name, status, response_time, error_rate, database_connection_ok, created_at
		 FROM health_indicators ORDER BY timestamp DESC LIMIT 50`,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch health indicators"})
		return
	}
	defer rows.Close()

	var indicators []models.HealthIndicator
	for rows.Next() {
		var indicator models.HealthIndicator
		rows.Scan(&indicator.ID, &indicator.Timestamp, &indicator.ServiceName, &indicator.Status,
			&indicator.ResponseTime, &indicator.ErrorRate, &indicator.DatabaseConnectionOk, &indicator.CreatedAt)
		indicators = append(indicators, indicator)
	}

	c.JSON(http.StatusOK, indicators)
}

// GetActiveAlerts gets active system alerts
func GetActiveAlerts(c *gin.Context) {
	rows, err := db.DB.Query(
		`SELECT id, alert_type, severity, message, service_name, status, resolved_at, created_at
		 FROM system_alerts WHERE status = 'active' ORDER BY created_at DESC`,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch alerts"})
		return
	}
	defer rows.Close()

	var alerts []models.SystemAlert
	for rows.Next() {
		var alert models.SystemAlert
		rows.Scan(&alert.ID, &alert.AlertType, &alert.Severity, &alert.Message, &alert.ServiceName, &alert.Status, &alert.ResolvedAt, &alert.CreatedAt)
		alerts = append(alerts, alert)
	}

	c.JSON(http.StatusOK, alerts)
}

// CreateAlert creates a new system alert
func CreateAlert(c *gin.Context) {
	var alert models.SystemAlert

	if err := c.ShouldBindJSON(&alert); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO system_alerts (alert_type, severity, message, service_name, status)
		 VALUES (?, ?, ?, ?, 'active')`,
		alert.AlertType, alert.Severity, alert.Message, alert.ServiceName,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create alert"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Alert created successfully",
		"id":      id,
	})
}

// GetDashboardSummary gets dashboard summary
func GetDashboardSummary(c *gin.Context) {
	summary := models.DashboardSummary{}

	// Get metrics from last hour
	var responseTime float64
	var errorCount int64

	err := db.DB.QueryRow(
		`SELECT AVG(response_time) as avg_response, SUM(error_count) as total_errors
		 FROM service_metrics WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)`,
	).Scan(&responseTime, &errorCount)

	if err != nil && err != sql.ErrNoRows {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch summary"})
		return
	}

	summary.AverageResponseTime = responseTime
	summary.ErrorRate = float64(errorCount)

	// Get today's metrics
	var revenue sql.NullFloat64
	var transactions int64

	db.DB.QueryRow(
		`SELECT SUM(total_revenue), COUNT(*) FROM revenue_analytics WHERE day = CURDATE()`,
	).Scan(&revenue, &transactions)

	if revenue.Valid {
		summary.TotalRevenue = revenue.Float64
	}
	summary.TotalTransactions = transactions

	c.JSON(http.StatusOK, summary)
}

// HealthCheck checks service health
func HealthCheck(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{
		"status":  "healthy",
		"service": "analytics-service",
		"time":    time.Now(),
	})
}
