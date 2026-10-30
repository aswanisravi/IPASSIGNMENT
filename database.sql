-- ==========================================================
-- IP ASSIGNMENT: RESPONSIVE PHP & MYSQL WEB APPLICATION
-- Database Schema & Sample Records
-- ==========================================================

-- 1. Create Database (if using phpMyAdmin / MySQL CLI)
CREATE DATABASE IF NOT EXISTS `ip_assignment_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ip_assignment_db`;

-- 2. Create Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'Student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Contact Messages Table
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'General Inquiry',
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insert Demo Accounts
-- Default Password for both demo accounts: password123
-- Stored with standard BCRYPT password_hash
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`) VALUES
('Alex Morgan', 'alex@example.com', '9876543210', '$2y$10$wE3/qQJ6m1wS8xPcqYjY7.0xK63q.qAfePsmk.YQ8K24vA2L9K6m.', 'Student'),
('Admin User', 'admin@example.com', '9123456780', '$2y$10$wE3/qQJ6m1wS8xPcqYjY7.0xK63q.qAfePsmk.YQ8K24vA2L9K6m.', 'Administrator')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- 5. Insert Sample Feedback Message
INSERT INTO `messages` (`name`, `email`, `subject`, `category`, `message`) VALUES
('Alex Morgan', 'alex@example.com', 'Assignment Project Feedback', 'Feedback', 'The Navy Blue and Gold responsive layout looks stunning and functions seamlessly on mobile and desktop!')
ON DUPLICATE KEY UPDATE `name`=`name`;

