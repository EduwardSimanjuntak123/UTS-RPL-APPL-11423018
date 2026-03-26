package database

import (
	"log"

	"gorm.io/driver/postgres"
	"gorm.io/gorm"
)

// InitDB initializes database connection and runs seeders
func InitDB(dsn string) (*gorm.DB, error) {
	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Printf("❌ Failed to connect to database: %v\n", err)
		return nil, err
	}

	log.Println("✅ Database connected successfully")
	return db, nil
}

// CheckAndSeed checks if database needs seeding and runs seeders
func CheckAndSeed(db *gorm.DB, serviceType string) error {
	// Check if database is empty
	var count int64
	
	switch serviceType {
	case "user-service":
		db.Table("users").Count(&count)
		if count == 0 {
			log.Println("🌱 Database is empty, running user-service seeder...")
			// Import and run seeder here
		}
		
	case "appointment-service":
		db.Table("appointments").Count(&count)
		if count == 0 {
			log.Println("🌱 Database is empty, running appointment-service seeder...")
			// Import and run seeder here
		}
		
	case "pharmacy-service":
		db.Table("pharmacies").Count(&count)
		if count == 0 {
			log.Println("🌱 Database is empty, running pharmacy-service seeder...")
			// Import and run seeder here
		}
		
	case "medical-service":
		db.Table("prescriptions").Count(&count)
		if count == 0 {
			log.Println("🌱 Database is empty, running medical-service seeder...")
			// Import and run seeder here
		}
		
	case "payment-service":
		db.Table("payments").Count(&count)
		if count == 0 {
			log.Println("🌱 Database is empty, running payment-service seeder...")
			// Import and run seeder here
		}
		
	case "analytics-service":
		db.Table("service_metrics").Count(&count)
		if count == 0 {
			log.Println("🌱 Database is empty, running analytics-service seeder...")
			// Import and run seeder here
		}
	}

	return nil
}
