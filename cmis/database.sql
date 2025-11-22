/**
 * Database Schema for CMIS
 * Author: Justin Edwards
 */

-- Create database
CREATE DATABASE IF NOT EXISTS cmis_db;
USE cmis_db;

-- Users table (system login accounts)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    mem_id VARCHAR(10) NOT NULL UNIQUE,          -- Links to members table
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,              -- Hashed password
    role ENUM('admin', 'pastor', 'ministry_leader', 'clerk') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_mem_id (mem_id),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Members table (basic structure - Person B will expand this)
CREATE TABLE IF NOT EXISTS members (
    mem_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_initial VARCHAR(5),
    dob DATE,
    gender ENUM('Male', 'Female'),
    status ENUM('member', 'adherent', 'visitor', 'inactive') DEFAULT 'member',
    date_joined DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ministries table (basic structure - Person C will expand this)
CREATE TABLE IF NOT EXISTS ministries (
    ministry_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Attendance table (basic structure - Person C will expand this)
CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    ministry_id INT,
    count INT DEFAULT 0,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ministry_id) REFERENCES ministries(ministry_id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events table (basic structure - Person D will expand this)
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_type ENUM('wedding', 'birthday', 'anniversary', 'baptism', 'death') NOT NULL,
    date DATE NOT NULL,
    member_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(mem_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: Admin123!)
INSERT INTO users (mem_id, username, password, role, status) VALUES
('001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Insert sample members for testing
INSERT INTO members (first_name, last_name, dob, gender, status, date_joined) VALUES
('John', 'Doe', '1985-05-15', 'Male', 'member', '2020-01-10'),
('Jane', 'Smith', '1990-08-22', 'Female', 'member', '2021-03-15'),
('Bob', 'Johnson', '1975-12-03', 'Male', 'adherent', '2022-06-20');

-- Insert sample ministries
INSERT INTO ministries (name, description) VALUES
('Senior Choir', 'Main church choir for Sunday services'),
('Youth Ministry', 'Ministry for ages 12-25'),
('Ushering Board', 'Welcoming and seating congregation'),
('Women\'s Fellowship', 'Women\'s ministry and outreach');