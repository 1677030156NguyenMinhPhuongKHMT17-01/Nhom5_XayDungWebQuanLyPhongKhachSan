-- ================================================================
-- Database Migration Script for Personal Profile Feature
-- Hotel Management System
-- ================================================================
-- This script adds profile fields to the users table
-- Run this if you already have an existing database
-- ================================================================

USE ql_phongks;

-- Check if columns don't already exist before adding them
-- Add email column if not exists
SET @col_exists = (SELECT COUNT(*)
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = 'ql_phongks'
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'email');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `email` varchar(100) DEFAULT NULL',
    'SELECT "Column email already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add full_name column if not exists
SET @col_exists = (SELECT COUNT(*)
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = 'ql_phongks'
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'full_name');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `full_name` varchar(100) DEFAULT NULL',
    'SELECT "Column full_name already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add phone column if not exists
SET @col_exists = (SELECT COUNT(*)
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = 'ql_phongks'
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'phone');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `phone` varchar(20) DEFAULT NULL',
    'SELECT "Column phone already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add bio column if not exists
SET @col_exists = (SELECT COUNT(*)
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = 'ql_phongks'
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'bio');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `bio` text DEFAULT NULL',
    'SELECT "Column bio already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add avatar column if not exists
SET @col_exists = (SELECT COUNT(*)
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = 'ql_phongks'
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'avatar');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `avatar` varchar(255) DEFAULT NULL',
    'SELECT "Column avatar already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add created_at column if not exists
SET @col_exists = (SELECT COUNT(*)
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = 'ql_phongks'
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'created_at');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP',
    'SELECT "Column created_at already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add unique constraint on email if not exists
SET @index_exists = (SELECT COUNT(*)
                     FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_SCHEMA = 'ql_phongks'
                     AND TABLE_NAME = 'users'
                     AND INDEX_NAME = 'email_UNIQUE');

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE `users` ADD UNIQUE KEY `email_UNIQUE` (`email`)',
    'SELECT "Index email_UNIQUE already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Display completion message
SELECT 'Database migration completed successfully!' AS status;
SELECT 'Personal profile feature is now ready to use.' AS message;

-- Show updated table structure
DESCRIBE users;
