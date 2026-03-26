package models

import "time"

type ServiceMetric struct {
	ID            int       `json:"id"`
	ServiceName   string    `json:"service_name"`
	Timestamp     time.Time `json:"timestamp"`
	ResponseTime  float64   `json:"response_time"`
	RequestCount  int64     `json:"request_count"`
	ErrorCount    int64     `json:"error_count"`
	ThroughputKBs float64   `json:"throughput_kbs"`
	Status        string    `json:"status"`
	CreatedAt     time.Time `json:"created_at"`
}

type UserAnalytics struct {
	ID              int       `json:"id"`
	Day             time.Time `json:"day"`
	TotalUsers      int64     `json:"total_users"`
	NewUsers        int64     `json:"new_users"`
	ActiveUsers     int64     `json:"active_users"`
	FeatureUsage    string    `json:"feature_usage"`
	CreatedAt       time.Time `json:"created_at"`
}

type AppointmentAnalytics struct {
	ID                    int       `json:"id"`
	Day                   time.Time `json:"day"`
	TotalAppointments     int64     `json:"total_appointments"`
	CompletedAppointments int64     `json:"completed_appointments"`
	CancelledAppointments int64     `json:"cancelled_appointments"`
	AverageWaitTime       float64   `json:"average_wait_time"`
	CreatedAt             time.Time `json:"created_at"`
}

type RevenueAnalytics struct {
	ID              int       `json:"id"`
	Day             time.Time `json:"day"`
	TotalRevenue    float64   `json:"total_revenue"`
	PaymentSuccess  int64     `json:"payment_success"`
	PaymentFailed   int64     `json:"payment_failed"`
	AveragePayment  float64   `json:"average_payment"`
	CreatedAt       time.Time `json:"created_at"`
}

type HealthIndicator struct {
	ID                    int       `json:"id"`
	Timestamp             time.Time `json:"timestamp"`
	ServiceName           string    `json:"service_name"`
	Status                string    `json:"status"`
	ResponseTime          float64   `json:"response_time"`
	ErrorRate             float64   `json:"error_rate"`
	DatabaseConnectionOk  bool      `json:"database_connection_ok"`
	CreatedAt             time.Time `json:"created_at"`
}

type SystemAlert struct {
	ID          int       `json:"id"`
	AlertType   string    `json:"alert_type"`
	Severity    string    `json:"severity"`
	Message     string    `json:"message"`
	ServiceName string    `json:"service_name"`
	Status      string    `json:"status"`
	ResolvedAt  *time.Time `json:"resolved_at"`
	CreatedAt   time.Time `json:"created_at"`
}

type DashboardSummary struct {
	ActiveUsers          int64   `json:"active_users"`
	TotalTransactions    int64   `json:"total_transactions"`
	TotalRevenue         float64 `json:"total_revenue"`
	SystemUptime         float64 `json:"system_uptime"`
	AverageResponseTime  float64 `json:"average_response_time"`
	ErrorRate            float64 `json:"error_rate"`
	PendingAppointments  int64   `json:"pending_appointments"`
	PendingPayments      int64   `json:"pending_payments"`
}
