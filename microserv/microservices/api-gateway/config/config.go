package config

import (
	"os"
	"strconv"
	"strings"

	"github.com/joho/godotenv"
)

type Config struct {
	ServiceName          string
	ServicePort          int
	UserServiceURL       string
	AppointmentServiceURL string
	MedicalServiceURL    string
	PharmacyServiceURL   string
	PaymentServiceURL    string
	AnalyticsServiceURL  string
	JWTSecret            string
	TokenExpiry          string
	RateLimit            int
	RateLimitWindow      string
	Environment          string
	LogLevel             string
	CORSAllowedOrigins   []string
}

var Cfg *Config

func LoadConfig() (*Config, error) {
	_ = godotenv.Load()

	port, _ := strconv.Atoi(getEnv("SERVICE_PORT", "3000"))
	rateLimit, _ := strconv.Atoi(getEnv("RATE_LIMIT", "100"))

	corsOrigins := strings.Split(getEnv("CORS_ALLOWED_ORIGINS", "http://localhost:3000"), ",")

	cfg := &Config{
		ServiceName:           getEnv("SERVICE_NAME", "api-gateway"),
		ServicePort:           port,
		UserServiceURL:        getEnv("USER_SERVICE_URL", "http://localhost:3001"),
		AppointmentServiceURL: getEnv("APPOINTMENT_SERVICE_URL", "http://localhost:3002"),
		MedicalServiceURL:     getEnv("MEDICAL_SERVICE_URL", "http://localhost:3003"),
		PharmacyServiceURL:    getEnv("PHARMACY_SERVICE_URL", "http://localhost:3004"),
		PaymentServiceURL:     getEnv("PAYMENT_SERVICE_URL", "http://localhost:3005"),
		AnalyticsServiceURL:   getEnv("ANALYTICS_SERVICE_URL", "http://localhost:3006"),
		JWTSecret:             getEnv("JWT_SECRET", "dev-secret-key"),
		TokenExpiry:           getEnv("TOKEN_EXPIRY", "24h"),
		RateLimit:             rateLimit,
		RateLimitWindow:       getEnv("RATE_LIMIT_WINDOW", "1m"),
		Environment:           getEnv("ENVIRONMENT", "development"),
		LogLevel:              getEnv("LOG_LEVEL", "info"),
		CORSAllowedOrigins:    corsOrigins,
	}

	Cfg = cfg
	return cfg, nil
}

func getEnv(key, defaultValue string) string {
	value := os.Getenv(key)
	if value == "" {
		return defaultValue
	}
	return value
}
