package handlers

import (
	"fmt"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/google/uuid"
	"github.com/meditrack/user-service/internal/db"
	"github.com/meditrack/user-service/internal/models"
)

// CreateUser creates a new user
func CreateUser(c *gin.Context) {
	var req models.CreateUserRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error":   "Invalid request",
			"details": err.Error(),
		})
		return
	}

	user := &models.User{
		Name:              req.Name,
		Email:             req.Email,
		Password:          req.Password,
		Phone:             req.Phone,
		Address:           req.Address,
		Role:              req.Role,
		Status:            "active",
		Specialty:         req.Specialty,
		LicenseNumber:     req.LicenseNumber,
		InsuranceProvider: req.InsuranceProvider,
	}

	// Hash password
	if err := user.HashPassword(); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Failed to process password",
		})
		return
	}

	// Insert into database
	result, err := db.DB.Exec(
		`INSERT INTO users (name, email, password, phone, address, role, status, specialty, license_number, insurance_provider)
		 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		user.Name, user.Email, user.Password, user.Phone, user.Address,
		user.Role, user.Status, user.Specialty, user.LicenseNumber, user.InsuranceProvider,
	)

	if err != nil {
		c.JSON(http.StatusConflict, gin.H{
			"error": "Email already exists",
		})
		return
	}

	id, err := result.LastInsertId()
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Failed to create user",
		})
		return
	}

	user.ID = fmt.Sprintf("%d", id)
	user.CreatedAt = time.Now()
	user.UpdatedAt = time.Now()

	c.JSON(http.StatusCreated, gin.H{
		"message": "User created successfully",
		"user":    user,
	})
}

// GetUser retrieves a user by ID
func GetUser(c *gin.Context) {
	userID, err := strconv.Atoi(c.Param("id"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Invalid user ID",
		})
		return
	}

	var user models.User
	err = db.DB.QueryRow(
		`SELECT id, name, email, phone, address, role, status, specialty, license_number, 
				insurance_provider, created_at, updated_at FROM users WHERE id = ?`,
		userID,
	).Scan(
		&user.ID, &user.Name, &user.Email, &user.Phone, &user.Address,
		&user.Role, &user.Status, &user.Specialty, &user.LicenseNumber,
		&user.InsuranceProvider, &user.CreatedAt, &user.UpdatedAt,
	)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"error": "User not found",
		})
		return
	}

	c.JSON(http.StatusOK, user)
}

// GetAllUsers retrieves all users with pagination
func GetAllUsers(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	limit := c.DefaultQuery("limit", "10")
	role := c.Query("role")

	pageNum, _ := strconv.Atoi(page)
	limitNum, _ := strconv.Atoi(limit)
	offset := (pageNum - 1) * limitNum

	query := "SELECT id, name, email, phone, address, role, status, specialty, license_number, insurance_provider, created_at, updated_at FROM users"
	args := []interface{}{}

	if role != "" {
		query += " WHERE role = ?"
		args = append(args, role)
	}

	query += " LIMIT ? OFFSET ?"
	args = append(args, limitNum, offset)

	rows, err := db.DB.Query(query, args...)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Failed to fetch users",
		})
		return
	}
	defer rows.Close()

	var users []models.User
	for rows.Next() {
		var user models.User
		err := rows.Scan(
			&user.ID, &user.Name, &user.Email, &user.Phone, &user.Address,
			&user.Role, &user.Status, &user.Specialty, &user.LicenseNumber,
			&user.InsuranceProvider, &user.CreatedAt, &user.UpdatedAt,
		)
		if err != nil {
			continue
		}
		users = append(users, user)
	}

	c.JSON(http.StatusOK, gin.H{
		"page":  pageNum,
		"limit": limitNum,
		"data":  users,
	})
}

// UpdateUser updates user information
func UpdateUser(c *gin.Context) {
	userID, err := strconv.Atoi(c.Param("id"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Invalid user ID",
		})
		return
	}

	var req models.CreateUserRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Invalid request",
		})
		return
	}

	result, err := db.DB.Exec(
		`UPDATE users SET name = ?, phone = ?, address = ?, role = ?, specialty = ?, 
				license_number = ?, insurance_provider = ? WHERE id = ?`,
		req.Name, req.Phone, req.Address, req.Role, req.Specialty,
		req.LicenseNumber, req.InsuranceProvider, userID,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Failed to update user",
		})
		return
	}

	rows, _ := result.RowsAffected()
	if rows == 0 {
		c.JSON(http.StatusNotFound, gin.H{
			"error": "User not found",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "User updated successfully",
	})
}

// DeleteUser deletes a user
func DeleteUser(c *gin.Context) {
	userID, err := strconv.Atoi(c.Param("id"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Invalid user ID",
		})
		return
	}

	result, err := db.DB.Exec("DELETE FROM users WHERE id = ?", userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"error": "Failed to delete user",
		})
		return
	}

	rows, _ := result.RowsAffected()
	if rows == 0 {
		c.JSON(http.StatusNotFound, gin.H{
			"error": "User not found",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "User deleted successfully",
	})
}

// Login authenticates a user and returns JWT token
func Login(c *gin.Context) {
	var req models.LoginRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": "Invalid request",
		})
		return
	}

	var user models.User
	err := db.DB.QueryRow(
		`SELECT id, name, email, password, role FROM users WHERE email = ?`,
		req.Email,
	).Scan(&user.ID, &user.Name, &user.Email, &user.Password, &user.Role)

	if err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{
			"error": "Invalid email or password",
		})
		return
	}

	if !user.VerifyPassword(req.Password) {
		c.JSON(http.StatusUnauthorized, gin.H{
			"error": "Invalid email or password",
		})
		return
	}

	// Generate simple token (in production use JWT library)
	token := "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9." + uuid.New().String()

	response := models.LoginResponse{
		ID:    user.ID,
		Name:  user.Name,
		Email: user.Email,
		Role:  user.Role,
		Token: token,
	}

	c.JSON(http.StatusOK, response)
}

// HealthCheck returns health status
func HealthCheck(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{
		"status":  "healthy",
		"service": "user-service",
		"time":    time.Now(),
	})
}
