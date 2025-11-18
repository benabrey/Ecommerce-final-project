-- Product table schema for storing product information

CREATE TABLE IF NOT EXISTS products (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        name VARCHAR(200) NOT NULL,
                                        description TEXT,
                                        price DECIMAL(10, 2) NOT NULL,
                                        stock_quantity INT DEFAULT 0,
                                        category VARCHAR(100),
                                        image_url VARCHAR(500),
                                        brand VARCHAR(100),


                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                        INDEX idx_category (category),
                                        INDEX idx_price (price),
                                        INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TODO: Consider creating a categories table for better organization
-- TODO: Consider creating a product_images table for multiple images per product
-- TODO: Consider creating a product_reviews table for customer reviews