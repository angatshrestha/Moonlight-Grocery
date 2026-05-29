-- Moonlight Grocery E-commerce Database Schema
-- Enterprise-grade Clean Structure

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    old_price DECIMAL(10, 2) NULL,
    stock INT DEFAULT 0,
    is_offer TINYINT(1) DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    driver_id INT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    delivery_fee DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'confirmed', 'dispatched', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    transaction_id VARCHAR(100) NULL,
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    delivery_address TEXT NOT NULL,
    phone VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Default Admin & User
-- Default password is 'password'
INSERT IGNORE INTO users (id, name, email, password, role) VALUES 
(1, 'Admin User', 'admin@moonlight.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Angat Shrestha', 'customer@moonlight.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert Premium Grocery Categories
INSERT IGNORE INTO categories (id, name) VALUES 
(1, 'Fruits & Vegetables'),
(2, 'Dairy & Eggs'),
(3, 'Bakery'),
(4, 'Beverages'),
(5, 'Pantry'),
(6, 'Meat & Seafood');

-- Insert 20+ Premium Retail Grocery Products
-- Balanced stock levels (some low stock < 5 to trigger low stock alerts)
INSERT IGNORE INTO products (id, category_id, name, description, price, old_price, stock, is_offer, image_url) VALUES
-- Fruits & Veggies
(1, 1, 'Organic Bananas', 'Fresh sweet organic Cavendish bananas from Queensland.', 2.99, NULL, 120, 0, 'https://images.unsplash.com/photo-1571501478200-84511598f804?auto=format&fit=crop&w=500&q=80'),
(2, 1, 'Fuji Apples 1kg', 'Crisp, sweet, and juice-packed premium local Fuji apples.', 4.50, 6.00, 85, 1, 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=500&q=80'),
(3, 1, 'Baby Spinach 120g', 'Washed and ready-to-eat tender organic baby spinach leaves.', 3.00, NULL, 4, 0, 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=500&q=80'),
(4, 1, 'Fresh Avocados (Pack of 3)', 'Creamy Haas avocados, perfect for spread and guacamole.', 5.50, 7.50, 40, 1, 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?auto=format&fit=crop&w=500&q=80'),

-- Dairy & Eggs
(5, 2, 'Almond Milk 1L', 'Smooth, unsweetened calcium-enriched premium organic almond milk.', 2.80, NULL, 90, 0, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=500&q=80'),
(6, 2, 'Whole Milk 2L', 'Full cream fresh dairy milk sourced from local farms.', 3.20, 4.00, 3, 1, 'https://images.unsplash.com/photo-1563636619-e9143da7973b?auto=format&fit=crop&w=500&q=80'),
(7, 2, 'Organic Free Range Eggs Dozen', 'Large 700g free-range organic eggs from pasture-fed hens.', 5.80, NULL, 65, 0, 'https://images.unsplash.com/photo-1518569656558-0f257c54d43f?auto=format&fit=crop&w=500&q=80'),
(8, 2, 'Greek Yogurt 1kg', 'Thick, creamy, authentic high-protein probiotic unsweetened Greek yogurt.', 6.20, 8.00, 50, 1, 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=500&q=80'),

-- Bakery
(9, 3, 'Sourdough Bread', 'Freshly baked, stoneground traditional sourdough bread loaf.', 5.00, NULL, 28, 0, 'https://images.unsplash.com/photo-1585478259715-876a6a81fa08?auto=format&fit=crop&w=500&q=80'),
(10, 3, 'Baguette (French Stick)', 'Classic golden, crusty French baguette stick baked daily.', 2.50, NULL, 15, 0, 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?auto=format&fit=crop&w=500&q=80'),
(11, 3, 'Anzac Biscuits 12pk', 'Golden traditional rolled oats and golden syrup sweet cookies.', 3.50, 4.20, 2, 1, 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?auto=format&fit=crop&w=500&q=80'),
(12, 3, 'Croissants (Pack of 4)', 'Buttery, flaky, golden melt-in-the-mouth French croissants.', 6.00, NULL, 18, 0, 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=500&q=80'),

-- Beverages
(13, 4, 'Apple Juice 2L', '100% natural, preservative-free pressed organic apple juice.', 5.00, 6.25, 30, 1, 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=500&q=80'),
(14, 4, 'Orange Juice Pulp Free 2L', '100% pure squeezed premium Australian oranges with zero pulp.', 5.50, NULL, 42, 0, 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?auto=format&fit=crop&w=500&q=80'),
(15, 4, 'Spring Water 24pk', 'Pure natural spring mineral water sourced from protected reserves.', 7.00, 9.50, 110, 1, 'https://images.unsplash.com/photo-1608885898957-a599fb18de37?auto=format&fit=crop&w=500&q=80'),

-- Pantry
(16, 5, 'Organic Penne Pasta 500g', 'Italian durum wheat semolina organic pasta, cooks to perfect al dente.', 2.20, NULL, 150, 0, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=500&q=80'),
(17, 5, 'Extra Virgin Olive Oil 750ml', 'First cold-pressed premium organic extra virgin cooking olive oil.', 12.00, 15.00, 32, 1, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=500&q=80'),
(18, 5, 'Peanut Butter Smooth 375g', 'Made with 100% local roasted peanuts and a pinch of salt.', 4.20, NULL, 78, 0, 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?auto=format&fit=crop&w=500&q=80'),

-- Meat & Seafood
(19, 6, 'Free Range Chicken Breast 1kg', 'Tender, juicy organic skinless chicken breast fillets.', 14.50, 18.00, 25, 1, 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?auto=format&fit=crop&w=500&q=80'),
(20, 6, 'Premium Beef Mince 500g', 'Lean premium grass-fed beef mince, perfect for bolognese or burgers.', 9.00, NULL, 1, 0, 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=500&q=80'),
(21, 6, 'Fresh Salmon Fillets 500g', 'Rich, omega-3 packed premium Atlantic salmon fillets with skin on.', 19.50, 24.00, 15, 1, 'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=500&q=80');
