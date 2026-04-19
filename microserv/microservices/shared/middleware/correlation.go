package middleware

import (
	"github.com/gin-gonic/gin"
	"github.com/google/uuid"
)

// CorrelationIDMiddleware adds correlation ID to all requests
func CorrelationIDMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		correlationID := c.GetHeader("X-Correlation-ID")

		// Generate if not provided
		if correlationID == "" {
			correlationID = uuid.New().String()
		}

		// Set in context
		c.Set("correlation_id", correlationID)

		// Set in response header
		c.Header("X-Correlation-ID", correlationID)

		c.Next()
	}
}

// GetCorrelationID retrieves correlation ID from context
func GetCorrelationID(c *gin.Context) string {
	id, exists := c.Get("correlation_id")
	if !exists {
		return ""
	}
	return id.(string)
}
