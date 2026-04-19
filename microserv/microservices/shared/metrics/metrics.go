package metrics

import (
	"fmt"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/prometheus/client_golang/prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"
)

var (
	// HTTP metrics
	RequestDuration = prometheus.NewHistogramVec(
		prometheus.HistogramOpts{
			Name: "http_request_duration_seconds",
			Help: "HTTP request latency in seconds",
		},
		[]string{"method", "path", "status"},
	)

	RequestCount = prometheus.NewCounterVec(
		prometheus.CounterOpts{
			Name: "http_requests_total",
			Help: "Total HTTP requests",
		},
		[]string{"method", "path", "status"},
	)

	// Database metrics
	DBQueryDuration = prometheus.NewHistogramVec(
		prometheus.HistogramOpts{
			Name: "db_query_duration_seconds",
			Help: "Database query latency in seconds",
		},
		[]string{"operation", "table"},
	)

	DBErrors = prometheus.NewCounterVec(
		prometheus.CounterOpts{
			Name: "db_errors_total",
			Help: "Total database errors",
		},
		[]string{"operation", "table"},
	)

	// Service health
	ServiceHealth = prometheus.NewGaugeVec(
		prometheus.GaugeOpts{
			Name: "service_health",
			Help: "Service health status (1=healthy, 0=unhealthy)",
		},
		[]string{"service"},
	)
)

// RegisterMetrics registers all metrics
func RegisterMetrics() {
	prometheus.MustRegister(RequestDuration)
	prometheus.MustRegister(RequestCount)
	prometheus.MustRegister(DBQueryDuration)
	prometheus.MustRegister(DBErrors)
	prometheus.MustRegister(ServiceHealth)
}

// MetricsHandler returns Prometheus metrics handler
func MetricsHandler() http.Handler {
	return promhttp.Handler()
}

// MetricsMiddleware records HTTP metrics
func MetricsMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		start := time.Now()

		c.Next()

		duration := time.Since(start).Seconds()
		status := fmt.Sprintf("%d", c.Writer.Status())

		RequestDuration.WithLabelValues(
			c.Request.Method,
			c.Request.URL.Path,
			status,
		).Observe(duration)

		RequestCount.WithLabelValues(
			c.Request.Method,
			c.Request.URL.Path,
			status,
		).Inc()
	}
}

// RecordDBQuery records database query metrics
func RecordDBQuery(operation, table string, duration time.Duration, err error) {
	DBQueryDuration.WithLabelValues(operation, table).Observe(duration.Seconds())

	if err != nil {
		DBErrors.WithLabelValues(operation, table).Inc()
	}
}

// SetServiceHealth sets service health status
func SetServiceHealth(serviceName string, healthy bool) {
	value := float64(0)
	if healthy {
		value = 1
	}
	ServiceHealth.WithLabelValues(serviceName).Set(value)
}
