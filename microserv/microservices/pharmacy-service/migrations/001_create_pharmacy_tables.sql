-- Create Drugs Table
CREATE TABLE IF NOT EXISTS drugs (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    name VARCHAR(255) NOT NULL,
    description TEXT,
    license_number VARCHAR(100) NOT NULL UNIQUE,
    manufacturer VARCHAR(255),
    expiry_date DATE NOT NULL,
    storage_condition VARCHAR(255),
    price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_license_number (license_number),
    INDEX idx_expiry_date (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Drug Stock Table
CREATE TABLE IF NOT EXISTS drug_stocks (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    drug_id CHAR(36) NOT NULL,
    quantity INT DEFAULT 0,
    reorder_level INT DEFAULT 50,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_stock (drug_id, location),
    FOREIGN KEY (drug_id) REFERENCES drugs(id) ON DELETE CASCADE,
    INDEX idx_drug_id (drug_id),
    INDEX idx_quantity (quantity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Pharmacy Orders Table
CREATE TABLE IF NOT EXISTS pharmacy_orders (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    patient_id CHAR(36) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    total_amount DECIMAL(10, 2),
    payment_status VARCHAR(50) DEFAULT 'pending',
    ready_date DATE,
    pickup_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patient_id (patient_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    pharmacy_order_id CHAR(36) NOT NULL,
    drug_id CHAR(36) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2),
    subtotal DECIMAL(10, 2),
    FOREIGN KEY (pharmacy_order_id) REFERENCES pharmacy_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (drug_id) REFERENCES drugs(id),
    INDEX idx_pharmacy_order_id (pharmacy_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Drug Inventory Log Table
CREATE TABLE IF NOT EXISTS drug_inventory_log (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    drug_id CHAR(36) NOT NULL,
    transaction_type VARCHAR(50),
    quantity_change INT,
    previous_quantity INT,
    new_quantity INT,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drug_id) REFERENCES drugs(id) ON DELETE CASCADE,
    INDEX idx_drug_id (drug_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
