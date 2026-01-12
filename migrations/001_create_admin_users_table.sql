-- Migration: Create admin users table
-- Created: 2026-01-12
-- Description: Add database-backed admin authentication with Argon2id password hashing

-- Create admin users table
CREATE TABLE IF NOT EXISTS `chronos_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for admin activity log
CREATE TABLE IF NOT EXISTS `chronos_admin_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `fk_admin_log_user` FOREIGN KEY (`admin_id`) REFERENCES `chronos_admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: Initial admin user should be created via setup script
-- Default password will be set during migration and should be changed immediately
