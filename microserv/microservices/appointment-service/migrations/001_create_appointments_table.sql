-- Create Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    patient_id CHAR(36) NOT NULL,
    doctor_id CHAR(36) NOT NULL,
    appointment_date DATETIME NOT NULL,
    status VARCHAR(50) DEFAULT 'scheduled',
    type VARCHAR(50),
    location VARCHAR(255),
    duration INT DEFAULT 30 COMMENT 'Duration in minutes',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patient_id (patient_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Appointment Slots Table
CREATE TABLE IF NOT EXISTS appointment_slots (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    doctor_id CHAR(36) NOT NULL,
    slot_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slot (doctor_id, slot_date, start_time),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_slot_date (slot_date),
    INDEX idx_is_available (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Appointment Notifications Table
CREATE TABLE IF NOT EXISTS appointment_notifications (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    appointment_id CHAR(36) NOT NULL,
    type VARCHAR(50),
    notification_text TEXT,
    send_to VARCHAR(255),
    sent_at TIMESTAMP NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_appointment_id (appointment_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
