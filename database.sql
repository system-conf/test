-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample product data
INSERT INTO products (name, category, price, stock, description, image_path) VALUES
('iPhone 13', 'elektronik', 24999.99, 50, 'Apple iPhone 13 128GB Akıllı Telefon', 'uploads/products/iphone13.jpg'),
('Nike Spor Ayakkabı', 'spor', 1299.99, 100, 'Nike Revolution 6 Erkek Koşu Ayakkabısı', 'uploads/products/nike_shoes.jpg'),
('Harry Potter Set', 'kitap', 749.99, 25, '7 Kitaplık Harry Potter Seti', 'uploads/products/harry_potter.jpg'),
('Samsung 4K TV', 'elektronik', 12999.99, 15, 'Samsung 55" 4K Ultra HD Smart TV', 'uploads/products/samsung_tv.jpg'),
('Levi\'s Kot Pantolon', 'giyim', 899.99, 75, 'Levi\'s 511 Slim Fit Kot Pantolon', 'uploads/products/levis_jeans.jpg');

-- Add indexes for better performance
ALTER TABLE products ADD INDEX idx_category (category);
ALTER TABLE products ADD INDEX idx_created_at (created_at);
