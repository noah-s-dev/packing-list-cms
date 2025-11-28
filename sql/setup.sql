-- Packing List CMS Database Setup
-- This file creates the database structure and inserts sample data

-- Create database (uncomment if needed)
CREATE DATABASE packing_list_cms;
USE packing_list_cms;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS packing_lists;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create packing_lists table
CREATE TABLE packing_lists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    trip_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create items table
CREATE TABLE items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    packing_list_id INT NOT NULL,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    quantity INT DEFAULT 1,
    is_packed BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (packing_list_id) REFERENCES packing_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_packing_lists_user_id ON packing_lists(user_id);
CREATE INDEX idx_items_packing_list_id ON items(packing_list_id);
CREATE INDEX idx_items_category_id ON items(category_id);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Clothes', 'Clothing items and accessories'),
('Electronics', 'Electronic devices and gadgets'),
('Toiletries', 'Personal hygiene and grooming items'),
('Documents', 'Important papers and identification'),
('Medications', 'Prescription and over-the-counter medicines'),
('Entertainment', 'Books, games, and leisure items'),
('Food & Snacks', 'Food items and snacks for travel'),
('Travel Gear', 'Luggage, bags, and travel accessories'),
('Sports & Outdoor', 'Sports equipment and outdoor gear'),
('Miscellaneous', 'Other items that don\'t fit specific categories');

-- Insert sample user (password is 'password123' hashed)
-- Note: In production, passwords should be hashed using PHP's password_hash() function
INSERT INTO users (username, email, password_hash) VALUES
('demo_user', 'demo@example.com', '$2y$10$2dK6rRu1NWNRCip6SAFrSeJ2SuRKat0kZWHu2oZczq8vy1XzQj4zK');

-- Insert sample packing list
INSERT INTO packing_lists (user_id, title, description, trip_date) VALUES
(1, 'Weekend Beach Trip', 'Packing list for a relaxing weekend at the beach', '2024-08-15');

-- Insert sample items
INSERT INTO items (packing_list_id, category_id, name, quantity, is_packed, notes) VALUES
(1, 1, 'Swimsuit', 2, FALSE, 'Bring both the blue and red ones'),
(1, 1, 'Beach shorts', 3, FALSE, NULL),
(1, 1, 'T-shirts', 4, TRUE, 'Pack lightweight cotton shirts'),
(1, 1, 'Flip-flops', 1, FALSE, NULL),
(1, 3, 'Sunscreen', 1, FALSE, 'SPF 50 or higher'),
(1, 3, 'Toothbrush', 1, TRUE, NULL),
(1, 3, 'Toothpaste', 1, TRUE, NULL),
(1, 3, 'Shampoo', 1, FALSE, 'Travel size bottle'),
(1, 2, 'Phone charger', 1, FALSE, NULL),
(1, 2, 'Camera', 1, FALSE, 'Don\'t forget extra batteries'),
(1, 6, 'Beach book', 1, FALSE, 'Something light and fun to read'),
(1, 7, 'Water bottle', 1, TRUE, 'Reusable bottle'),
(1, 7, 'Snacks', 1, FALSE, 'Trail mix and energy bars'),
(1, 8, 'Beach towel', 2, FALSE, 'Large and absorbent'),
(1, 8, 'Beach bag', 1, TRUE, 'Waterproof bag for wet items');

-- Display setup completion message
SELECT 'Database setup completed successfully!' AS message;
SELECT 'Demo data inserted for testing' AS message;
SELECT CONCAT('Total categories: ', COUNT(*)) AS message FROM categories;
SELECT CONCAT('Total users: ', COUNT(*)) AS message FROM users;
SELECT CONCAT('Total packing lists: ', COUNT(*)) AS message FROM packing_lists;
SELECT CONCAT('Total items: ', COUNT(*)) AS message FROM items;

