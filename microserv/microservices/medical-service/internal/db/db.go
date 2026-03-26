package db

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
	"github.com/meditrack/medical-service/config"
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
		`CREATE TABLE IF NOT EXISTS medical_records (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			doctor_id INT NOT NULL,
			record_type VARCHAR(100),
			diagnosis TEXT NOT NULL,
			treatment TEXT,
			notes TEXT,
			attachment VARCHAR(255),
			is_confidential BOOLEAN DEFAULT FALSE,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_patient (patient_id),
			INDEX idx_doctor (doctor_id),
			INDEX idx_date (created_at)
		)`,

		`CREATE TABLE IF NOT EXISTS prescriptions (
			id INT AUTO_INCREMENT PRIMARY KEY,
			medical_record_id INT NOT NULL,
			patient_id INT NOT NULL,
			doctor_id INT NOT NULL,
			drug_name VARCHAR(255) NOT NULL,
			dosage VARCHAR(100) NOT NULL,
			frequency VARCHAR(100) NOT NULL,
			duration INT,
			quantity INT,
			instructions TEXT,
			status ENUM('pending', 'fulfilled', 'cancelled') DEFAULT 'pending',
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE CASCADE,
			INDEX idx_patient (patient_id),
			INDEX idx_doctor (doctor_id),
			INDEX idx_status (status)
		)`,

		`CREATE TABLE IF NOT EXISTS lab_results (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			doctor_id INT NOT NULL,
			medical_record_id INT,
			test_name VARCHAR(255) NOT NULL,
			result TEXT NOT NULL,
			unit VARCHAR(50),
			reference_range VARCHAR(100),
			status ENUM('pending', 'completed', 'abnormal') DEFAULT 'pending',
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE SET NULL,
			INDEX idx_patient (patient_id),
			INDEX idx_doctor (doctor_id),
			INDEX idx_status (status)
		)`,

		`CREATE TABLE IF NOT EXISTS clinical_notes (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			doctor_id INT NOT NULL,
			appointment_id INT,
			note TEXT NOT NULL,
			vitals VARCHAR(255),
			symptoms TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_patient (patient_id),
			INDEX idx_doctor (doctor_id),
			INDEX idx_date (created_at)
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
