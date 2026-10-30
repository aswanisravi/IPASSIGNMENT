-- ===============================================================
-- STUDENT PORTAL DATABASE SCHEMA
-- MySQL Database: student_portal
-- Compatible with XAMPP (phpMyAdmin / MySQL CLI)
-- ===============================================================

CREATE DATABASE IF NOT EXISTS `student_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `student_portal`;

-- 1. Table: students
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` VARCHAR(12) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) DEFAULT '',
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table: reset_codes
CREATE TABLE IF NOT EXISTS `reset_codes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL,
  `code` VARCHAR(6) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Demo Student Account for Instant Evaluation:
-- Student ID: 2024-01-01 (or 20240101)
-- Name: Aswani Ravi
-- Email: aswani123@gmail.com
-- Password: Password123 (Password meets rule: 8+ chars, capital, small, number)
INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `password_hash`)
VALUES (
  '2024-01-01',
  'Aswani',
  'Ravi',
  'aswani123@gmail.com',
  '$2y$10$hO5fBJsFXCQg1ypQl/VykeIB1rI08rB1TQGwKzk1CetZpqXwgy9fO'
)
ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`), `student_id` = VALUES(`student_id`);

