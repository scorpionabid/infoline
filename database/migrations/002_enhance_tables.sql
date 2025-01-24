-- Enhance schools table
-- First add code column as nullable
SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN code VARCHAR(50) UNIQUE NULL AFTER name',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'code' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing schools with a generated code
UPDATE schools SET code = CONCAT('SCH', LPAD(id, 5, '0')) WHERE code IS NULL;

-- Now make the code column NOT NULL
ALTER TABLE schools MODIFY COLUMN code VARCHAR(50) NOT NULL UNIQUE;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN region VARCHAR(100) AFTER code',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'region' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN address TEXT AFTER region',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'address' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN phone VARCHAR(20) AFTER address',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'phone' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN principal_name VARCHAR(100) AFTER phone',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'principal_name' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN status ENUM("active", "inactive") DEFAULT "active" AFTER principal_name',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'status' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE schools ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'schools' AND COLUMN_NAME = 'updated_at' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Enhance columns table
SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE columns ADD COLUMN description TEXT AFTER name',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'columns' AND COLUMN_NAME = 'description' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE columns ADD COLUMN created_by INT NOT NULL AFTER description',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'columns' AND COLUMN_NAME = 'created_by' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE columns ADD COLUMN is_required BOOLEAN DEFAULT false AFTER created_by',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'columns' AND COLUMN_NAME = 'is_required' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE columns ADD COLUMN validation_rules TEXT AFTER is_required',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'columns' AND COLUMN_NAME = 'validation_rules' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE columns MODIFY COLUMN type ENUM('text', 'number', 'date', 'select', 'multiple') NOT NULL;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE columns ADD COLUMN options TEXT NULL AFTER type',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'columns' AND COLUMN_NAME = 'options' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key if not exists
SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'columns'
        AND REFERENCED_TABLE_NAME = 'users'
        AND CONSTRAINT_SCHEMA = DATABASE()
    ) > 0,
    'SELECT 1',
    'ALTER TABLE columns ADD FOREIGN KEY (created_by) REFERENCES users(id)'
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Enhance data_values table
SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE data_values ADD COLUMN updated_by INT AFTER value',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'data_values' AND COLUMN_NAME = 'updated_by' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE data_values ADD COLUMN status ENUM("draft", "submitted", "approved", "rejected") DEFAULT "draft" AFTER updated_by',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'data_values' AND COLUMN_NAME = 'status' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE data_values ADD COLUMN comment TEXT AFTER status',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'data_values' AND COLUMN_NAME = 'comment' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key if not exists
SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'data_values'
        AND REFERENCED_TABLE_NAME = 'users'
        AND CONSTRAINT_SCHEMA = DATABASE()
    ) > 0,
    'SELECT 1',
    'ALTER TABLE data_values ADD FOREIGN KEY (updated_by) REFERENCES users(id)'
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Enhance notifications table
ALTER TABLE notifications MODIFY COLUMN type ENUM('new_column', 'deadline', 'reminder', 'system') NOT NULL;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE notifications ADD COLUMN priority ENUM("low", "medium", "high") DEFAULT "medium" AFTER type',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'notifications' AND COLUMN_NAME = 'priority' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE notifications ADD COLUMN related_entity_type VARCHAR(50) AFTER priority',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'notifications' AND COLUMN_NAME = 'related_entity_type' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE notifications ADD COLUMN related_entity_id INT AFTER related_entity_type',
    'SELECT 1'
) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'notifications' AND COLUMN_NAME = 'related_entity_id' AND TABLE_SCHEMA = DATABASE());
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create audit_logs table if not exists
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes if not exists
CREATE INDEX IF NOT EXISTS idx_schools_code ON schools(code);
CREATE INDEX IF NOT EXISTS idx_schools_region ON schools(region);
CREATE INDEX IF NOT EXISTS idx_columns_type ON columns(type);
CREATE INDEX IF NOT EXISTS idx_data_values_status ON data_values(status);
CREATE INDEX IF NOT EXISTS idx_notifications_type ON notifications(type);
CREATE INDEX IF NOT EXISTS idx_audit_logs_entity ON audit_logs(entity_type, entity_id);