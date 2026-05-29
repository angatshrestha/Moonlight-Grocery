-- Moonlight Grocery E-commerce Database Schema

CREATE DATABASE IF NOT EXISTS moonlight_grocery;
USE moonlight_grocery;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending',
    delivery_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Default Admin
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@moonlight.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
-- Default password is 'password'

-- Insert Sample Categories
INSERT INTO categories (name) VALUES ('Fruits & Vegetables'), ('Dairy'), ('Bakery'), ('Beverages');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES
(1, 'Organic Bananas', 'Fresh organic bananas from local farms.', 2.99, 100, 'https://images.unsplash.com/photo-1571501478200-84511598f804?auto=format&fit=crop&w=500&q=60'),
(2, 'Whole Milk 1 Gallon', 'Farm fresh whole milk.', 4.50, 50, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=500&q=60'),
(3, 'Sourdough Bread', 'Freshly baked sourdough bread.', 5.00, 30, 'https://images.unsplash.com/photo-1585478259715-876a6a81fa08?auto=format&fit=crop&w=500&q=60');
