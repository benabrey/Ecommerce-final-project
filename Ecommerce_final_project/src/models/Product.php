<?php
// Product model for database operations related to products

class Product {

    private $db; // Database connection

    public function __construct($dbConnection) {
        //Store the database connection
         $this->db = $dbConnection;
    }

    /**
     * Find a product by ID
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public function findById($id) {
        $sql= "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        return $product ?:null;
    }

    /**
     * Get all products
     * @param int $limit Number of products to retrieve (optional)
     * @param int $offset Offset for pagination (optional)
     * @return array Array of product data
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM products ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();

    }

    /**
     * Get products by category
     * @param string $category Category name
     * @param int $limit Limit (optional)
     * @param int $offset Offset (optional)
     * @return array Array of products in category
     */
    public function getByCategory($category, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM products WHERE category = :category";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':category', $category, PDO::PARAM_STR);

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /**
     * Get all unique categories
     * @return array Array of category names
     */
    public function getAllCategories() {
        $sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt -> fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Search products by name or description
     * @param string $searchTerm Search term
     * @param int $limit Limit (optional)
     * @param int $offset Offset (optional)
     * @return array Array of matching products
     */
    public function search($searchTerm, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM products WHERE name LIKE :searchTerm OR description LIKE :searchTerm";
        if($limit !== null){
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->db->prepare($sql);

        $likeTerm = "%" . $searchTerm . "%";
        $stmt ->bindValue(':searchTerm', $likeTerm, PDO::PARAM_STR);

        if($limit !== null){
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new product
     * @param array $data Product data (name, description, price, stock_quantity, category, image_url)
     * @return int|bool Product ID if successful, false otherwise
     */
    public function create($data) {
        $sql = "INSERT INTO products 
            (name, description, price, stock_quantity, category, image_url)
            VALUES (:name, :description, :price, :stock_quantity, :category, :image_url)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':image_url', $data['image_url']);

        if($stmt->execute()){
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update a product
     * @param int $id Product ID
     * @param array $data Data to update
     * @return bool True if successful, false otherwise
     */
    public function update($id, $data) {
        if(empty($data)){
            return false;
        }
        $fields =[];
        foreach($data as $key => $value){
            $fields[] = "$key = :$key";
        }
        $setClause = implode(", ", $fields);

        $sql = "UPDATE products SET $setClause WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete a product
     * @param int $id Product ID
     * @return bool True if successful, false otherwise
     */
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([':id' => $id]);

        return $result;
    }

    /**
     * Update product stock quantity
     * @param int $id Product ID
     * @param int $quantity New stock quantity
     * @return bool True if successful, false otherwise
     */
    public function updateStock($id, $quantity) {
        $sql="UPDATE products SET stock_quantity = :quantity WHERE id= :id";
        $stmt =$this->db->prepare($sql);
        $result = $stmt->execute([':quantity' => $quantity, ':id' => $id]);
        return $result;
    }

    /**
     * Decrease product stock (when item is purchased)
     * @param int $id Product ID
     * @param int $amount Amount to decrease
     * @return bool True if successful, false otherwise
     */
    public function decreaseStock($id, $amount) {
        if($amount <= 0){
            return false;
        }
        $sql = "UPDATE products SET stock_quantity = stock_quantity - :amount WHERE id = :id AND stock_quantity >= :amount";
        $stmt =$this->db->prepare($sql);

        $stmt->execute([':id' => $id, ':amount' => $amount]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Increase product stock (when item is restocked)
     * @param int $id Product ID
     * @param int $amount Amount to increase
     * @return bool True if successful, false otherwise
     */
    public function increaseStock($id, $amount) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity + :amount WHERE id = :id";
        $stmt =$this->db->prepare($sql);

        return $stmt->execute([':id' => $id, ':amount' => $amount]);

    }

    /**
     * Check if product has sufficient stock
     * @param int $id Product ID
     * @param int $quantity Quantity needed
     * @return bool True if sufficient stock, false otherwise
     */
    public function hasStock($id, $quantity) {
        $sql = "SELECT stock_quantity FROM products WHERE id = :id";
        $stmt =$this->db->prepare($sql);

        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$row){
            return false;
        }
        return $row['stock_quantity'] >= $quantity;
    }

    /**
     * Get featured products
     * @param int $limit Number of featured products
     * @return array Array of featured products
     */
    public function getFeatured($limit = 6) {
        $sql ="SELECT * FROM products ORDER BY created_at DESC LIMIT :limit";
        $stmt =$this->db->prepare($sql);

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get products with low stock (for admin alerts)
     * @param int $threshold Stock threshold (default: 10)
     * @return array Array of products with low stock
     */
    public function getLowStock($threshold = 10) {
        $sql = "SELECT * FROM products WHERE stock_quantity <= :threshold AND stock_quantity > 0";
        $stmt =$this->db->prepare($sql);

        $stmt->execute([':threshold' => $threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get out of stock products
     * @return array Array of out of stock products
     */
    public function getOutOfStock() {
        $sql = "SELECT * FROM products WHERE stock_quantity = 0";
        $stmt =$this->db->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
