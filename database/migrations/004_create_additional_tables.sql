-- Update regions table to add phone_number
ALTER TABLE regions
    ADD COLUMN phone_number VARCHAR(20) NULL;

-- Update sectors table to add phone
ALTER TABLE sectors
    ADD COLUMN phone VARCHAR(20) NULL;

-- Update schools table
ALTER TABLE schools
    ADD COLUMN utis_code CHAR(7) UNIQUE NOT NULL,
    ADD COLUMN phone VARCHAR(20) NULL,
    ADD COLUMN email VARCHAR(100) NULL;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create columns table
CREATE TABLE IF NOT EXISTS columns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    data_type ENUM('text', 'number', 'date', 'select', 'multiselect', 'file') NOT NULL,
    end_date DATE NULL,
    input_limit INT NULL,
    category_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create column_choices table for select and multiselect options
CREATE TABLE IF NOT EXISTS column_choices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    column_id INT NOT NULL,
    choice_value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (column_id) REFERENCES columns(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
