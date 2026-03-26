package db

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
	"github.com/meditrack/payment-service/config"
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
		`CREATE TABLE IF NOT EXISTS invoices (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			invoice_number VARCHAR(50) UNIQUE,
			service_type VARCHAR(100),
			total_amount DECIMAL(12, 2) NOT NULL,
			paid_amount DECIMAL(12, 2) DEFAULT 0,
			due_date DATE,
			status ENUM('draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
			item_details JSON,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_patient (patient_id),
			INDEX idx_status (status),
			INDEX idx_created (created_at)
		)`,

		`CREATE TABLE IF NOT EXISTS payments (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			invoice_id INT NOT NULL,
			amount DECIMAL(12, 2) NOT NULL,
			payment_method VARCHAR(50),
			transaction_id VARCHAR(100),
			status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
			description TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
			INDEX idx_patient (patient_id),
			INDEX idx_invoice (invoice_id),
			INDEX idx_status (status),
			INDEX idx_transaction (transaction_id)
		)`,

		`CREATE TABLE IF NOT EXISTS insurance_claims (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			insurance_id INT NOT NULL,
			invoice_id INT NOT NULL,
			claim_amount DECIMAL(12, 2) NOT NULL,
			claim_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			approval_date TIMESTAMP NULL,
			status ENUM('submitted', 'under_review', 'approved', 'rejected', 'paid') DEFAULT 'submitted',
			reject_reason TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
			INDEX idx_patient (patient_id),
			INDEX idx_insurance (insurance_id),
			INDEX idx_status (status)
		)`,

		`CREATE TABLE IF NOT EXISTS payment_proofs (
			id INT AUTO_INCREMENT PRIMARY KEY,
			payment_id INT NOT NULL,
			proof_url VARCHAR(500),
			file_type VARCHAR(50),
			uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
		)`,

		`CREATE TABLE IF NOT EXISTS refunds (
			id INT AUTO_INCREMENT PRIMARY KEY,
			payment_id INT NOT NULL,
			refund_amount DECIMAL(12, 2) NOT NULL,
			reason VARCHAR(255),
			status ENUM('pending', 'processed', 'completed', 'failed') DEFAULT 'pending',
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
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
