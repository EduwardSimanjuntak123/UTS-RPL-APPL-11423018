package logging

import (
	"fmt"
	"os"
	"time"

	"github.com/sirupsen/logrus"
)

var Logger *logrus.Logger

// Initialize sets up structured logging
func Initialize() {
	Logger = logrus.New()

	// Set output format
	Logger.SetFormatter(&logrus.JSONFormatter{
		TimestampFormat: time.RFC3339,
	})

	// Set output to stdout
	Logger.SetOutput(os.Stdout)

	// Set log level
	logLevel := os.Getenv("LOG_LEVEL")
	if logLevel == "" {
		logLevel = "info"
	}

	level, err := logrus.ParseLevel(logLevel)
	if err != nil {
		level = logrus.InfoLevel
	}
	Logger.SetLevel(level)
}

// LogWithContext logs with context fields
func LogWithContext(correlationID, serviceName string, level logrus.Level, message string, fields map[string]interface{}) {
	if fields == nil {
		fields = make(map[string]interface{})
	}

	fields["correlation_id"] = correlationID
	fields["service"] = serviceName
	fields["timestamp"] = time.Now()

	entry := Logger.WithFields(fields)

	switch level {
	case logrus.InfoLevel:
		entry.Info(message)
	case logrus.ErrorLevel:
		entry.Error(message)
	case logrus.DebugLevel:
		entry.Debug(message)
	case logrus.WarnLevel:
		entry.Warn(message)
	default:
		entry.Info(message)
	}
}

// LogError logs error with context
func LogError(correlationID, serviceName, message string, err error) {
	fields := map[string]interface{}{
		"error": fmt.Sprintf("%v", err),
	}
	LogWithContext(correlationID, serviceName, logrus.ErrorLevel, message, fields)
}

// LogInfo logs info with context
func LogInfo(correlationID, serviceName, message string, fields map[string]interface{}) {
	LogWithContext(correlationID, serviceName, logrus.InfoLevel, message, fields)
}

// LogDebug logs debug with context
func LogDebug(correlationID, serviceName, message string, fields map[string]interface{}) {
	LogWithContext(correlationID, serviceName, logrus.DebugLevel, message, fields)
}
