package routes

import (
	"github.com/gin-gonic/gin"
	"github.com/meditrack/medical-service/internal/handlers"
)

// SetupMedicalRoutes sets up all medical service routes
func SetupMedicalRoutes(router *gin.Engine) {
	// Health check
	router.GET("/health", handlers.HealthCheck)

	// Medical Records endpoints
	recordRoutes := router.Group("/medical-records")
	{
		recordRoutes.POST("", handlers.CreateMedicalRecord)            // POST /medical-records - Create record
		recordRoutes.GET("", handlers.GetAllMedicalRecords)            // GET /medical-records - List all
		recordRoutes.GET("/:id", handlers.GetMedicalRecord)            // GET /medical-records/:id - Get by ID
		recordRoutes.PUT("/:id", handlers.UpdateMedicalRecord)         // PUT /medical-records/:id - Update
		recordRoutes.DELETE("/:id", handlers.DeleteMedicalRecord)      // DELETE /medical-records/:id - Delete
	}

	// Patient medical records
	patientRoutes := router.Group("/patients/:patient_id/medical-records")
	{
		patientRoutes.GET("", handlers.GetPatientMedicalRecords)       // GET /patients/:patient_id/medical-records
	}

	// Prescriptions
	prescriptionRoutes := router.Group("/prescriptions")
	{
		prescriptionRoutes.POST("", handlers.CreatePrescription)       // POST /prescriptions - Create prescription
		prescriptionRoutes.GET("", handlers.GetAllPrescriptions)       // GET /prescriptions - List all
		prescriptionRoutes.GET("/:id", handlers.GetPrescription)       // GET /prescriptions/:id - Get by ID
		prescriptionRoutes.PUT("/:id", handlers.UpdatePrescription)    // PUT /prescriptions/:id - Update
		prescriptionRoutes.DELETE("/:id", handlers.DeletePrescription) // DELETE /prescriptions/:id - Delete
	}

	// Patient prescriptions
	patientRx := router.Group("/patients/:patient_id/prescriptions")
	{
		patientRx.GET("", handlers.GetPatientPrescriptions)            // GET /patients/:patient_id/prescriptions
	}

	// Lab Results
	labRoutes := router.Group("/lab-results")
	{
		labRoutes.POST("", handlers.CreateLabResult)                   // POST /lab-results - Create result
		labRoutes.GET("", handlers.GetAllLabResults)                   // GET /lab-results - List all
		labRoutes.GET("/:id", handlers.GetLabResult)                   // GET /lab-results/:id - Get by ID
		labRoutes.PUT("/:id", handlers.UpdateLabResult)                // PUT /lab-results/:id - Update
		labRoutes.DELETE("/:id", handlers.DeleteLabResult)             // DELETE /lab-results/:id - Delete
	}

	// Clinical Notes
	noteRoutes := router.Group("/clinical-notes")
	{
		noteRoutes.POST("", handlers.CreateClinicalNote)               // POST /clinical-notes - Create note
		noteRoutes.GET("/:id", handlers.GetClinicalNote)               // GET /clinical-notes/:id - Get by ID
		noteRoutes.PUT("/:id", handlers.UpdateClinicalNote)            // PUT /clinical-notes/:id - Update
		noteRoutes.DELETE("/:id", handlers.DeleteClinicalNote)         // DELETE /clinical-notes/:id - Delete
	}
}
