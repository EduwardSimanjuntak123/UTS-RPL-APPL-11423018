package db

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
	"github.com/meditrack/user-service/config"
)

var DB *sql.DB

func InitDB(cfg *config.Config) (*sql.DB, error) {
	dsn := config.GetDSN(cfg)
	
	db, err := sql.Open("mysql", dsn)
	if err != nil {
		log.Printf("Error opening database: %v", err)
		return nil, err
	}

	// Test connection
	err = db.Ping()
	if err != nil {
		log.Printf("Error pinging database: %v", err)
		return nil, err
	}

	// Set connection pool settings
	db.SetMaxOpenConns(25)
	db.SetMaxIdleConns(5)

	log.Println("✓ Database connected successfully")
	DB = db
	return db, nil
}

func CloseDB() {
	if DB != nil {
		DB.Close()
		log.Println("✓ Database connection closed")
	}
}

func CreateTables() error {
	tables := []string{
		`CREATE TABLE IF NOT EXISTS users (
			id INT AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			email VARCHAR(255) UNIQUE NOT NULL,
			password VARCHAR(255) NOT NULL,
			phone VARCHAR(20),
			address TEXT,
			role ENUM('patient', 'doctor', 'pharmacist', 'admin') NOT NULL DEFAULT 'patient',
			status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
			specialty VARCHAR(255),
			license_number VARCHAR(255),
			insurance_provider VARCHAR(255),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			INDEX idx_email (email),
			INDEX idx_role (role),
			INDEX idx_status (status)
		)`,

		`CREATE TABLE IF NOT EXISTS roles (
			id INT AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(100) UNIQUE NOT NULL,
			description TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)`,

		`CREATE TABLE IF NOT EXISTS permissions (
			id INT AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(100) UNIQUE NOT NULL,
			resource VARCHAR(100),
			action VARCHAR(100),
			description TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)`,

		`CREATE TABLE IF NOT EXISTS role_permissions (
			role_id INT NOT NULL,
			permission_id INT NOT NULL,
			PRIMARY KEY (role_id, permission_id),
			FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
			FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
		)`,

		`CREATE TABLE IF NOT EXISTS audit_logs (
			id INT AUTO_INCREMENT PRIMARY KEY,
			user_id INT,
			action VARCHAR(100) NOT NULL,
			resource VARCHAR(100),
			old_values JSON,
			new_values JSON,
			ip_address VARCHAR(45),
			user_agent VARCHAR(500),
			timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_user_id (user_id),
			INDEX idx_timestamp (timestamp)
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
