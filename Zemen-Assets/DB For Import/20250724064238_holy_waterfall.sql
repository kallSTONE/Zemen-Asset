-- Property Rental and Sales Platform Database Setup
-- Run this file in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS property_platform;
USE property_platform;

-- Users table with role-based access
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'seller', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Property listings table
CREATE TABLE listings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category ENUM('house', 'apartment', 'commercial', 'land', 'machinery') DEFAULT 'house',
    price DECIMAL(12,2) NOT NULL,
    location VARCHAR(100) NOT NULL,
    listing_type ENUM('sale', 'rent') DEFAULT 'sale',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Property images table
CREATE TABLE listing_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    listing_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

-- Orders/inquiries table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    seller_id INT NOT NULL,
    listing_id INT NOT NULL,
    message TEXT,
    status ENUM('pending', 'responded', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

-- Favorites table
CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, listing_id)
);

-- Reviews table (for dummy review system)
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    listing_id INT NOT NULL,
    reviewer_name VARCHAR(100) NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@property.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample seller users
INSERT INTO users (name, email, password, phone, role) VALUES 
('John Seller', 'john@property.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123-456-7890', 'seller'),
('Sarah Agent', 'sarah@property.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '098-765-4321', 'seller');

-- Insert sample customer users
INSERT INTO users (name, email, password, phone, role) VALUES 
('Mike Customer', 'mike@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-123-4567', 'customer'),
('Lisa Buyer', 'lisa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-987-6543', 'customer');

-- Insert sample listings
INSERT INTO listings (user_id, title, description, category, price, location, listing_type, status) VALUES 
(2, 'Modern Downtown Apartment', 'Beautiful 2-bedroom apartment in the heart of downtown with city views and modern amenities.', 'apartment', 350000.00, 'Downtown District', 'sale', 'approved'),
(2, 'Family House with Garden', 'Spacious 4-bedroom house with large garden, perfect for families. Recently renovated.', 'house', 485000.00, 'Suburban Area', 'sale', 'approved'),
(3, 'Commercial Office Space', 'Prime commercial office space suitable for small to medium businesses. Great location.', 'commercial', 2500.00, 'Business District', 'rent', 'approved'),
(2, 'Luxury Villa', 'Stunning luxury villa with pool and panoramic views. High-end finishes throughout.', 'house', 750000.00, 'Hills District', 'sale', 'pending');

-- Insert sample reviews
INSERT INTO reviews (listing_id, reviewer_name, rating, comment) VALUES 
(1, 'David Thompson', 5, 'Amazing apartment with great location and amenities!'),
(1, 'Emma Wilson', 4, 'Beautiful space, would definitely recommend.'),
(2, 'Robert Chen', 5, 'Perfect family home with excellent condition.'),
(3, 'Business Owner', 4, 'Great office space for our growing company.');