-- Create Medical Records Table
CREATE TABLE IF NOT EXISTS medical_records (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    patient_id CHAR(36) NOT NULL,
    doctor_id CHAR(36) NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment TEXT,
    confidential BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_patient_id (patient_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Prescriptions Table
CREATE TABLE IF NOT EXISTS prescriptions (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    medical_record_id CHAR(36) NOT NULL,
    drug_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(50),
    duration INT COMMENT 'Duration in days',
    quantity INT,
    instructions TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE CASCADE,
    INDEX idx_medical_record_id (medical_record_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Lab Results Table
CREATE TABLE IF NOT EXISTS lab_results (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    medical_record_id CHAR(36) NOT NULL,
    test_name VARCHAR(255) NOT NULL,
    result VARCHAR(255),
    unit VARCHAR(50),
    reference_range VARCHAR(100),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE CASCADE,
    INDEX idx_medical_record_id (medical_record_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Clinical Notes Table
CREATE TABLE IF NOT EXISTS clinical_notes (
    id CHAR(36) PRIMARY KEY COMMENT 'UUID',
    medical_record_id CHAR(36) NOT NULL,
    note TEXT NOT NULL,
    vitals VARCHAR(255),
    symptoms TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_record_id) REFERENCES medical_records(id) ON DELETE CASCADE,
    INDEX idx_medical_record_id (medical_record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
