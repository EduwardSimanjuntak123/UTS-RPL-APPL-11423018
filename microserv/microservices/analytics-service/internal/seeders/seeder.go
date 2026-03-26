package seeders

import (
	"log"
	"time"

	"github.com/google/uuid"
	"gorm.io/gorm"
)

type ServiceMetric struct {
	ID            string `gorm:"primaryKey"`
	ServiceName   string
	MetricType    string
	Value         float64
	Timestamp     int64
	Tags          string
	CreatedAt     int64
	UpdatedAt     int64
}

type HealthIndicator struct {
	ID              string `gorm:"primaryKey"`
	ServiceName     string
	Status          string
	UptimePercent   float64
	ResponseTime    float64
	ErrorRate       float64
	ActiveRequests  int
	LastCheckedAt   int64
	CreatedAt       int64
	UpdatedAt       int64
}

type SystemAlert struct {
	ID          string `gorm:"primaryKey"`
	Service     string
	AlertType   string
	Severity    string
	Message     string
	Status      string
	ResolvedAt  int64
	CreatedAt   int64
	UpdatedAt   int64
}

// Seed analytics data
func SeedAnalytics(db *gorm.DB) error {
	log.Println("🌱 Starting analytics seeding...")

	if err := db.AutoMigrate(&ServiceMetric{}, &HealthIndicator{}, &SystemAlert{}); err != nil {
		log.Printf("❌ Migration failed: %v\n", err)
		return err
	}

	now := time.Now().Unix()

	services := []string{
		"user-service",
		"appointment-service",
		"medical-service",
		"pharmacy-service",
		"payment-service",
		"api-gateway",
	}

	// 1. Create Service Metrics
	metricTypes := []string{
		"response_time",
		"throughput",
		"error_rate",
		"cpu_usage",
		"memory_usage",
	}

	for _, service := range services {
		for i := 0; i < 30; i++ {
			for _, metricType := range metricTypes {
				var value float64

				switch metricType {
				case "response_time":
					value = 100.0 + float64((i%10)*50)
				case "throughput":
					value = 1000.0 + float64((i%5)*200)
				case "error_rate":
					value = float64(i%3) * 0.5
				case "cpu_usage":
					value = 10.0 + float64((i%7)*10)
				case "memory_usage":
					value = 20.0 + float64((i%6)*15)
				}

				metric := ServiceMetric{
					ID:          uuid.New().String(),
					ServiceName: service,
					MetricType:  metricType,
					Value:       value,
					Timestamp:   now - int64((i)*3600),
					Tags:        "service=" + service,
					CreatedAt:   now - int64((i)*3600),
					UpdatedAt:   now - int64((i)*3600),
				}

				if err := db.Create(&metric).Error; err != nil {
					log.Printf("⚠️  Warning: Could not create metric: %v\n", err)
				}
			}
		}
		log.Printf("✅ Created metrics for service: %s\n", service)
	}

	// 2. Create Health Indicators
	healthStatuses := []string{"healthy", "degraded", "down"}

	for _, service := range services {
		healthIndicator := HealthIndicator{
			ID:             uuid.New().String(),
			ServiceName:    service,
			Status:         healthStatuses[0], // healthy
			UptimePercent:  99.9,
			ResponseTime:   150.5,
			ErrorRate:      0.1,
			ActiveRequests: 150 + (len(service) % 5 * 50),
			LastCheckedAt:  now,
			CreatedAt:      now,
			UpdatedAt:      now,
		}

		if err := db.Create(&healthIndicator).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create health indicator: %v\n", err)
		} else {
			log.Printf("✅ Created health indicator for: %s\n", service)
		}
	}

	// 3. Create System Alerts
	alertTypes := []string{
		"high_error_rate",
		"slow_response",
		"service_down",
		"high_memory",
		"high_cpu",
	}

	severities := []string{"info", "warning", "critical"}

	for i := 0; i < 20; i++ {
		service := services[i%len(services)]
		alertType := alertTypes[i%len(alertTypes)]
		severity := severities[i%len(severities)]

		alert := SystemAlert{
			ID:        uuid.New().String(),
			Service:   service,
			AlertType: alertType,
			Severity:  severity,
			Message:   "Alert: " + alertType + " detected on " + service,
			Status:    []string{"open", "resolved"}[i%2],
			ResolvedAt: func() int64 {
				if i%2 == 0 {
					return now + 3600
				}
				return 0
			}(),
			CreatedAt: now - int64((i)*3600),
			UpdatedAt: now - int64((i)*3600),
		}

		if err := db.Create(&alert).Error; err != nil {
			log.Printf("⚠️  Warning: Could not create alert: %v\n", err)
		} else {
			log.Printf("✅ Created alert: %s\n", alert.ID)
		}
	}

	log.Println("✅ Analytics seeding completed successfully!")
	return nil
}
