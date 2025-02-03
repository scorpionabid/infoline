-- Create regions table
CREATE TABLE IF NOT EXISTS regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sectors table
CREATE TABLE IF NOT EXISTS sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    region_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create schools table if not exists
CREATE TABLE IF NOT EXISTS schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sector_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (sector_id) REFERENCES sectors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table if not exists
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update users table
ALTER TABLE users
    ADD COLUMN first_name VARCHAR(255) NOT NULL AFTER id,
    ADD COLUMN last_name VARCHAR(255) NOT NULL AFTER first_name,
    ADD COLUMN utis_code CHAR(7) UNIQUE NOT NULL AFTER last_name,
    MODIFY COLUMN role ENUM('superadmin', 'sectoradmin', 'schooladmin') NOT NULL,
    ADD COLUMN region_id INT NULL AFTER role,
    ADD COLUMN sector_id INT NULL AFTER region_id,
    ADD COLUMN school_id INT NULL AFTER sector_id,
    ADD COLUMN last_login_at TIMESTAMP NULL,
    ADD COLUMN email_verified_at TIMESTAMP NULL,
    ADD COLUMN remember_token VARCHAR(100) NULL,
    ADD COLUMN deleted_at TIMESTAMP NULL,
    ADD FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL,
    ADD FOREIGN KEY (sector_id) REFERENCES sectors(id) ON DELETE SET NULL,
    ADD FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL,
    ADD UNIQUE (username),
    ADD UNIQUE (email);

-- Add test data
INSERT INTO regions (name) VALUES 
    ('Bakı şəhəri'),
    ('Sumqayıt şəhəri'),
    ('Gəncə şəhəri');

-- Insert test sectors
INSERT INTO sectors (name, region_id) 
SELECT 'Binəqədi rayonu', id FROM regions WHERE name = 'Bakı şəhəri'
UNION ALL
SELECT 'Yasamal rayonu', id FROM regions WHERE name = 'Bakı şəhəri'
UNION ALL
SELECT '1-ci mikrorayon', id FROM regions WHERE name = 'Sumqayıt şəhəri'
UNION ALL
SELECT '2-ci mikrorayon', id FROM regions WHERE name = 'Sumqayıt şəhəri'
UNION ALL
SELECT 'Kəpəz rayonu', id FROM regions WHERE name = 'Gəncə şəhəri'
UNION ALL
SELECT 'Nizami rayonu', id FROM regions WHERE name = 'Gəncə şəhəri';

-- Create super admin
INSERT INTO users (first_name, last_name, username, email, password, utis_code, role) 
VALUES ('Super', 'Admin', 'superadmin', 'superadmin@example.com', 
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
        '1000000', 'superadmin');
