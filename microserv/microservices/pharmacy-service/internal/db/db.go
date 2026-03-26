package db

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
	"github.com/meditrack/pharmacy-service/config"
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
		`CREATE TABLE IF NOT EXISTS drugs (
			id INT AUTO_INCREMENT PRIMARY KEY,
			drug_name VARCHAR(255) NOT NULL,
			generic_name VARCHAR(255),
			manufacturer VARCHAR(255),
			dosage VARCHAR(100) NOT NULL,
			form_type VARCHAR(100),
			price DECIMAL(10, 2) NOT NULL,
			license_number VARCHAR(100),
			expiry_date DATE,
			storage_condition VARCHAR(255),
			description TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY uk_license (license_number),
			INDEX idx_drug_name (drug_name)
		)`,

		`CREATE TABLE IF NOT EXISTS drug_stocks (
			id INT AUTO_INCREMENT PRIMARY KEY,
			drug_id INT NOT NULL,
			quantity INT NOT NULL DEFAULT 0,
			reorder_level INT DEFAULT 50,
			location VARCHAR(255),
			last_restocked TIMESTAMP,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			FOREIGN KEY (drug_id) REFERENCES drugs(id) ON DELETE CASCADE,
			UNIQUE KEY uk_drug_location (drug_id, location),
			INDEX idx_reorder_level (reorder_level)
		)`,

		`CREATE TABLE IF NOT EXISTS pharmacy_orders (
			id INT AUTO_INCREMENT PRIMARY KEY,
			patient_id INT NOT NULL,
			prescription_id INT NOT NULL,
			order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			status ENUM('pending', 'processing', 'ready', 'picked_up', 'delivered', 'cancelled') DEFAULT 'pending',
			total_amount DECIMAL(10, 2),
			payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
			ready_date TIMESTAMP NULL,
			pickup_date TIMESTAMP NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_patient (patient_id),
			INDEX idx_status (status),
			INDEX idx_date (order_date)
		)`,

		`CREATE TABLE IF NOT EXISTS order_items (
			id INT AUTO_INCREMENT PRIMARY KEY,
			order_id INT NOT NULL,
			drug_id INT NOT NULL,
			quantity INT NOT NULL,
			unit_price DECIMAL(10, 2) NOT NULL,
			subtotal DECIMAL(10, 2),
			FOREIGN KEY (order_id) REFERENCES pharmacy_orders(id) ON DELETE CASCADE,
			FOREIGN KEY (drug_id) REFERENCES drugs(id)
		)`,

		`CREATE TABLE IF NOT EXISTS drug_inventory_log (
			id INT AUTO_INCREMENT PRIMARY KEY,
			drug_id INT NOT NULL,
			quantity_added INT DEFAULT 0,
			quantity_consumed INT DEFAULT 0,
			transaction_type ENUM('purchase', 'consumption', 'adjustment') DEFAULT 'purchase',
			reason VARCHAR(255),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (drug_id) REFERENCES drugs(id) ON DELETE CASCADE,
			INDEX idx_drug (drug_id),
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
