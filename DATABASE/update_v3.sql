-- Update script for Bike Renting Website
-- Adds pickup_location to abookings table

ALTER TABLE abookings ADD COLUMN IF NOT EXISTS pickup_location VARCHAR(255) AFTER bike_id;
ALTER TABLE abike ADD COLUMN IF NOT EXISTS address VARCHAR(255) AFTER id;
