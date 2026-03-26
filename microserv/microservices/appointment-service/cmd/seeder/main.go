package main

import (
	"fmt"
	"log"
	"os"
	"github.com/meditrack/appointment-service/internal/seeders"

	"github.com/joho/godotenv"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

func main() {
	// Load .env file
	if err := godotenv.Load(".env"); err != nil {
		log.Println("⚠️  No .env file found, using environment variables")
	}

	// Database configuration from .env
	dbHost := os.Getenv("DB_HOST")
	dbPort := os.Getenv("DB_PORT")
	dbUser := os.Getenv("DB_USER")
	dbPassword := os.Getenv("DB_PASSWORD")
	dbName := os.Getenv("DB_NAME")

	if dbHost == "" {
		dbHost = "localhost"
	}
	if dbPort == "" {
		dbPort = "3307"
	}
	if dbUser == "" {
		dbUser = "root"
	}
	if dbName == "" {
		dbName = "meditrack_appointments"
	}

	// MySQL DSN format: user:password@tcp(host:port)/dbname?charset=utf8mb4&parseTime=True&loc=Local
	dsn := dbUser + ":" + dbPassword + "@tcp(" + dbHost + ":" + dbPort + ")/" + dbName + "?charset=utf8mb4&parseTime=True&loc=Local"

	log.Println("🔗 Connecting to MySQL database...")
	log.Printf("   Host: %s, Port: %s, User: %s, Database: %s\n", dbHost, dbPort, dbUser, dbName)

	// Connect to database
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatalf("❌ Failed to connect to database: %v\n", err)
	}

	log.Println("✅ Connected to appointment-service database")

	// Run seeder
	if err := seeders.SeedAppointments(db); err != nil {
		log.Fatalf("❌ Seeding failed: %v\n", err)
	}

	fmt.Println("\n✅ Appointment Service seeding completed successfully!")
}
