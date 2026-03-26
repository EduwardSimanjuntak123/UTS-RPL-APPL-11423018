-- Create Service Metrics Table
CREATE TABLE IF NOT EXISTS service_metrics (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    service_name VARCHAR(100),
    response_time_ms FLOAT,
    request_count BIGINT DEFAULT 0,
    error_count BIGINT DEFAULT 0,
    throughput_kbs FLOAT,
    status VARCHAR(50) DEFAULT 'healthy',
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_service_name (service_name),
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create User Analytics Table
CREATE TABLE IF NOT EXISTS user_analytics (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    day DATE NOT NULL,
    active_users INT DEFAULT 0,
    new_users INT DEFAULT 0,
    feature_usage JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_day (day),
    INDEX idx_day (day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Appointment Analytics Table
CREATE TABLE IF NOT EXISTS appointment_analytics (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    day DATE NOT NULL,
    total_appointments INT DEFAULT 0,
    completed_appointments INT DEFAULT 0,
    cancelled_appointments INT DEFAULT 0,
    average_duration_min FLOAT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_day (day),
    INDEX idx_day (day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Revenue Analytics Table
CREATE TABLE IF NOT EXISTS revenue_analytics (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    day DATE NOT NULL,
    total_revenue DECIMAL(10, 2),
    paid_invoices INT DEFAULT 0,
    pending_invoices INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_day (day),
    INDEX idx_day (day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Health Indicators Table
CREATE TABLE IF NOT EXISTS health_indicators (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    service_name VARCHAR(100),
    status VARCHAR(50) DEFAULT 'healthy',
    last_check TIMESTAMP,
    uptime_percentage FLOAT,
    error_rate FLOAT,
    response_time_ms FLOAT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_service_name (service_name),
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create System Alerts Table
CREATE TABLE IF NOT EXISTS system_alerts (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    alert_type VARCHAR(50),
    severity VARCHAR(50) DEFAULT 'info',
    message TEXT,
    service_name VARCHAR(100),
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    INDEX idx_service_name (service_name),
    INDEX idx_status (status),
    INDEX idx_severity (severity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
