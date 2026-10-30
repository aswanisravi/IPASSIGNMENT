-- ==========================================================
-- STUDENT LOGIN PORTAL - DATABASE SCHEMA
-- Compatible with MySQL / MariaDB (phpMyAdmin / XAMPP)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `ip_assignment_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ip_assignment_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` VARCHAR(50) NOT NULL UNIQUE,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `name` VARCHAR(200) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `address` TEXT DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'Student',
  `reset_code` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Messages Table
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'General Inquiry',
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Seed Demo Student Accounts
-- Password: Password123 (Contains capital P, lowercase, and numbers 123)
INSERT INTO `users` (`student_id`, `first_name`, `last_name`, `name`, `email`, `address`, `phone`, `password`, `role`) VALUES
('1001', 'Alex', 'Morgan', 'Alex Morgan', 'alex@gmail.com', '123 Campus Lane, University Town', '9876543210', '$2y$10$wE3/qQJ6m1wS8xPcqYjY7.0xK63q.qAfePsmk.YQ8K24vA2L9K6m.', 'Student'),
('1002', 'Aswani', 'Ravi', 'Aswani Ravi', 'aswani@gmail.com', 'College Hostel Block A, Tech Campus', '9123456780', '$2y$10$wE3/qQJ6m1wS8xPcqYjY7.0xK63q.qAfePsmk.YQ8K24vA2L9K6m.', 'Student')
ON DUPLICATE KEY UPDATE `name`=`name`;
