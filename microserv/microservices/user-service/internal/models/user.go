package models

import (
	"time"

	"golang.org/x/crypto/bcrypt"
)

type User struct {
	ID                string    `json:"id"`
	Name              string    `json:"name" binding:"required"`
	Email             string    `json:"email" binding:"required,email"`
	Password          string    `json:"password,omitempty" binding:"required,min=6"`
	Phone             string    `json:"phone"`
	Address           string    `json:"address"`
	Role              string    `json:"role" binding:"required,oneof=patient doctor pharmacist admin"`
	Status            string    `json:"status" binding:"oneof=active inactive suspended"`
	Specialty         string    `json:"specialty"`
	LicenseNumber     string    `json:"license_number"`
	InsuranceProvider string    `json:"insurance_provider"`
	CreatedAt         time.Time `json:"created_at"`
	UpdatedAt         time.Time `json:"updated_at"`
}

type CreateUserRequest struct {
	Name              string `json:"name" binding:"required"`
	Email             string `json:"email" binding:"required,email"`
	Password          string `json:"password" binding:"required,min=6"`
	Phone             string `json:"phone"`
	Address           string `json:"address"`
	Role              string `json:"role" binding:"required,oneof=patient doctor pharmacist admin"`
	Specialty         string `json:"specialty"`
	LicenseNumber     string `json:"license_number"`
	InsuranceProvider string `json:"insurance_provider"`
}

type LoginRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required"`
}

type LoginResponse struct {
	ID    string `json:"id"`
	Name  string `json:"name"`
	Email string `json:"email"`
	Role  string `json:"role"`
	Token string `json:"token"`
}

// HashPassword hashes a password using bcrypt
func (u *User) HashPassword() error {
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(u.Password), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	u.Password = string(hashedPassword)
	return nil
}

// VerifyPassword verifies a password against its hash
func (u *User) VerifyPassword(password string) bool {
	err := bcrypt.CompareHashAndPassword([]byte(u.Password), []byte(password))
	return err == nil
}

type Role struct {
	ID          int    `json:"id"`
	Name        string `json:"name" binding:"required"`
	Description string `json:"description"`
	CreatedAt   time.Time `json:"created_at"`
}

type Permission struct {
	ID          int    `json:"id"`
	Name        string `json:"name" binding:"required"`
	Resource    string `json:"resource"`
	Action      string `json:"action"`
	Description string `json:"description"`
	CreatedAt   time.Time `json:"created_at"`
}

type AuditLog struct {
	ID        int       `json:"id"`
	UserID    *int      `json:"user_id"`
	Action    string    `json:"action"`
	Resource  string    `json:"resource"`
	OldValues interface{} `json:"old_values"`
	NewValues interface{} `json:"new_values"`
	IPAddress string    `json:"ip_address"`
	UserAgent string    `json:"user_agent"`
	Timestamp time.Time `json:"timestamp"`
}
