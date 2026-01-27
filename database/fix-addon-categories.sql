-- Fix addon_services category column to include new categories
-- Run this in phpMyAdmin

USE tufanconx_tufanresort;

-- Update category ENUM to include new values
ALTER TABLE addon_services 
MODIFY COLUMN category ENUM('decoration', 'sound_system', 'photography', 'catering', 'transport', 'room_service', 'laundry', 'parking', 'other') NOT NULL DEFAULT 'other';

-- Check if service_type column exists, if not add it
-- Run this separately if the above works:
-- ALTER TABLE addon_services ADD COLUMN service_type ENUM('room', 'convention', 'both') NOT NULL DEFAULT 'convention' AFTER category;
