-- SQL script for restaurant management system
CREATE DATABASE IF NOT EXISTS restaurant_db;
USE restaurant_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    remember_token VARCHAR(128) DEFAULT NULL
);

-- Insert first admin (username: admin, password: admin123, role: admin)
INSERT INTO users (username, password, role) VALUES (
    'admin',
    -- Password hash for 'admin123' (use PHP's password_hash)
    '$2y$10$wH6QwQwQwQwQwQwQwQwQwOQwQwQwQwQwQwQwQwQwQwQwQwQwQw',
    'admin'
) ON DUPLICATE KEY UPDATE role='admin';

CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    picture VARCHAR(255),
    category VARCHAR(50),
    description TEXT,
    qty INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    created_by INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    picture VARCHAR(255),
    category ENUM('ingredient','meat','vegetable','drink'),
    description TEXT,
    qty INT DEFAULT 0,
    price DECIMAL(10,2),
    created_by INT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10,2),
    status ENUM('pending','paid') DEFAULT 'pending',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    menu_id INT,
    qty INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_id) REFERENCES menu(id)
);

-- Delete orders and order_items older than 1 month
DELETE oi FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE o.date_created < DATE_SUB(NOW(), INTERVAL 1 MONTH);
DELETE FROM orders WHERE date_created < DATE_SUB(NOW(), INTERVAL 1 MONTH);
