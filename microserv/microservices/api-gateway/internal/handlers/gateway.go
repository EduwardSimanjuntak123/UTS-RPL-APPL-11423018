package handlers

import (
	"bytes"
	"fmt"
	"io"
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/api-gateway/config"
)

// ProxyRequest proxies the request to the appropriate service
func ProxyRequest(targetURL string) gin.HandlerFunc {
	return func(c *gin.Context) {
		// Read request body
		bodyBytes, _ := io.ReadAll(c.Request.Body)
		c.Request.Body = io.NopCloser(bytes.NewBuffer(bodyBytes))

		// Create new request
		url := fmt.Sprintf("%s%s", targetURL, c.Request.RequestURI)
		proxyReq, _ := http.NewRequest(c.Request.Method, url, bytes.NewBuffer(bodyBytes))

		// Copy headers
		for key, values := range c.Request.Header {
			for _, value := range values {
				proxyReq.Header.Add(key, value)
			}
		}

		// Execute request
		client := &http.Client{}
		resp, err := client.Do(proxyReq)
		if err != nil {
			c.JSON(http.StatusBadGateway, gin.H{"error": "Service unavailable"})
			return
		}
		defer resp.Body.Close()

		// Copy response
		respBody, _ := io.ReadAll(resp.Body)
		for key, values := range resp.Header {
			for _, value := range values {
				c.Writer.Header().Add(key, value)
			}
		}
		c.Data(resp.StatusCode, resp.Header.Get("Content-Type"), respBody)
	}
}

// HealthCheck checks gateway health
func HealthCheck(cfg *config.Config) gin.HandlerFunc {
	return func(c *gin.Context) {
		services := map[string]bool{}

		// Check each service
		serviceEndpoints := map[string]string{
			"user":         cfg.UserServiceURL + "/health",
			"appointment":  cfg.AppointmentServiceURL + "/health",
			"medical":      cfg.MedicalServiceURL + "/health",
			"pharmacy":     cfg.PharmacyServiceURL + "/health",
			"payment":      cfg.PaymentServiceURL + "/health",
			"analytics":    cfg.AnalyticsServiceURL + "/health",
		}

		for serviceName, endpoint := range serviceEndpoints {
			resp, err := http.Get(endpoint)
			services[serviceName] = err == nil && resp.StatusCode == http.StatusOK
		}

		c.JSON(http.StatusOK, gin.H{
			"status":   "operational",
			"services": services,
		})
	}
}

// ServiceStatus returns detailed service status
func ServiceStatus(cfg *config.Config) gin.HandlerFunc {
	return func(c *gin.Context) {
		status := map[string]interface{}{
			"gateway": "operational",
			"services": map[string]interface{}{
				"user":        checkService(cfg.UserServiceURL),
				"appointment": checkService(cfg.AppointmentServiceURL),
				"medical":     checkService(cfg.MedicalServiceURL),
				"pharmacy":    checkService(cfg.PharmacyServiceURL),
				"payment":     checkService(cfg.PaymentServiceURL),
				"analytics":   checkService(cfg.AnalyticsServiceURL),
			},
		}

		c.JSON(http.StatusOK, status)
	}
}

func checkService(serviceURL string) map[string]interface{} {
	resp, err := http.Get(serviceURL + "/health")
	defer func() {
		if resp != nil && resp.Body != nil {
			resp.Body.Close()
		}
	}()

	if err != nil || resp.StatusCode != http.StatusOK {
		return map[string]interface{}{
			"status": "down",
			"url":    serviceURL,
		}
	}

	return map[string]interface{}{
		"status": "healthy",
		"url":    serviceURL,
	}
}
