package config

import (
	"fmt"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	ServiceName string
	ServicePort int
	DBHost      string
	DBPort      int
	DBUser      string
	DBPassword  string
	DBName      string
	JWTSecret   string
	JWTExpiry   int
	Environment string
	LogLevel    string
}

var Cfg *Config

func LoadConfig() (*Config, error) {
	// Load .env file
	_ = godotenv.Load()

	port, err := strconv.Atoi(getEnv("SERVICE_PORT", "3001"))
	if err != nil {
		port = 3001
	}

	dbPort, err := strconv.Atoi(getEnv("DB_PORT", "3306"))
	if err != nil {
		dbPort = 3306
	}

	jwtExpiry, err := strconv.Atoi(getEnv("JWT_EXPIRY", "24"))
	if err != nil {
		jwtExpiry = 24
	}

	cfg := &Config{
		ServiceName: getEnv("SERVICE_NAME", "user-service"),
		ServicePort: port,
		DBHost:      getEnv("DB_HOST", "localhost"),
		DBPort:      dbPort,
		DBUser:      getEnv("DB_USER", "root"),
		DBPassword:  getEnv("DB_PASSWORD", ""),
		DBName:      getEnv("DB_NAME", "meditrack_users"),
		JWTSecret:   getEnv("JWT_SECRET", "change_me_in_production"),
		JWTExpiry:   jwtExpiry,
		Environment: getEnv("ENVIRONMENT", "development"),
		LogLevel:    getEnv("LOG_LEVEL", "info"),
	}

	Cfg = cfg
	return cfg, nil
}

func GetDSN(cfg *Config) string {
	return fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?parseTime=true",
		cfg.DBUser,
		cfg.DBPassword,
		cfg.DBHost,
		cfg.DBPort,
		cfg.DBName,
	)
}

func getEnv(key, defaultValue string) string {
	value := os.Getenv(key)
	if value == "" {
		return defaultValue
	}
	return value
}
