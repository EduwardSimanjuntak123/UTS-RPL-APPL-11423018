package routes

import (
	"github.com/gin-gonic/gin"
	"github.com/meditrack/pharmacy-service/internal/handlers"
)

// SetupPharmacyRoutes sets up all pharmacy service routes
func SetupPharmacyRoutes(router *gin.Engine) {
	// Health check
	router.GET("/health", handlers.HealthCheck)

	// Drugs/Inventory endpoints
	drugRoutes := router.Group("/drugs")
	{
		drugRoutes.POST("", handlers.CreateDrug)                       // POST /drugs - Create drug
		drugRoutes.GET("", handlers.GetAllDrugs)                       // GET /drugs - List all
		drugRoutes.GET("/:id", handlers.GetDrug)                       // GET /drugs/:id - Get by ID
		drugRoutes.PUT("/:id", handlers.UpdateDrug)                    // PUT /drugs/:id - Update
		drugRoutes.DELETE("/:id", handlers.DeleteDrug)                 // DELETE /drugs/:id - Delete
	}

	// Drug Stock endpoints
	stockRoutes := router.Group("/stocks")
	{
		stockRoutes.POST("", handlers.CreateStock)                     // POST /stocks - Create stock entry
		stockRoutes.GET("", handlers.GetAllStocks)                     // GET /stocks - List all
		stockRoutes.GET("/:id", handlers.GetStock)                     // GET /stocks/:id - Get by ID
		stockRoutes.PUT("/:id", handlers.UpdateStock)                  // PUT /stocks/:id - Update
		stockRoutes.DELETE("/:id", handlers.DeleteStock)               // DELETE /stocks/:id - Delete
	}

	// Low stock alerts
	lowStockRoutes := router.Group("/stocks")
	{
		lowStockRoutes.GET("/low", handlers.GetLowStockDrugs)          // GET /stocks/low - Get low stock items
	}

	// Pharmacy Orders endpoints
	orderRoutes := router.Group("/orders")
	{
		orderRoutes.POST("", handlers.CreatePharmacyOrder)             // POST /orders - Create order
		orderRoutes.GET("", handlers.GetAllPharmacyOrders)             // GET /orders - List all
		orderRoutes.GET("/:id", handlers.GetPharmacyOrder)             // GET /orders/:id - Get by ID
		orderRoutes.PUT("/:id", handlers.UpdatePharmacyOrder)          // PUT /orders/:id - Update
		orderRoutes.DELETE("/:id", handlers.CancelPharmacyOrder)       // DELETE /orders/:id - Cancel order
	}

	// Patient orders
	patientOrderRoutes := router.Group("/patients/:patient_id/orders")
	{
		patientOrderRoutes.GET("", handlers.GetPatientOrders)          // GET /patients/:patient_id/orders
	}

	// Order status management
	statusRoutes := router.Group("/orders/:id")
	{
		statusRoutes.PUT("/confirm", handlers.ConfirmOrder)            // PUT /orders/:id/confirm
		statusRoutes.PUT("/ready", handlers.MarkOrderReady)            // PUT /orders/:id/ready
		statusRoutes.PUT("/pickup", handlers.MarkOrderPickedUp)        // PUT /orders/:id/pickup
	}

	// Inventory transactions
	inventoryRoutes := router.Group("/inventory")
	{
		inventoryRoutes.GET("", handlers.GetInventoryLog)              // GET /inventory - Get inventory log
		inventoryRoutes.POST("/adjust", handlers.AdjustInventory)      // POST /inventory/adjust
	}
}
