-- Opcional: defina aqui a base de dados alvo
-- CREATE DATABASE IF NOT EXISTS clinica
--   DEFAULT CHARACTER SET utf8mb4
--   COLLATE utf8mb4_general_ci;
-- USE clinica;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================
-- 3.1. Tabelas base (globais, sem clinic_id)
-- =========================================

CREATE TABLE IF NOT EXISTS clinics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    tax_id VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    clinic_id BIGINT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS role_user (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_role_user (user_id, role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS permission_role (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    role_id BIGINT NOT NULL,
    permission_id BIGINT NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_permission_role (role_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.2. Tabelas de menus (globais)
-- =========================================

CREATE TABLE IF NOT EXISTS menus (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    icon VARCHAR(100) NOT NULL,
    route VARCHAR(255) NULL,
    parent_id BIGINT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu_groups (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu_group_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    menu_group_id BIGINT NOT NULL,
    menu_id BIGINT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_menu_group_item (menu_group_id, menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_menu_groups (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    menu_group_id BIGINT NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_user_menu_group (user_id, menu_group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.3. Entidades gerais (com clinic_id)
-- =========================================

CREATE TABLE IF NOT EXISTS people (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    full_name VARCHAR(200) NOT NULL,
    birth_date DATE NULL,
    gender VARCHAR(20) NULL,
    tax_id VARCHAR(50) NULL,
    document_number VARCHAR(50) NULL,
    phone VARCHAR(50) NULL,
    mobile VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS patients (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    person_id BIGINT NOT NULL,
    medical_record_number VARCHAR(50) NOT NULL,
    insurance_id BIGINT NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_patients_mrn (clinic_id, medical_record_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS doctors (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    person_id BIGINT NOT NULL,
    specialty_id BIGINT NULL,
    department_id BIGINT NULL,
    registration_number VARCHAR(50) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS employees (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    person_id BIGINT NOT NULL,
    department_id BIGINT NULL,
    job_title VARCHAR(150) NULL,
    hire_date DATE NULL,
    termination_date DATE NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS insurances (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    contract_number VARCHAR(50) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    address VARCHAR(255) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS suppliers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    tax_id VARCHAR(50) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS specialties (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    description VARCHAR(255) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS departments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    description VARCHAR(255) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de numeração geral por clínica/tipo (usada pelo modelo Numerador)
CREATE TABLE IF NOT EXISTS general_numerator (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    type_num_id INT NOT NULL,
    mode ENUM('A','C') NOT NULL DEFAULT 'A', -- A = anual, C = contínua
    number BIGINT NOT NULL DEFAULT 0,
    number_year INT NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.4. Agenda e atendimento
-- =========================================

CREATE TABLE IF NOT EXISTS appointments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    doctor_id BIGINT NOT NULL,
    scheduled_at DATETIME NOT NULL,
    status VARCHAR(50) NOT NULL,
    reason VARCHAR(255) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS triages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    appointment_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    nurse_id BIGINT NULL,
    triage_time DATETIME NOT NULL,
    blood_pressure VARCHAR(20) NULL,
    heart_rate INT NULL,
    temperature DECIMAL(5,2) NULL,
    weight DECIMAL(10,2) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.5. Prontuário e actos clínicos
-- =========================================

CREATE TABLE IF NOT EXISTS clinical_records (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    doctor_id BIGINT NOT NULL,
    appointment_id BIGINT NULL,
    record_date DATETIME NOT NULL,
    summary TEXT NOT NULL,
    notes TEXT NULL,
    status VARCHAR(50) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS prescriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    doctor_id BIGINT NOT NULL,
    clinical_record_id BIGINT NULL,
    issued_at DATETIME NOT NULL,
    notes TEXT NULL,
    status VARCHAR(50) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS prescription_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    prescription_id BIGINT NOT NULL,
    medication_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NULL,
    frequency VARCHAR(100) NULL,
    duration VARCHAR(100) NULL,
    instructions TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS exam_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    doctor_id BIGINT NOT NULL,
    clinical_record_id BIGINT NULL,
    requested_at DATETIME NOT NULL,
    status VARCHAR(50) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS exam_request_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    exam_request_id BIGINT NOT NULL,
    exam_name VARCHAR(150) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS exam_results (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    exam_request_item_id BIGINT NOT NULL,
    result_date DATETIME NOT NULL,
    result TEXT NOT NULL,
    attachment_path VARCHAR(255) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.6. Serviços e facturação
-- =========================================

CREATE TABLE IF NOT EXISTS services (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    description VARCHAR(255) NULL,
    default_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS price_lists (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    valid_from DATE NULL,
    valid_to DATE NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS price_list_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    price_list_id BIGINT NOT NULL,
    service_id BIGINT NOT NULL,
    price DECIMAL(18,2) NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_price_list_item (price_list_id, service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invoices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
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
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_invoice_number (clinic_id, invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS invoice_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
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
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
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
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS electronic_invoices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    invoice_id BIGINT NOT NULL,
    status VARCHAR(50) NOT NULL,
    protocol_number VARCHAR(100) NULL,
    xml_path VARCHAR(255) NULL,
    pdf_path VARCHAR(255) NULL,
    sent_at DATETIME NULL,
    received_at DATETIME NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS credit_notes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    invoice_id BIGINT NOT NULL,
    credit_note_number VARCHAR(50) NOT NULL,
    credit_date DATETIME NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    reason TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.7. Tesouraria
-- =========================================

CREATE TABLE IF NOT EXISTS cash_registers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(150) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cash_sessions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    cash_register_id BIGINT NOT NULL,
    opened_by BIGINT NOT NULL,
    closed_by BIGINT NULL,
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    opening_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    closing_amount DECIMAL(18,2) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cash_movements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    cash_session_id BIGINT NOT NULL,
    type VARCHAR(10) NOT NULL,
    description VARCHAR(255) NULL,
    amount DECIMAL(18,2) NOT NULL,
    payment_id BIGINT NULL,
    movement_date DATETIME NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bank_accounts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    bank_name VARCHAR(150) NOT NULL,
    account_number VARCHAR(100) NOT NULL,
    iban VARCHAR(50) NULL,
    swift VARCHAR(50) NULL,
    opening_balance DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bank_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    bank_account_id BIGINT NOT NULL,
    transaction_date DATETIME NOT NULL,
    type VARCHAR(10) NOT NULL,
    description VARCHAR(255) NULL,
    amount DECIMAL(18,2) NOT NULL,
    reference VARCHAR(100) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.8. Farmácia e stock
-- =========================================

CREATE TABLE IF NOT EXISTS products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    barcode VARCHAR(100) NULL,
    description VARCHAR(255) NULL,
    unit VARCHAR(50) NULL,
    cost_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    description VARCHAR(255) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_category_product (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    product_category_id BIGINT NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_product_category_product (product_id, product_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS warehouses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    location VARCHAR(255) NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    warehouse_id BIGINT NOT NULL,
    quantity DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    min_quantity DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    max_quantity DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_stock_item (product_id, warehouse_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_batches (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    warehouse_id BIGINT NOT NULL,
    batch_number VARCHAR(100) NOT NULL,
    expiry_date DATE NULL,
    quantity DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_movements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    warehouse_id BIGINT NOT NULL,
    stock_batch_id BIGINT NULL,
    movement_date DATETIME NOT NULL,
    type VARCHAR(20) NOT NULL,
    quantity DECIMAL(18,2) NOT NULL,
    unit_cost DECIMAL(18,2) NOT NULL,
    reference_table VARCHAR(50) NULL,
    reference_id BIGINT NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS purchase_orders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    supplier_id BIGINT NOT NULL,
    order_number VARCHAR(50) NOT NULL,
    order_date DATETIME NOT NULL,
    status VARCHAR(50) NOT NULL,
    total_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_purchase_order (clinic_id, order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS purchase_order_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    purchase_order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity DECIMAL(18,2) NOT NULL,
    unit_cost DECIMAL(18,2) NOT NULL,
    total DECIMAL(18,2) NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS goods_receipts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    supplier_id BIGINT NOT NULL,
    purchase_order_id BIGINT NULL,
    receipt_number VARCHAR(50) NOT NULL,
    receipt_date DATETIME NOT NULL,
    total_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_goods_receipt (clinic_id, receipt_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS goods_receipt_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    goods_receipt_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    stock_batch_id BIGINT NULL,
    quantity DECIMAL(18,2) NOT NULL,
    unit_cost DECIMAL(18,2) NOT NULL,
    total DECIMAL(18,2) NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pharmacy_sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    patient_id BIGINT NULL,
    sale_number VARCHAR(50) NOT NULL,
    sale_date DATETIME NOT NULL,
    total_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    payment_status VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_pharmacy_sale (clinic_id, sale_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pharmacy_sale_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    pharmacy_sale_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity DECIMAL(18,2) NOT NULL,
    unit_price DECIMAL(18,2) NOT NULL,
    discount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(18,2) NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.9. Recursos humanos
-- =========================================

CREATE TABLE IF NOT EXISTS employee_contracts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    contract_type VARCHAR(50) NOT NULL,
    salary DECIMAL(18,2) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS shifts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    name VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS employee_schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    shift_id BIGINT NOT NULL,
    schedule_date DATE NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_employee_schedule (employee_id, schedule_date, shift_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS attendances (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    schedule_id BIGINT NULL,
    check_in DATETIME NULL,
    check_out DATETIME NULL,
    status VARCHAR(20) NULL,
    notes TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3.10. Auditoria
-- =========================================

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    clinic_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    entity VARCHAR(100) NOT NULL,
    entity_id BIGINT NOT NULL,
    action VARCHAR(50) NOT NULL,
    changes TEXT NULL,
    ip_address VARCHAR(50) NULL,
    user_agent VARCHAR(255) NULL,
    logged_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- FKs (ALTER TABLE após criação de todas as tabelas)
-- =========================================

-- 3.1 Base
ALTER TABLE users
    ADD CONSTRAINT fk_users_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE role_user
    ADD CONSTRAINT fk_role_user_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_role_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE permission_role
    ADD CONSTRAINT fk_permission_role_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_permission_role_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- 3.2 Menus
ALTER TABLE menus
    ADD CONSTRAINT fk_menus_parent FOREIGN KEY (parent_id) REFERENCES menus(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE menu_group_items
    ADD CONSTRAINT fk_menu_group_items_group FOREIGN KEY (menu_group_id) REFERENCES menu_groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_menu_group_items_menu FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE user_menu_groups
    ADD CONSTRAINT fk_user_menu_groups_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_user_menu_groups_group FOREIGN KEY (menu_group_id) REFERENCES menu_groups(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- 3.3 Entidades gerais
ALTER TABLE people
    ADD CONSTRAINT fk_people_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE patients
    ADD CONSTRAINT fk_patients_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_patients_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_patients_insurance FOREIGN KEY (insurance_id) REFERENCES insurances(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE doctors
    ADD CONSTRAINT fk_doctors_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_doctors_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_doctors_specialty FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT fk_doctors_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE employees
    ADD CONSTRAINT fk_employees_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_employees_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE insurances
    ADD CONSTRAINT fk_insurances_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE suppliers
    ADD CONSTRAINT fk_suppliers_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE specialties
    ADD CONSTRAINT fk_specialties_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE departments
    ADD CONSTRAINT fk_departments_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE general_numerator
    ADD CONSTRAINT fk_general_numerator_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- 3.4 Agenda e atendimento
ALTER TABLE appointments
    ADD CONSTRAINT fk_appointments_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_appointments_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE triages
    ADD CONSTRAINT fk_triages_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_triages_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_triages_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_triages_nurse FOREIGN KEY (nurse_id) REFERENCES employees(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- 3.5 Prontuário
ALTER TABLE clinical_records
    ADD CONSTRAINT fk_clinical_records_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_clinical_records_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_clinical_records_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_clinical_records_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE prescriptions
    ADD CONSTRAINT fk_prescriptions_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_prescriptions_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_prescriptions_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_prescriptions_record FOREIGN KEY (clinical_record_id) REFERENCES clinical_records(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE prescription_items
    ADD CONSTRAINT fk_prescription_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_prescription_items_prescription FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE exam_requests
    ADD CONSTRAINT fk_exam_requests_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_exam_requests_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_exam_requests_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_exam_requests_record FOREIGN KEY (clinical_record_id) REFERENCES clinical_records(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE exam_request_items
    ADD CONSTRAINT fk_exam_request_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_exam_request_items_request FOREIGN KEY (exam_request_id) REFERENCES exam_requests(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE exam_results
    ADD CONSTRAINT fk_exam_results_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_exam_results_item FOREIGN KEY (exam_request_item_id) REFERENCES exam_request_items(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- 3.6 Serviços e facturação
ALTER TABLE services
    ADD CONSTRAINT fk_services_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE price_lists
    ADD CONSTRAINT fk_price_lists_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE price_list_items
    ADD CONSTRAINT fk_price_list_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_price_list_items_list FOREIGN KEY (price_list_id) REFERENCES price_lists(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_price_list_items_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE invoices
    ADD CONSTRAINT fk_invoices_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_invoices_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT fk_invoices_insurance FOREIGN KEY (insurance_id) REFERENCES insurances(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE invoice_items
    ADD CONSTRAINT fk_invoice_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_invoice_items_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE payments
    ADD CONSTRAINT fk_payments_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE electronic_invoices
    ADD CONSTRAINT fk_electronic_invoices_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_electronic_invoices_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE credit_notes
    ADD CONSTRAINT fk_credit_notes_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_credit_notes_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- 3.7 Tesouraria
ALTER TABLE cash_registers
    ADD CONSTRAINT fk_cash_registers_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE cash_sessions
    ADD CONSTRAINT fk_cash_sessions_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_cash_sessions_register FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_cash_sessions_opened_by FOREIGN KEY (opened_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_cash_sessions_closed_by FOREIGN KEY (closed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE cash_movements
    ADD CONSTRAINT fk_cash_movements_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_cash_movements_session FOREIGN KEY (cash_session_id) REFERENCES cash_sessions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_cash_movements_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE bank_accounts
    ADD CONSTRAINT fk_bank_accounts_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE bank_transactions
    ADD CONSTRAINT fk_bank_transactions_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_bank_transactions_account FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- 3.8 Farmácia e stock
ALTER TABLE products
    ADD CONSTRAINT fk_products_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE product_categories
    ADD CONSTRAINT fk_product_categories_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE product_category_product
    ADD CONSTRAINT fk_product_category_product_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_product_category_product_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_product_category_product_category FOREIGN KEY (product_category_id) REFERENCES product_categories(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE warehouses
    ADD CONSTRAINT fk_warehouses_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE stock_items
    ADD CONSTRAINT fk_stock_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_items_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE stock_batches
    ADD CONSTRAINT fk_stock_batches_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_batches_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_batches_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE stock_movements
    ADD CONSTRAINT fk_stock_movements_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_movements_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_movements_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_stock_movements_batch FOREIGN KEY (stock_batch_id) REFERENCES stock_batches(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE purchase_orders
    ADD CONSTRAINT fk_purchase_orders_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_purchase_orders_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE purchase_order_items
    ADD CONSTRAINT fk_purchase_order_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_purchase_order_items_order FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_purchase_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE goods_receipts
    ADD CONSTRAINT fk_goods_receipts_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_goods_receipts_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_goods_receipts_order FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE goods_receipt_items
    ADD CONSTRAINT fk_goods_receipt_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_goods_receipt_items_receipt FOREIGN KEY (goods_receipt_id) REFERENCES goods_receipts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_goods_receipt_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_goods_receipt_items_batch FOREIGN KEY (stock_batch_id) REFERENCES stock_batches(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE pharmacy_sales
    ADD CONSTRAINT fk_pharmacy_sales_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_pharmacy_sales_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE pharmacy_sale_items
    ADD CONSTRAINT fk_pharmacy_sale_items_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_pharmacy_sale_items_sale FOREIGN KEY (pharmacy_sale_id) REFERENCES pharmacy_sales(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_pharmacy_sale_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- 3.9 Recursos humanos
ALTER TABLE employee_contracts
    ADD CONSTRAINT fk_employee_contracts_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_employee_contracts_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE shifts
    ADD CONSTRAINT fk_shifts_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE employee_schedules
    ADD CONSTRAINT fk_employee_schedules_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_employee_schedules_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_employee_schedules_shift FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE attendances
    ADD CONSTRAINT fk_attendances_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_attendances_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_attendances_schedule FOREIGN KEY (schedule_id) REFERENCES employee_schedules(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- 3.10 Auditoria
ALTER TABLE audit_logs
    ADD CONSTRAINT fk_audit_logs_clinic FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
