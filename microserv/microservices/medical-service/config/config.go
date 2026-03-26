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
	Environment string
	LogLevel    string
}

var Cfg *Config

func LoadConfig() (*Config, error) {
	_ = godotenv.Load()

	port, _ := strconv.Atoi(getEnv("SERVICE_PORT", "3003"))
	dbPort, _ := strconv.Atoi(getEnv("DB_PORT", "3307"))

	cfg := &Config{
		ServiceName: getEnv("SERVICE_NAME", "medical-service"),
		ServicePort: port,
		DBHost:      getEnv("DB_HOST", "localhost"),
		DBPort:      dbPort,
		DBUser:      getEnv("DB_USER", "root"),
		DBPassword:  getEnv("DB_PASSWORD", ""),
		DBName:      getEnv("DB_NAME", "meditrack_medical"),
		Environment: getEnv("ENVIRONMENT", "development"),
		LogLevel:    getEnv("LOG_LEVEL", "info"),
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
