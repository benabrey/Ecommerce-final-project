-- Cart table schema for storing shopping cart items

CREATE TABLE IF NOT EXISTS cart_items (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          user_id INT NOT NULL,
                                          product_id INT NOT NULL,
                                          quantity INT NOT NULL DEFAULT 1,
                                          added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                                          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                                          FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

                                          UNIQUE KEY unique_user_product (user_id, product_id),

                                          INDEX idx_user_id (user_id),
                                          INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- TODO: Consider adding session_id for guest cart support (users not logged in)