-- Order tables schema for storing completed orders

-- Main orders table
CREATE TABLE IF NOT EXISTS orders (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      user_id INT NOT NULL,
                                      total_amount DECIMAL(10, 2) NOT NULL,
                                      status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',

    -- Shipping information
                                      shipping_address TEXT NOT NULL,
                                      shipping_city VARCHAR(100) NOT NULL,
                                      shipping_postal_code VARCHAR(20) NOT NULL,
                                      shipping_country VARCHAR(100) NOT NULL,

                                      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

                                      INDEX idx_user_id (user_id),
                                      INDEX idx_status (status),
                                      INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table (products in each order)
CREATE TABLE IF NOT EXISTS order_items (
                                           id INT AUTO_INCREMENT PRIMARY KEY,
                                           order_id INT NOT NULL,
                                           product_id INT NOT NULL,
                                           quantity INT NOT NULL,
                                           price_at_purchase DECIMAL(10, 2) NOT NULL, -- Store price at time of purchase

                                           FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                                           FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

                                           INDEX idx_order_id (order_id),
                                           INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
