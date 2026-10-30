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
-- Student ID: 20240101
-- Name: Aswani Ravi
-- Email: aswani123@gmail.com
-- Password: Password123 (Password meets rule: 8+ chars, capital, small, number)
INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `password_hash`)
VALUES (
  '20240101',
  'Aswani',
  'Ravi',
  'aswani123@gmail.com',
  '$2y$10$wE3/qQJ6m1wS8xPcqYjY7.0xK63q.qAfePsmk.YQ8K24vA2L9K6m.'
)
ON DUPLICATE KEY UPDATE `student_id` = `student_id`;
