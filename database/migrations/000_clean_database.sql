-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS=0;

-- Drop all existing tables
DROP TABLE IF EXISTS data;
DROP TABLE IF EXISTS data_values;
DROP TABLE IF EXISTS columns;
DROP TABLE IF EXISTS migrations;
DROP TABLE IF EXISTS schools;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS sectors;
DROP TABLE IF EXISTS regions;

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
