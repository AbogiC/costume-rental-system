-- Create database
CREATE DATABASE costume_rental_db;
USE costume_rental_db;

-- Create costumes table
CREATE TABLE costumes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    size VARCHAR(20),
    price_per_day DECIMAL(10, 2) NOT NULL,
    quantity_available INT NOT NULL DEFAULT 1,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create rentals table
CREATE TABLE rentals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    costume_id INT NOT NULL,
    renter_name VARCHAR(255) NOT NULL,
    renter_email VARCHAR(255) NOT NULL,
    renter_phone VARCHAR(20),
    rental_date DATE NOT NULL,
    return_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (costume_id) REFERENCES costumes(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO costumes (name, description, category, size, price_per_day, quantity_available, image_url) VALUES
('Medieval Knight Armor', 'Full knight armor set with helmet and sword', 'Historical', 'L', 45.00, 3, 'https://images.unsplash.com/photo-1583209814682-c8c4235c7c0f'),
('Wizard Robe', 'Purple wizard robe with stars and moon patterns', 'Fantasy', 'M', 25.00, 5, 'https://images.unsplash.com/photo-1518709268805-4e9042af2176'),
('Vampire Cape', 'Elegant black cape with red lining', 'Horror', 'One Size', 20.00, 8, 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3'),
('Superhero Costume', 'Classic superhero suit with cape and mask', 'Comic', 'S', 30.00, 4, 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3'),
('1920s Flapper Dress', 'Sparkling flapper dress with headband', 'Historical', 'M', 35.00, 2, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8');