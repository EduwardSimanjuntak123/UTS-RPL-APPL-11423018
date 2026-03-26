# TASK 6: GOLANG MICROSERVICES IMPLEMENTATION GUIDE

**MediTrack Transformation - Case Study 2**  
**Date**: March 26, 2026  
**Technology**: Golang (Go 1.23) + Gin Framework + GORM  
**Focus**: Complete implementation guide for production-ready microservices

---

## 📋 EXECUTIVE SUMMARY

MediTrack terdiri dari **7 independent Golang microservices** yang dapat dijalankan, di-scale, dan di-maintain secara terpisah. Dokumen ini menyediakan:

- **Project Structure** untuk setiap service
- **Code Templates** dan Best Practices
- **Development Environment Setup**
- **Deployment Configuration**
- **Testing Strategy**

---

## 🏗️ STANDARD PROJECT STRUCTURE

Setiap microservice harus mengikuti struktur berikut:

```
user-service/                          # Service root
├── cmd/
│   └── server/
│       └── main.go                   # Entry point
├── internal/
│   ├── handlers/                     # HTTP handlers
│   │   ├── user_handler.go
│   │   └── health_handler.go
│   ├── services/                     # Business logic
│   │   ├── user_service.go
│   │   └── cache_service.go
│   ├── models/                       # Data structures
│   │   ├── user.go
│   │   └── dto.go
│   ├── repository/                   # Database layer
│   │   ├── user_repository.go
│   │   └── interface.go
│   ├── middleware/                   # HTTP middleware
│   │   ├── auth.go
│   │   └── logging.go
│   └── config/                       # Configuration
│       └── config.go
├── database/
│   ├── migrations/                   # SQL migrations
│   │   └── 001_create_users_table.sql
│   └── seeder.go                     # Data seeding
├── routes/
│   └── routes.go                     # Route definitions
├── utils/                            # Utility functions
│   ├── validator.go
│   └── error_handler.go
├── .env                              # Environment variables
├── .env.example                      # Example env
├── docker-compose.yml                # Local development
├── Dockerfile                        # Production image
├── go.mod                            # Go modules
├── go.sum
├── tests/
│   ├── unit/                         # Unit tests
│   ├── integration/                  # Integration tests
│   └── e2e/                          # End-to-end tests
└── README.md                         # Documentation
```

---

## 🚀 QUICK START - LOCAL DEVELOPMENT

### 1. Prerequisites

```bash
# Install Go 1.23
wget https://go.dev/dl/go1.23.0.windows-amd64.msi
# Run installer

# Verify installation
go version
# Expected: go version go1.23 windows/amd64
```

### 2. Clone Service

```bash
cd d:\semester 6\APPL\UTS-RPL-APPL-11423018\uts\microservices\user-service
```

### 3. Setup Environment

```bash
# Copy example env
cp .env.example .env

# Edit .env with your settings
# Key variables:
# DB_HOST=localhost
# DB_PORT=3306
# DB_USER=root
# DB_PASSWORD=yourpassword
# DB_NAME=meditrack_user
# PORT=3001
```

### 4. Download Dependencies

```bash
go mod download
go mod tidy
```

### 5. Run Migrations

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_user;"

# Run migrations
go run cmd/server/main.go migrate

# Or use migration tool
# sqlite-migrate -path database/migrations -database "mysql://root:root@tcp(localhost:3306)/meditrack_user" up
```

### 6. Run Service

```bash
# Development mode (hot reload with air)
go install github.com/cosmtrek/air@latest
air

# Production mode
go build -o bin/user-service ./cmd/server
./bin/user-service
```

### 7. Test API

```bash
# Health check
curl http://localhost:3001/health

# Expected response:
# {"status":"ok","timestamp":"2026-03-26T10:00:00Z"}
```

---

## 📝 TEMPLATE CODE EXAMPLES

### Template 1: Main Entry Point

**File**: `cmd/server/main.go`

```go
package main

import (
	"fmt"
	"log"
	"os"
	"user-service/internal/config"
	"user-service/internal/handlers"
	"user-service/internal/middleware"
	"user-service/routes"
	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"
)

func main() {
	// Load environment variables
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found, using environment variables")
	}

	// Initialize configuration
	cfg := config.LoadConfig()

	// Setup Gin router
	httpServer := gin.New()

	// Apply middleware
	httpServer.Use(middleware.CORSMiddleware())
	httpServer.Use(middleware.LoggingMiddleware())
	httpServer.Use(middleware.RecoveryMiddleware())

	// Setup database
	db := config.InitializeDatabase(cfg)
	if db == nil {
		log.Fatal("Failed to initialize database")
	}

	// Inject dependencies
	userHandler := handlers.NewUserHandler(db)
	healthHandler := handlers.NewHealthHandler(db)

	// Setup routes
	routes.setupRoutes(httpServer, userHandler, healthHandler)

	// Start server
	port := os.Getenv("PORT")
	if port == "" {
		port = "3001"
	}

	fmt.Printf("User Service running on port %s\n", port)
	if err := httpServer.Run(":" + port); err != nil {
		log.Fatal("Failed to start server:", err)
	}
}
```

---

### Template 2: Configuration Management

**File**: `internal/config/config.go`

```go
package config

import (
	"fmt"
	"os"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

type Config struct {
	DBHost     string
	DBPort     string
	DBUser     string
	DBPassword string
	DBName     string
	Port       string
	LogLevel   string
	JWTSecret  string
}

func LoadConfig() *Config {
	return &Config{
		DBHost:     getEnv("DB_HOST", "localhost"),
		DBPort:     getEnv("DB_PORT", "3306"),
		DBUser:     getEnv("DB_USER", "root"),
		DBPassword: getEnv("DB_PASSWORD", "root"),
		DBName:     getEnv("DB_NAME", "meditrack_user"),
		Port:       getEnv("PORT", "3001"),
		LogLevel:   getEnv("LOG_LEVEL", "INFO"),
		JWTSecret:  getEnv("JWT_SECRET", "your-secret-key"),
	}
}

func (c *Config) GetDSN() string {
	return fmt.Sprintf(
		"%s:%s@tcp(%s:%s)/%s?charset=utf8mb4&parseTime=True&loc=Local",
		c.DBUser,
		c.DBPassword,
		c.DBHost,
		c.DBPort,
		c.DBName,
	)
}

func InitializeDatabase(cfg *Config) *gorm.DB {
	dsn := cfg.GetDSN()
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		fmt.Println("Failed to connect to database:", err)
		return nil
	}

	sqlDB, _ := db.DB()
	sqlDB.SetMaxIdleConns(10)
	sqlDB.SetMaxOpenConns(100)

	// Auto migrate models
	// db.AutoMigrate(&User{})

	return db
}

func getEnv(key, defaultVal string) string {
	if value, exists := os.LookupEnv(key); exists {
		return value
	}
	return defaultVal
}
```

---

### Template 3: Models & DTOs

**File**: `internal/models/user.go`

```go
package models

import (
	"gorm.io/gorm"
	"time"
)

// Database Model
type User struct {
	ID        string    `gorm:"primaryKey" json:"id"`
	Email     string    `gorm:"uniqueIndex" json:"email"`
	Password  string    `json:"-"` // Never expose in JSON
	Name      string    `json:"name"`
	Role      string    `json:"role"` // patient, doctor, admin, pharmacy
	Status    string    `json:"status"` // active, inactive
	Phone     string    `json:"phone"`
	Address   string    `json:"address"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
	DeletedAt gorm.DeletedAt `gorm:"index" json:"-"` // Soft delete
}

// Request DTO
type CreateUserRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required,min=8"`
	Name     string `json:"name" binding:"required,max=100"`
	Role     string `json:"role" binding:"required,oneof=patient doctor admin pharmacy"`
}

// Response DTO
type UserResponse struct {
	ID        string    `json:"id"`
	Email     string    `json:"email"`
	Name      string    `json:"name"`
	Role      string    `json:"role"`
	Status    string    `json:"status"`
	CreatedAt time.Time `json:"created_at"`
}

func (u *User) ToResponse() UserResponse {
	return UserResponse{
		ID:        u.ID,
		Email:     u.Email,
		Name:      u.Name,
		Role:      u.Role,
		Status:    u.Status,
		CreatedAt: u.CreatedAt,
	}
}
```

---

### Template 4: Repository Pattern

**File**: `internal/repository/user_repository.go`

```go
package repository

import (
	"fmt"
	"user-service/internal/models"
	"gorm.io/gorm"
)

type UserRepository interface {
	Create(user *models.User) error
	GetByID(id string) (*models.User, error)
	GetByEmail(email string) (*models.User, error)
	Update(user *models.User) error
	Delete(id string) error
	GetAll(page, pageSize int) ([]models.User, int64, error)
}

type userRepository struct {
	db *gorm.DB
}

func NewUserRepository(db *gorm.DB) UserRepository {
	return &userRepository{db: db}
}

func (r *userRepository) Create(user *models.User) error {
	if err := r.db.Create(user).Error; err != nil {
		return fmt.Errorf("failed to create user: %w", err)
	}
	return nil
}

func (r *userRepository) GetByID(id string) (*models.User, error) {
	var user models.User
	if err := r.db.Where("id = ?", id).First(&user).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			return nil, nil
		}
		return nil, fmt.Errorf("failed to fetch user: %w", err)
	}
	return &user, nil
}

func (r *userRepository) GetByEmail(email string) (*models.User, error) {
	var user models.User
	if err := r.db.Where("email = ?", email).First(&user).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			return nil, nil
		}
		return nil, fmt.Errorf("failed to fetch user by email: %w", err)
	}
	return &user, nil
}

func (r *userRepository) Update(user *models.User) error {
	if err := r.db.Save(user).Error; err != nil {
		return fmt.Errorf("failed to update user: %w", err)
	}
	return nil
}

func (r *userRepository) Delete(id string) error {
	if err := r.db.Where("id = ?", id).Delete(&models.User{}).Error; err != nil {
		return fmt.Errorf("failed to delete user: %w", err)
	}
	return nil
}

func (r *userRepository) GetAll(page, pageSize int) ([]models.User, int64, error) {
	var users []models.User
	var total int64

	// Count total
	if err := r.db.Model(&models.User{}).Count(&total).Error; err != nil {
		return nil, 0, err
	}

	// Fetch paginated results
	offset := (page - 1) * pageSize
	if err := r.db.Offset(offset).Limit(pageSize).Find(&users).Error; err != nil {
		return nil, 0, err
	}

	return users, total, nil
}
```

---

### Template 5: Service Layer

**File**: `internal/services/user_service.go`

```go
package services

import (
	"fmt"
	"user-service/internal/models"
	"user-service/internal/repository"
	"golang.org/x/crypto/bcrypt"
	"github.com/google/uuid"
)

type UserService interface {
	Register(req *models.CreateUserRequest) (*models.User, error)
	Login(email, password string) (*models.User, error)
	GetUser(id string) (*models.User, error)
	UpdateUser(user *models.User) error
	DeleteUser(id string) error
}

type userService struct {
	repo repository.UserRepository
}

func NewUserService(repo repository.UserRepository) UserService {
	return &userService{repo: repo}
}

func (s *userService) Register(req *models.CreateUserRequest) (*models.User, error) {
	// Check if email already exists
	existing, err := s.repo.GetByEmail(req.Email)
	if err != nil {
		return nil, fmt.Errorf("failed to check email: %w", err)
	}
	if existing != nil {
		return nil, fmt.Errorf("email already registered")
	}

	// Hash password
	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(req.Password), bcrypt.DefaultCost)
	if err != nil {
		return nil, fmt.Errorf("failed to hash password: %w", err)
	}

	// Create user
	user := &models.User{
		ID:       uuid.New().String(),
		Email:    req.Email,
		Password: string(hashedPassword),
		Name:     req.Name,
		Role:     req.Role,
		Status:   "active",
	}

	if err := s.repo.Create(user); err != nil {
		return nil, err
	}

	return user, nil
}

func (s *userService) Login(email, password string) (*models.User, error) {
	user, err := s.repo.GetByEmail(email)
	if err != nil {
		return nil, err
	}
	if user == nil {
		return nil, fmt.Errorf("user not found")
	}

	// Verify password
	if err := bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(password)); err != nil {
		return nil, fmt.Errorf("invalid password")
	}

	return user, nil
}

func (s *userService) GetUser(id string) (*models.User, error) {
	user, err := s.repo.GetByID(id)
	if err != nil {
		return nil, err
	}
	if user == nil {
		return nil, fmt.Errorf("user not found")
	}
	return user, nil
}

func (s *userService) UpdateUser(user *models.User) error {
	return s.repo.Update(user)
}

func (s *userService) DeleteUser(id string) error {
	return s.repo.Delete(id)
}
```

---

### Template 6: HTTP Handlers

**File**: `internal/handlers/user_handler.go`

```go
package handlers

import (
	"net/http"
	"user-service/internal/models"
	"user-service/internal/repository"
	"user-service/internal/services"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

type UserHandler struct {
	service services.UserService
}

func NewUserHandler(db *gorm.DB) *UserHandler {
	repo := repository.NewUserRepository(db)
	service := services.NewUserService(repo)
	return &UserHandler{service: service}
}

// POST /api/users/register
func (h *UserHandler) Register(c *gin.Context) {
	var req models.CreateUserRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": err.Error(),
		})
		return
	}

	user, err := h.service.Register(&req)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": err.Error(),
		})
		return
	}

	c.JSON(http.StatusCreated, user.ToResponse())
}

// POST /api/users/login
func (h *UserHandler) Login(c *gin.Context) {
	var req struct {
		Email    string `json:"email" binding:"required,email"`
		Password string `json:"password" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"error": err.Error(),
		})
		return
	}

	user, err := h.service.Login(req.Email, req.Password)
	if err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{
			"error": "Invalid email or password",
		})
		return
	}

	// Generate JWT token (implement in middleware)
	token := generateJWTToken(user.ID)

	c.JSON(http.StatusOK, gin.H{
		"user":  user.ToResponse(),
		"token": token,
	})
}

// GET /api/users/:id
func (h *UserHandler) GetUser(c *gin.Context) {
	id := c.Param("id")

	user, err := h.service.GetUser(id)
	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"error": "User not found",
		})
		return
	}

	c.JSON(http.StatusOK, user.ToResponse())
}
```

---

### Template 7: Middleware

**File**: `internal/middleware/auth.go`

```go
package middleware

import (
	"fmt"
	"net/http"
	"strings"
	"github.com/gin-gonic/gin"
	"github.com/golang-jwt/jwt/v5"
)

func AuthMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		authHeader := c.GetHeader("Authorization")
		if authHeader == "" {
			c.JSON(http.StatusUnauthorized, gin.H{
				"error": "Missing authorization header",
			})
			c.Abort()
			return
		}

		// Extract token from "Bearer <token>"
		parts := strings.Split(authHeader, " ")
		if len(parts) != 2 || parts[0] != "Bearer" {
			c.JSON(http.StatusUnauthorized, gin.H{
				"error": "Invalid authorization header format",
			})
			c.Abort()
			return
		}

		tokenString := parts[1]

		// Verify JWT token
		token, err := jwt.Parse(tokenString, func(token *jwt.Token) (interface{}, error) {
			if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
				return nil, fmt.Errorf("unexpected signing method: %v", token.Header["alg"])
			}
			return []byte("your-secret-key"), nil
		})

		if err != nil || !token.Valid {
			c.JSON(http.StatusUnauthorized, gin.H{
				"error": "Invalid token",
			})
			c.Abort()
			return
		}

		// Extract claims
		if claims, ok := token.Claims.(jwt.MapClaims); ok {
			userID := claims["user_id"].(string)
			c.Set("user_id", userID)
			c.Set("user_claims", claims)
		}

		c.Next()
	}
}
```

---

### Template 8: Routes

**File**: `routes/routes.go`

```go
package routes

import (
	"user-service/internal/handlers"
	"user-service/internal/middleware"
	"github.com/gin-gonic/gin"
)

func SetupRoutes(r *gin.Engine, userHandler *handlers.UserHandler, healthHandler *handlers.HealthHandler) {
	// Health check (no auth required)
	r.GET("/health", healthHandler.HealthCheck)
	r.GET("/ready", healthHandler.ReadinessCheck)

	// Public routes
	public := r.Group("/api")
	{
		public.POST("/users/register", userHandler.Register)
		public.POST("/users/login", userHandler.Login)
	}

	// Protected routes
	protected := r.Group("/api")
	protected.Use(middleware.AuthMiddleware())
	{
		protected.GET("/users/:id", userHandler.GetUser)
		protected.PUT("/users/:id", userHandler.UpdateUser)
		protected.DELETE("/users/:id", userHandler.DeleteUser)
	}

	// Admin routes
	admin := r.Group("/api/admin")
	admin.Use(middleware.AuthMiddleware())
	admin.Use(middleware.AdminMiddleware())
	{
		admin.GET("/users", userHandler.GetAllUsers)
		admin.DELETE("/users/:id", userHandler.AdminDeleteUser)
	}
}
```

---

## 🧪 TESTING STRATEGY

### Unit Tests

**File**: `tests/unit/user_service_test.go`

```go
package unit

import (
	"testing"
	"user-service/internal/models"
	"user-service/internal/services"
	"github.com/stretchr/testify/assert"
	"github.com/stretchr/testify/mock"
)

// Mock repository
type MockUserRepository struct {
	mock.Mock
}

func (m *MockUserRepository) Create(user *models.User) error {
	args := m.Called(user)
	return args.Error(0)
}

func (m *MockUserRepository) GetByEmail(email string) (*models.User, error) {
	args := m.Called(email)
	if user := args.Get(0); user != nil {
		return user.(*models.User), args.Error(1)
	}
	return nil, args.Error(1)
}

// Test case
func TestUserServiceRegister(t *testing.T) {
	repo := new(MockUserRepository)
	repo.On("GetByEmail", "test@example.com").Return(nil, nil)
	repo.On("Create", mock.Anything).Return(nil)

	service := services.NewUserService(repo)

	req := &models.CreateUserRequest{
		Email:    "test@example.com",
		Password: "SecurePass123!",
		Name:     "Test User",
		Role:     "patient",
	}

	user, err := service.Register(req)

	assert.NoError(t, err)
	assert.NotNil(t, user)
	assert.Equal(t, "test@example.com", user.Email)
	assert.Equal(t, "Test User", user.Name)
	repo.AssertExpectations(t)
}
```

### Integration Tests

```bash
# Run all tests
go test ./... -v

# Run specific test
go test -run TestUserServiceRegister -v

# Run with coverage
go test ./... -cover

# Generate coverage report
go test ./... -coverprofile=coverage.out
go tool cover -html=coverage.out
```

---

## 🐳 DOCKER SETUP

### Dockerfile

```dockerfile
# Build stage
FROM golang:1.23-alpine AS builder

WORKDIR /app
COPY go.mod go.sum ./
RUN go mod download

COPY . .
RUN CGO_ENABLED=1 GOOS=linux go build -a -installsuffix cgo -o user-service ./cmd/server

# Runtime stage
FROM alpine:latest
RUN apk --no-cache add ca-certificates

WORKDIR /root/
COPY --from=builder /app/user-service .
COPY --from=builder /app/.env.example .

EXPOSE 3001

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD wget --quiet --tries=1 --spider http://localhost:3001/health || exit 1

CMD ["./user-service"]
```

### Docker Compose for Local Development

```yaml
# docker-compose.yml

version: '3.8'

services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: meditrack_user
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

  user-service:
    build:
      context: .
        dockerfile: Dockerfile
    ports:
      - "3001:3001"
    environment:
      DB_HOST: mysql
      DB_USER: root
      DB_PASSWORD: root
      DB_NAME: meditrack_user
      PORT: 3001
    depends_on:
      - mysql
    command: sh -c "sleep 10 && ./user-service"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

volumes:
  mysql_data:
```

### Run with Docker Compose

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f user-service

# Stop services
docker-compose down
```

---

## 📊 GO.MOD DEPENDENCIES

```go
module github.com/meditrack/user-service

go 1.23

require (
	github.com/gin-gonic/gin v1.9.1
	github.com/go-sql-driver/mysql v1.8.1
	github.com/google/uuid v1.4.0
	github.com/golang-jwt/jwt/v5 v5.0.0
	github.com/joho/godotenv v1.5.1
	github.com/sirupsen/logrus v1.9.3
	github.com/stretchr/testify v1.8.4
	golang.org/x/crypto v0.31.0
	gorm.io/driver/mysql v1.6.0
	gorm.io/gorm v1.31.1
)
```

### Install Dependencies

```bash
go get github.com/gin-gonic/gin
go get gorm.io/driver/mysql
go get gorm.io/gorm
go get golang.org/x/crypto
go get github.com/golang-jwt/jwt/v5
go get github.com/google/uuid
go get github.com/joho/godotenv

# Update all
go get -u ./...

# Tidy (remove unused)
go mod tidy
```

---

## 🔧 ENVIRONMENT VARIABLES

### .env.example

```
# Database
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=root
DB_NAME=meditrack_user

# Server
PORT=3001
LOG_LEVEL=INFO

# JWT
JWT_SECRET=your-super-secret-key-change-in-production

# Redis (optional)
REDIS_HOST=localhost
REDIS_PORT=6379

# External Services
USER_SERVICE_URL=http://localhost:3001
APPOINTMENT_SERVICE_URL=http://localhost:3002
MEDICAL_SERVICE_URL=http://localhost:3003
PHARMACY_SERVICE_URL=http://localhost:3004
PAYMENT_SERVICE_URL=http://localhost:3005
ANALYTICS_SERVICE_URL=http://localhost:3006

# API Gateway
API_GATEWAY_URL=http://localhost:3000

# Email (optional)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password

# Payment Gateway (Stripe example)
STRIPE_API_KEY=sk_test_xxxxx
STRIPE_PUBLIC_KEY=pk_test_xxxxx
```

---

## 📋 GOLANG BEST PRACTICES FOR MEDITRACK

### 1. Error Handling

```go
// ❌ BAD
if err != nil {
    fmt.Println(err)
    return nil
}

// ✅ GOOD
if err != nil {
    logger.Error("Failed to fetch user", map[string]interface{}{
        "error": err.Error(),
        "user_id": userID,
    })
    return nil, fmt.Errorf("failed to fetch user: %w", err)
}
```

### 2. Dependency Injection

```go
// ✅ GOOD - Dependencies injected in constructor
func NewUserService(repo repository.UserRepository, logger Logger) UserService {
    return &userService{
        repo:   repo,
        logger: logger,
    }
}
```

### 3. Interface Segregation

```go
// ✅ GOOD - Small, focused interfaces
type UserRepository interface {
    GetByID(id string) (*User, error)
    Create(user *User) error
}

// ❌ BAD - Fat interface
type Repository interface {
    // 20+ methods mixed together
}
```

### 4. Concurrent Safe Operations

```go
// ✅ GOOD - Use sync.Mutex or channels for shared data
type Cache struct {
    mu    sync.RWMutex
    items map[string]interface{}
}

func (c *Cache) Get(key string) interface{} {
    c.mu.RLock()
    defer c.mu.RUnlock()
    return c.items[key]
}
```

### 5. Context for Graceful Shutdown

```go
// ✅ GOOD - Handle graceful shutdown
func main() {
    ctx, cancel := signal.NotifyContext(context.Background(), os.Interrupt)
    defer cancel()

    server := &http.Server{Addr: ":3001"}
    go server.ListenAndServe()

    <-ctx.Done()
    server.Shutdown(context.Background())
}
```

---

## 📈 PERFORMANCE OPTIMIZATION CHECKLIST

- [ ] Connection pooling configured
- [ ] Indexes created on frequently queried fields
- [ ] Caching implemented (Redis)
- [ ] Query optimization (N+1 problem fixed)
- [ ] Rate limiting enabled
- [ ] Timeouts set on external calls
- [ ] Pagination implemented
- [ ] Database replication setup
- [ ] Monitoring metrics collected
- [ ] Health checks implemented

---

## 🚀 DEPLOYMENT CHECKLIST

### Before Deployment

- [ ] All tests passing
- [ ] Code reviewed & approved
- [ ] Security vulnerabilities scanned
- [ ] Docker image built & tested
- [ ] Environment variables configured
- [ ] Database migrations prepared
- [ ] Logs configured
- [ ] Monitoring alerts set

### Deployment

- [ ] Build Docker image
- [ ] Push to registry
- [ ] Update Kubernetes manifests
- [ ] Apply rolling update
- [ ] Monitor logs & metrics
- [ ] Test endpoints
- [ ] Verify database connections

### Post-Deployment

- [ ] Monitor error rates
- [ ] Check response times
- [ ] Verify resource usage
- [ ] Document any issues
- [ ] Keep rollback plan ready

---

## 📚 USEFUL COMMANDS

```bash
# Development
go run ./cmd/server
go test ./...
go fmt ./...
go vet ./...

# Build
go build -o bin/user-service ./cmd/server

# Docker
docker build -t meditrack/user-service:1.0.0 .
docker run -p 3001:3001 meditrack/user-service:1.0.0

# Dependencies
go mod tidy
go mod vendor

# Benchmarking
go test -bench=. -benchmem

# Profiling
go tool pprof http://localhost:6060/debug/pprof/profile
```

---

## ✅ SUMMARY

MediTrack Golang microservices menggunakan:
- **Framework**: Gin (lightweight, fast)
- **ORM**: GORM (easy, powerful)
- **Database**: MySQL with replication
- **Deployment**: Docker + Kubernetes
- **Monitoring**: Prometheus + Grafana
- **API**: RESTful with JWT auth

Setiap service adalah **self-contained, independently deployable**, dan dapat **scaled horizontally**.

---

**Status**: Complete implementation guide ready for production.

