package routes

import (
	"github.com/gin-gonic/gin"
	"github.com/meditrack/appointment-service/internal/handlers"
)

// SetupAppointmentRoutes sets up all appointment service routes
func SetupAppointmentRoutes(router *gin.Engine) {
	// Health check
	router.GET("/health", handlers.HealthCheck)

	// Appointment CRUD endpoints
	appointmentRoutes := router.Group("/appointments")
	{
		appointmentRoutes.POST("", handlers.CreateAppointment)         // POST /appointments - Create appointment
		appointmentRoutes.GET("", handlers.GetAllAppointments)         // GET /appointments - List all
		appointmentRoutes.GET("/:id", handlers.GetAppointment)         // GET /appointments/:id - Get by ID
		appointmentRoutes.PUT("/:id", handlers.UpdateAppointment)      // PUT /appointments/:id - Update
		appointmentRoutes.DELETE("/:id", handlers.DeleteAppointment)   // DELETE /appointments/:id - Delete
	}

	// Patient appointments
	patientRoutes := router.Group("/patients/:patient_id/appointments")
	{
		patientRoutes.GET("", handlers.GetPatientAppointments)         // GET /patients/:patient_id/appointments
	}

	// Doctor availability slots
	slotRoutes := router.Group("/slots")
	{
		slotRoutes.POST("", handlers.CreateSlot)                       // POST /slots - Create availability slot
		slotRoutes.GET("", handlers.GetAvailableSlots)                 // GET /slots - Get available slots
		slotRoutes.DELETE("/:id", handlers.DeleteSlot)                 // DELETE /slots/:id - Delete slot
	}

	// Appointment status management
	statusRoutes := router.Group("/appointments")
	{
		statusRoutes.PUT("/:id/confirm", handlers.ConfirmAppointment)  // PUT /appointments/:id/confirm
		statusRoutes.PUT("/:id/cancel", handlers.CancelAppointment)    // PUT /appointments/:id/cancel
		statusRoutes.PUT("/:id/complete", handlers.CompleteAppointment) // PUT /appointments/:id/complete
	}

	// Notifications
	notificationRoutes := router.Group("/notifications")
	{
		notificationRoutes.GET("", handlers.GetNotifications)          // GET /notifications
		notificationRoutes.POST("/:id/send", handlers.SendNotification) // POST /notifications/:id/send
	}
}
