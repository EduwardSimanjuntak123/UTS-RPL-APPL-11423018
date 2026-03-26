package db

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
	"github.com/meditrack/appointment-service/config"
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
		`CREATE TABLE IF NOT EXISTS appointments (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			doctor_id INT NOT NULL,
			appointment_date DATETIME NOT NULL,
			status ENUM('scheduled', 'confirmed', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
			type VARCHAR(100),
			location VARCHAR(255),
			duration INT,
			description TEXT,
			notes TEXT,
			cancellation_reason VARCHAR(255),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_patient (patient_id),
			INDEX idx_doctor (doctor_id),
			INDEX idx_date (appointment_date),
			INDEX idx_status (status)
		)`,

		`CREATE TABLE IF NOT EXISTS appointment_slots (
			id INT AUTO_INCREMENT PRIMARY KEY,
			doctor_id INT NOT NULL,
			start_time DATETIME NOT NULL,
			end_time DATETIME NOT NULL,
			is_available BOOLEAN DEFAULT TRUE,
			booked_by_id INT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_doctor (doctor_id),
			INDEX idx_start_time (start_time)
		)`,

		`CREATE TABLE IF NOT EXISTS appointment_notifications (
			id INT AUTO_INCREMENT PRIMARY KEY,
			appointment_id INT NOT NULL,
			type ENUM('sms', 'email', 'push'),
			recipient VARCHAR(255),
			message TEXT,
			sent_at TIMESTAMP,
			status VARCHAR(50),
			FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
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
