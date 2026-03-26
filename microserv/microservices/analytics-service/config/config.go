package config

import (
	"fmt"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	ServiceName              string
	ServicePort              int
	DBHost                   string
	DBPort                   int
	DBUser                   string
	DBPassword               string
	DBName                   string
	UserServiceURL           string
	AppointmentServiceURL    string
	MedicalServiceURL        string
	PharmacyServiceURL       string
	PaymentServiceURL        string
	Environment              string
	LogLevel                 string
}

var Cfg *Config

func LoadConfig() (*Config, error) {
	_ = godotenv.Load()

	port, _ := strconv.Atoi(getEnv("SERVICE_PORT", "3006"))
	dbPort, _ := strconv.Atoi(getEnv("DB_PORT", "3307"))

	cfg := &Config{
		ServiceName:           getEnv("SERVICE_NAME", "analytics-service"),
		ServicePort:           port,
		DBHost:                getEnv("DB_HOST", "localhost"),
		DBPort:                dbPort,
		DBUser:                getEnv("DB_USER", "root"),
		DBPassword:            getEnv("DB_PASSWORD", ""),
		DBName:                getEnv("DB_NAME", "meditrack_analytics"),
		UserServiceURL:        getEnv("USER_SERVICE_URL", "http://localhost:3001"),
		AppointmentServiceURL: getEnv("APPOINTMENT_SERVICE_URL", "http://localhost:3002"),
		MedicalServiceURL:     getEnv("MEDICAL_SERVICE_URL", "http://localhost:3003"),
		PharmacyServiceURL:    getEnv("PHARMACY_SERVICE_URL", "http://localhost:3004"),
		PaymentServiceURL:     getEnv("PAYMENT_SERVICE_URL", "http://localhost:3005"),
		Environment:           getEnv("ENVIRONMENT", "development"),
		LogLevel:              getEnv("LOG_LEVEL", "info"),
	}

	Cfg = cfg
	return cfg, nil
}

func GetDSN(cfg *Config) string {
	return fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?parseTime=true",
		cfg.DBUser, cfg.DBPassword, cfg.DBHost, cfg.DBPort, cfg.DBName)
}

func getEnv(key, defaultValue string) string {
	value := os.Getenv(key)
	if value == "" {
		return defaultValue
	}
	return value
}
