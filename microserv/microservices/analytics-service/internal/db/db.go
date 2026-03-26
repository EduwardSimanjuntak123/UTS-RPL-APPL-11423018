package db

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
	"github.com/meditrack/analytics-service/config"
)

var DB *sql.DB

func InitDB(cfg *config.Config) (*sql.DB, error) {
	dsn := config.GetDSN(cfg)
	
	db, err := sql.Open("mysql", dsn)
	if err != nil {
		log.Printf("Error opening database: %v", err)
		return nil, err
	}

	err = db.Ping()
	if err != nil {
		log.Printf("Error pinging database: %v", err)
		return nil, err
	}

	db.SetMaxOpenConns(25)
	db.SetMaxIdleConns(5)

	log.Println("✓ Database connected successfully")
	DB = db
	return db, nil
}

func CloseDB() {
	if DB != nil {
		DB.Close()
		log.Println("✓ Database closed")
	}
}

func CreateTables() error {
	tables := []string{
		`CREATE TABLE IF NOT EXISTS service_metrics (
			id INT AUTO_INCREMENT PRIMARY KEY,
			service_name VARCHAR(100) NOT NULL,
			timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			response_time FLOAT,
			request_count BIGINT DEFAULT 0,
			error_count BIGINT DEFAULT 0,
			throughput_kbs FLOAT,
			status ENUM('healthy', 'degraded', 'down') DEFAULT 'healthy',
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_service (service_name),
			INDEX idx_timestamp (timestamp)
		)`,

		`CREATE TABLE IF NOT EXISTS user_analytics (
			id INT AUTO_INCREMENT PRIMARY KEY,
			day DATE NOT NULL,
			total_users BIGINT DEFAULT 0,
			new_users BIGINT DEFAULT 0,
			active_users BIGINT DEFAULT 0,
			feature_usage JSON,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY uk_day (day)
		)`,

		`CREATE TABLE IF NOT EXISTS appointment_analytics (
			id INT AUTO_INCREMENT PRIMARY KEY,
			day DATE NOT NULL,
			total_appointments BIGINT DEFAULT 0,
			completed_appointments BIGINT DEFAULT 0,
			cancelled_appointments BIGINT DEFAULT 0,
			average_wait_time FLOAT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY uk_day (day)
		)`,

		`CREATE TABLE IF NOT EXISTS revenue_analytics (
			id INT AUTO_INCREMENT PRIMARY KEY,
			day DATE NOT NULL,
			total_revenue DECIMAL(15, 2) DEFAULT 0,
			payment_success BIGINT DEFAULT 0,
			payment_failed BIGINT DEFAULT 0,
			average_payment DECIMAL(12, 2),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY uk_day (day)
		)`,

		`CREATE TABLE IF NOT EXISTS health_indicators (
			id INT AUTO_INCREMENT PRIMARY KEY,
			timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			service_name VARCHAR(100),
			status ENUM('healthy', 'degraded', 'unhealthy'),
			response_time FLOAT,
			error_rate FLOAT,
			database_connection_ok BOOLEAN,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_service (service_name),
			INDEX idx_timestamp (timestamp)
		)`,

		`CREATE TABLE IF NOT EXISTS system_alerts (
			id INT AUTO_INCREMENT PRIMARY KEY,
			alert_type VARCHAR(100),
			severity ENUM('info', 'warning', 'critical'),
			message TEXT,
			service_name VARCHAR(100),
			status ENUM('active', 'acknowledged', 'resolved') DEFAULT 'active',
			resolved_at TIMESTAMP NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_severity (severity),
			INDEX idx_status (status)
		)`,
	}

	for _, table := range tables {
		_, err := DB.Exec(table)
		if err != nil {
			return fmt.Errorf("error creating table: %v", err)
		}
	}

	log.Println("✓ All tables created successfully")
	return nil
}
