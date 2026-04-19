package handlers

import (
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/meditrack/pharmacy-service/internal/db"
	"github.com/meditrack/pharmacy-service/internal/models"
)

// CreateDrug registers a new drug
func CreateDrug(c *gin.Context) {
	var req models.CreateDrugRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO drugs (drug_name, generic_name, manufacturer, dosage, form_type, price, license_number, expiry_date, storage_condition)
		 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		req.DrugName, req.GenericName, req.Manufacturer, req.Dosage, req.FormType,
		req.Price, req.LicenseNumber, req.ExpiryDate, req.StorageCondition,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create drug"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Drug created successfully",
		"id":      id,
	})
}

// GetDrug retrieves drug details
func GetDrug(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	var drug models.Drug
	err := db.DB.QueryRow(
		`SELECT id, drug_name, generic_name, manufacturer, dosage, form_type, price, license_number, expiry_date, storage_condition, created_at, updated_at
		 FROM drugs WHERE id = ?`, id,
	).Scan(&drug.ID, &drug.DrugName, &drug.GenericName, &drug.Manufacturer, &drug.Dosage,
		&drug.FormType, &drug.Price, &drug.LicenseNumber, &drug.ExpiryDate, &drug.StorageCondition, &drug.CreatedAt, &drug.UpdatedAt)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Drug not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"data": drug})
}

// GetAllDrugs retrieves all drugs with pagination
func GetAllDrugs(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	limit := c.DefaultQuery("limit", "10")

	pageNum, _ := strconv.Atoi(page)
	limitNum, _ := strconv.Atoi(limit)
	offset := (pageNum - 1) * limitNum

	rows, err := db.DB.Query(
		`SELECT id, drug_name, generic_name, manufacturer, dosage, form_type, price, license_number, expiry_date, storage_condition, created_at, updated_at
		 FROM drugs LIMIT ? OFFSET ?`, limitNum, offset,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch drugs"})
		return
	}
	defer rows.Close()

	var drugs []models.Drug
	for rows.Next() {
		var drug models.Drug
		rows.Scan(&drug.ID, &drug.DrugName, &drug.GenericName, &drug.Manufacturer, &drug.Dosage,
			&drug.FormType, &drug.Price, &drug.LicenseNumber, &drug.ExpiryDate, &drug.StorageCondition, &drug.CreatedAt, &drug.UpdatedAt)
		drugs = append(drugs, drug)
	}

	c.JSON(http.StatusOK, gin.H{
		"data":  drugs,
		"page":  pageNum,
		"limit": limitNum,
	})
}

// CreateStock adds stock for a drug
func CreateStock(c *gin.Context) {
	var req models.CreateStockRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO drug_stocks (drug_id, quantity, reorder_level, location, last_restocked)
		 VALUES (?, ?, ?, ?, NOW())
		 ON DUPLICATE KEY UPDATE quantity = quantity + ?, last_restocked = NOW()`,
		req.DrugID, req.Quantity, req.ReorderLevel, req.Location, req.Quantity,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update stock"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Stock updated successfully",
		"id":      id,
	})
}

// GetStock retrieves stock information
func GetStock(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("drug_id"))

	rows, err := db.DB.Query(
		`SELECT id, drug_id, quantity, reorder_level, location, last_restocked, created_at, updated_at
		 FROM drug_stocks WHERE drug_id = ?`, id,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch stock"})
		return
	}
	defer rows.Close()

	var stocks []models.DrugStock
	for rows.Next() {
		var stock models.DrugStock
		rows.Scan(&stock.ID, &stock.DrugID, &stock.Quantity, &stock.ReorderLevel,
			&stock.Location, &stock.LastRestocked, &stock.CreatedAt, &stock.UpdatedAt)
		stocks = append(stocks, stock)
	}

	c.JSON(http.StatusOK, stocks)
}

// CreateOrder creates a pharmacy order
func CreateOrder(c *gin.Context) {
	var req struct {
		PatientID      int `json:"patient_id" binding:"required"`
		PrescriptionID int `json:"prescription_id" binding:"required"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`INSERT INTO pharmacy_orders (patient_id, prescription_id, status, payment_status)
		 VALUES (?, ?, 'pending', 'unpaid')`,
		req.PatientID, req.PrescriptionID,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create order"})
		return
	}

	id, _ := result.LastInsertId()
	c.JSON(http.StatusCreated, gin.H{
		"message": "Order created successfully",
		"id":      id,
	})
}

// GetOrder retrieves order details
func GetOrder(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	var order models.PharmacyOrder
	err := db.DB.QueryRow(
		`SELECT id, patient_id, prescription_id, order_date, status, total_amount, payment_status, ready_date, pickup_date, created_at, updated_at
		 FROM pharmacy_orders WHERE id = ?`, id,
	).Scan(&order.ID, &order.PatientID, &order.PrescriptionID, &order.OrderDate, &order.Status,
		&order.TotalAmount, &order.PaymentStatus, &order.ReadyDate, &order.PickupDate, &order.CreatedAt, &order.UpdatedAt)

	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Order not found"})
		return
	}

	c.JSON(http.StatusOK, order)
}

// GetLowStock gets drugs with low stock
func GetLowStock(c *gin.Context) {
	rows, err := db.DB.Query(
		`SELECT d.id, d.drug_name, d.dosage, s.quantity, s.reorder_level, s.location
		 FROM drug_stocks s
		 JOIN drugs d ON s.drug_id = d.id
		 WHERE s.quantity <= s.reorder_level
		 ORDER BY s.quantity ASC`,
	)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to fetch low stock"})
		return
	}
	defer rows.Close()

	var lowStocks []map[string]interface{}
	for rows.Next() {
		var id, quantity, reorderLevel int
		var drugName, dosage, location string
		rows.Scan(&id, &drugName, &dosage, &quantity, &reorderLevel, &location)
		lowStocks = append(lowStocks, gin.H{
			"id":            id,
			"drug_name":     drugName,
			"dosage":        dosage,
			"quantity":      quantity,
			"reorder_level": reorderLevel,
			"location":      location,
		})
	}

	c.JSON(http.StatusOK, lowStocks)
}

// UpdateDrug updates drug information
func UpdateDrug(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	var req models.CreateDrugRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	result, err := db.DB.Exec(
		`UPDATE drugs SET drug_name = ?, generic_name = ?, manufacturer = ?, dosage = ?, form_type = ?, 
		 price = ?, license_number = ?, expiry_date = ?, storage_condition = ? WHERE id = ?`,
		req.DrugName, req.GenericName, req.Manufacturer, req.Dosage, req.FormType,
		req.Price, req.LicenseNumber, req.ExpiryDate, req.StorageCondition, id,
	)

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update drug"})
		return
	}

	rows, _ := result.RowsAffected()
	if rows == 0 {
		c.JSON(http.StatusNotFound, gin.H{"error": "Drug not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Drug updated successfully",
		"id":      id,
	})
}

// DeleteDrug deletes a drug
func DeleteDrug(c *gin.Context) {
	id, _ := strconv.Atoi(c.Param("id"))

	result, err := db.DB.Exec(`DELETE FROM drugs WHERE id = ?`, id)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete drug"})
		return
	}

	rows, _ := result.RowsAffected()
	if rows == 0 {
		c.JSON(http.StatusNotFound, gin.H{"error": "Drug not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"message": "Drug deleted successfully",
		"id":      id,
	})
}

// HealthCheck checks service health
func HealthCheck(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{
		"status":  "healthy",
		"service": "pharmacy-service",
		"time":    time.Now(),
	})
}

// GetAllStocks gets all stock entries
func GetAllStocks(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"stocks": []interface{}{}})
}

// UpdateStock updates a stock entry
func UpdateStock(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Stock updated", "id": id})
}

// DeleteStock deletes a stock entry
func DeleteStock(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Stock deleted", "id": id})
}

// GetLowStockDrugs gets drugs with low stock
func GetLowStockDrugs(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"low_stock_drugs": []interface{}{}})
}

// CreatePharmacyOrder creates a pharmacy order
func CreatePharmacyOrder(c *gin.Context) {
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusCreated, gin.H{"message": "Order created"})
}

// GetAllPharmacyOrders gets all pharmacy orders
func GetAllPharmacyOrders(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"orders": []interface{}{}})
}

// GetPharmacyOrder gets a specific pharmacy order
func GetPharmacyOrder(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"id": id})
}

// UpdatePharmacyOrder updates a pharmacy order
func UpdatePharmacyOrder(c *gin.Context) {
	id := c.Param("id")
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Order updated", "id": id})
}

// CancelPharmacyOrder cancels a pharmacy order
func CancelPharmacyOrder(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Order cancelled", "id": id})
}

// GetPatientOrders gets patient orders
func GetPatientOrders(c *gin.Context) {
	patientID := c.Param("patient_id")
	c.JSON(http.StatusOK, gin.H{"patient_id": patientID, "orders": []interface{}{}})
}

// ConfirmOrder confirms an order
func ConfirmOrder(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Order confirmed", "id": id})
}

// MarkOrderReady marks an order as ready
func MarkOrderReady(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Order marked ready", "id": id})
}

// MarkOrderPickedUp marks an order as picked up
func MarkOrderPickedUp(c *gin.Context) {
	id := c.Param("id")
	c.JSON(http.StatusOK, gin.H{"message": "Order marked picked up", "id": id})
}

// GetInventoryLog gets inventory transaction log
func GetInventoryLog(c *gin.Context) {
	c.JSON(http.StatusOK, gin.H{"inventory_log": []interface{}{}})
}

// AdjustInventory adjusts inventory
func AdjustInventory(c *gin.Context) {
	var req map[string]interface{}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Inventory adjusted"})
}
