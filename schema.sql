-- Vice City Services Database Schema
-- MySQL Database Creation Script

-- Create database
CREATE DATABASE IF NOT EXISTS vice_city_services
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE vice_city_services;

-- =====================================================
-- 1. USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password using password_hash()',
    role ENUM('customer', 'agency') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. CARS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS cars (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NOT NULL,
    vehicle_model VARCHAR(100) NOT NULL,
    vehicle_number VARCHAR(50) NOT NULL UNIQUE,
    seating_capacity TINYINT UNSIGNED NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_agency_id (agency_id),
    INDEX idx_vehicle_number (vehicle_number),
    INDEX idx_rent_per_day (rent_per_day),
    INDEX idx_created_at (created_at),
    
    CONSTRAINT fk_cars_agency
        FOREIGN KEY (agency_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT chk_seating_capacity
        CHECK (seating_capacity > 0 AND seating_capacity <= 50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. BOOKINGS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    car_id INT UNSIGNED NOT NULL,
    number_of_days INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_customer_id (customer_id),
    INDEX idx_car_id (car_id),
    INDEX idx_start_date (start_date),
    INDEX idx_created_at (created_at),
    
    CONSTRAINT fk_bookings_customer
        FOREIGN KEY (customer_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_bookings_car
        FOREIGN KEY (car_id)
        REFERENCES cars(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT chk_number_of_days
        CHECK (number_of_days > 0 AND number_of_days <= 365)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA (Optional - Remove if not needed)
-- =====================================================

-- Insert sample agency user
-- Password: agency123 (hashed using PHP password_hash)
INSERT INTO users (name, email, password, role) VALUES
('ABC Car Rentals', 'agency@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agency'),
('XYZ Motors', 'xyz@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agency');

-- Insert sample customer user
-- Password: customer123 (hashed using PHP password_hash)
INSERT INTO users (name, email, password, role) VALUES
('John Doe', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert sample cars
INSERT INTO cars (agency_id, vehicle_model, vehicle_number, seating_capacity, rent_per_day) VALUES
(1, 'Toyota Camry', 'VC-1234', 5, 2500.00),
(1, 'Honda City', 'VC-5678', 5, 2000.00),
(2, 'Hyundai Creta', 'VC-9101', 7, 3500.00);

-- Insert sample bookings
INSERT INTO bookings (customer_id, car_id, number_of_days, start_date) VALUES
(3, 1, 3, '2026-02-15'),
(3, 2, 5, '2026-02-20');

-- =====================================================
-- VERIFY SCHEMA
-- =====================================================
-- Show all tables
SHOW TABLES;

-- Describe each table structure
DESCRIBE users;
DESCRIBE cars;
DESCRIBE bookings;
