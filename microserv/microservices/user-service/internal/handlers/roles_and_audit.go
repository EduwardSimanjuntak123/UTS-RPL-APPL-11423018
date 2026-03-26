package handlers

import (
	"database/sql"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/google/uuid"
	"github.com/meditrack/user-service/internal/db"
)

// CreateRole creates a new role
func CreateRole(c *gin.Context) {
	var req struct {
		Name        string `json:"name" binding:"required"`
		Description string `json:"description"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid request", "details": err.Error()})
		return
	}

	roleID := uuid.New().String()
	_, err := db.DB.Exec(
		`INSERT INTO roles (id, name, description) VALUES (?, ?, ?)`,
		roleID, req.Name, req.Description,
	)

	if err != nil {
		c.JSON(http.StatusConflict, gin.H{"error": "Role already exists"})
		return
	}

	c.JSON(http.StatusCreated, gin.H{
		"message": "Role created successfully",
		"role_id": roleID,
	})
}

// GetRole retrieves a role by ID
func GetRole(c *gin.Context) {
	roleID := c.Param("id")

	var name, description string
	err := db.DB.QueryRow(
		`SELECT name, description FROM roles WHERE id = ?`,
		roleID,
	).Scan(&name, &description)

	if err == sql.ErrNoRows {
		c.JSON(http.StatusNotFound, gin.H{"error": "Role not found"})
		return
	} else if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"id":          roleID,
		"name":        name,
		"description": description,
	})
}

// GetAllRoles retrieves all roles
func GetAllRoles(c *gin.Context) {
	rows, err := db.DB.Query(`SELECT id, name, description FROM roles`)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}
	defer rows.Close()

	var roles []gin.H
	for rows.Next() {
		var id, name, description string
		if err := rows.Scan(&id, &name, &description); err != nil {
			continue
		}
		roles = append(roles, gin.H{
			"id":          id,
			"name":        name,
			"description": description,
		})
	}

	c.JSON(http.StatusOK, gin.H{
		"total": len(roles),
		"roles": roles,
	})
}

// UpdateRole updates a role
func UpdateRole(c *gin.Context) {
	roleID := c.Param("id")

	var req struct {
		Name        string `json:"name"`
		Description string `json:"description"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid request"})
		return
	}

	_, err := db.DB.Exec(
		`UPDATE roles SET name = ?, description = ? WHERE id = ?`,
		req.Name, req.Description, roleID,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update role"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Role updated successfully"})
}

// DeleteRole deletes a role
func DeleteRole(c *gin.Context) {
	roleID := c.Param("id")

	_, err := db.DB.Exec(`DELETE FROM roles WHERE id = ?`, roleID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete role"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Role deleted successfully"})
}

// GetAuditLogs retrieves all audit logs
func GetAuditLogs(c *gin.Context) {
	limit := c.DefaultQuery("limit", "20")
	offset := c.DefaultQuery("offset", "0")

	rows, err := db.DB.Query(
		`SELECT id, user_id, action, resource, ip_address, created_at FROM audit_logs 
		 ORDER BY created_at DESC LIMIT ? OFFSET ?`,
		limit, offset,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}
	defer rows.Close()

	var logs []gin.H
	for rows.Next() {
		var id, userID, action, resource, ipAddress string
		var createdAt time.Time

		if err := rows.Scan(&id, &userID, &action, &resource, &ipAddress, &createdAt); err != nil {
			continue
		}

		logs = append(logs, gin.H{
			"id":         id,
			"user_id":    userID,
			"action":     action,
			"resource":   resource,
			"ip_address": ipAddress,
			"created_at": createdAt,
		})
	}

	c.JSON(http.StatusOK, gin.H{
		"total": len(logs),
		"logs":  logs,
	})
}

// GetUserAuditLogs retrieves audit logs for a specific user
func GetUserAuditLogs(c *gin.Context) {
	userID := c.Param("user_id")

	rows, err := db.DB.Query(
		`SELECT id, user_id, action, resource, ip_address, created_at FROM audit_logs 
		 WHERE user_id = ? ORDER BY created_at DESC`,
		userID,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}
	defer rows.Close()

	var logs []gin.H
	for rows.Next() {
		var id, user, action, resource, ipAddress string
		var createdAt time.Time

		if err := rows.Scan(&id, &user, &action, &resource, &ipAddress, &createdAt); err != nil {
			continue
		}

		logs = append(logs, gin.H{
			"id":         id,
			"user_id":    user,
			"action":     action,
			"resource":   resource,
			"ip_address": ipAddress,
			"created_at": createdAt,
		})
	}

	c.JSON(http.StatusOK, gin.H{
		"total": len(logs),
		"logs":  logs,
	})
}


