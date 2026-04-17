CREATE TABLE IF NOT EXISTS invoices (
    Id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    patient_id BIGINT NULL,
    insurance_id BIGINT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATETIME NOT NULL,
    total_gross DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    total_discount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    total_net DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_invoice_number (clinic_id, invoice_number),
    KEY idx_invoice_patient (clinic_id, patient_id),
    KEY idx_invoice_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invoice_items (
    Id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    invoice_id BIGINT NOT NULL,
    service_id BIGINT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(18,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    KEY idx_invoice_items_invoice (invoice_id),
    KEY idx_invoice_items_clinic (clinic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
    Id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    invoice_id BIGINT NOT NULL,
    payment_date DATETIME NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    method VARCHAR(50) NOT NULL,
    reference VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    KEY idx_payments_invoice (invoice_id),
    KEY idx_payments_clinic (clinic_id),
    KEY idx_payments_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;