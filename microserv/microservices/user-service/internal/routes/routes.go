package routes

import (
	"github.com/gin-gonic/gin"
	"github.com/meditrack/user-service/internal/handlers"
)

// SetupUserRoutes sets up all user service routes
func SetupUserRoutes(router *gin.Engine) {
	// Health check
	router.GET("/health", handlers.HealthCheck)

	// User CRUD endpoints
	userRoutes := router.Group("/users")
	{
		userRoutes.POST("", handlers.CreateUser)           // POST /users - Create user
		userRoutes.GET("", handlers.GetAllUsers)           // GET /users - List all users
		userRoutes.GET("/:id", handlers.GetUser)           // GET /users/:id - Get user by ID
		userRoutes.PUT("/:id", handlers.UpdateUser)        // PUT /users/:id - Update user
		userRoutes.DELETE("/:id", handlers.DeleteUser)     // DELETE /users/:id - Delete user
	}

	// Authentication endpoints
	authRoutes := router.Group("/auth")
	{
		authRoutes.POST("/register", handlers.CreateUser)  // POST /auth/register - Register new user
		authRoutes.POST("/login", handlers.Login)          // POST /auth/login - Login user
	}

	// Role management endpoints
	roleRoutes := router.Group("/roles")
	{
		roleRoutes.POST("", handlers.CreateRole)           // POST /roles - Create role
		roleRoutes.GET("", handlers.GetAllRoles)           // GET /roles - List all roles
		roleRoutes.GET("/:id", handlers.GetRole)           // GET /roles/:id - Get role by ID
		roleRoutes.PUT("/:id", handlers.UpdateRole)        // PUT /roles/:id - Update role
		roleRoutes.DELETE("/:id", handlers.DeleteRole)     // DELETE /roles/:id - Delete role
	}

	// Audit log endpoints
	auditRoutes := router.Group("/audit-logs")
	{
		auditRoutes.GET("", handlers.GetAuditLogs)         // GET /audit-logs - List audit logs
		auditRoutes.GET("/user/:user_id", handlers.GetUserAuditLogs) // GET /audit-logs/user/:user_id
	}
}
