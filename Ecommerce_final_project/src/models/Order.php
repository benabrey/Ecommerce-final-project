<?php
// Order model for database operations related to orders

class Order {

    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Find an order by ID
     * @param int $id Order ID
     * @return array|null Order data or null if not found
     */
    public function findById($id) {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get all orders for a specific user
     * @param int $userId User ID
     * @return array Array of orders
     */
    public function getByUserId($userId) {
        $sql = "SELECT * FROM orders WHERE user_id = :userId ORDER BY created_at DESC";
        $stmt= $this->db->prepare($sql);

        $stmt->execute([':userId' => $userId]);
        $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Get all orders (admin function)
     * @param int $limit Limit (optional)
     * @param int $offset Offset (optional)
     * @return array Array of all orders
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";

        if($limit !== null){
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt= $this->db->prepare($sql);

        if($limit !== null){
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get orders by status
     * @param string $status Order status (pending, processing, shipped, delivered, cancelled)
     * @return array Array of orders with specified status
     */
    public function getByStatus($status) {
        $sql = "SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC";
        $stmt=$this->db->prepare($sql);

        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new order
     * @param array $orderData Order data (user_id, total_amount, shipping info, etc.)
     * @return int|bool Order ID if successful, false otherwise
     */
    public function create($orderData) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address, shipping_city, shipping_postal_code, shipping_country) 
                VALUES (:user_id, :total_amount, :status, :shipping_address, :shipping_city, :shipping_postal_code, :shipping_country)";

            $stmt = $this->db->prepare($sql);

            $success = $stmt->execute([
                ':user_id' => $orderData['user_id'],
                ':total_amount' => $orderData['total_amount'],
                ':status' => $orderData['status'],
                ':shipping_address' => $orderData['shipping_address'],
                ':shipping_city' => $orderData['shipping_city'],
                ':shipping_postal_code' => $orderData['shipping_postal_code'],
                ':shipping_country' => $orderData['shipping_country']
            ]);

            if (!$success) {
                $this->db->rollBack();
                return false;
            }

            $orderId = $this->db->lastInsertId();
            $this->db->commit();

            return $orderId;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Add items to an order
     * @param int $orderId Order ID
     * @param array $items Array of items with product_id, quantity, price_at_purchase
     * @return bool True if successful, false otherwise
     */
    public function addItems($orderId, $items) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                VALUES (:order_id, :product_id, :quantity, :price_at_purchase)";

            $stmt = $this->db->prepare($sql);

            foreach ($items as $item) {
                $success = $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price_at_purchase' => $item['price_at_purchase']
                ]);

                if (!$success) {
                    $this->db->rollBack();
                    return false;
                }
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get all items in an order
     * @param int $orderId Order ID
     * @return array Array of order items with product details
     */
    public function getItems($orderId) {
         $sql = "SELECT oi.*, p.name, p.image_url
         FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id = :orderId";

         $stmt =$this->db->prepare($sql);
         $stmt->execute([':orderId'=>$orderId]);

         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order with items (complete order details)
     * @param int $orderId Order ID
     * @return array|null Order data with items or null if not found
     */
    public function getWithItems($orderId) {
        $order = $this->findById($orderId);

        if (!$order) {
            return null;
        }

        $items = $this->getItems($orderId);
        $order['items'] = $items;

        return $order;
    }

    /**
     * Update order status
     * @param int $id Order ID
     * @param string $status New status
     * @return bool True if successful, false otherwise
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db-> prepare($sql);

        return $stmt->execute([':status'=>$status, ':id'=>$id]);

    }

    /**
     * Update order information
     * @param int $id Order ID
     * @param array $data Data to update
     * @return bool True if successful, false otherwise
     */
    public function update($id, $data) {
        if (empty($data)) {
            return false;
        }

        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        $sql = "UPDATE orders SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Cancel an order
     * @param int $id Order ID
     * @return bool True if successful, false otherwise
     */
    public function cancel($id) {
        $success = $this->updateStatus($id, 'cancelled');

        if ($success) {
            $items = $this->getItems($id);
             require_once __DIR__ . '/Product.php';
             $productModel = new Product($this->db);
             foreach ($items as $item) {
                 $productModel->increaseStock($item['product_id'], $item['quantity']);
             }
        }
        return $success;
    }

    /**
     * Delete an order (admin only, use with caution)
     * @param int $id Order ID
     * @return bool True if successful, false otherwise
     */
    public function delete($id) {
        $sql ="DELETE FROM orders WHERE id= :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id'=>$id]);
    }

    /**
     * Get order count for a user
     * @param int $userId User ID
     * @return int Number of orders
     */
    public function getCountByUser($userId) {
        $sql= "SELECT COUNT(*) as count FROM orders WHERE user_id = :userId";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([':userId' =>$userId]);
        $result = $stmt->fetch();

        return $result['count'] ?? 0;
    }

    /**
     * Get total revenue (admin function)
     * @param string $startDate Start date (optional)
     * @param string $endDate End date (optional)
     * @return float Total revenue
     */
    public function getTotalRevenue($startDate = null, $endDate = null) {
        $sql="SELECT SUM(total_amount) as revenue FROM orders WHERE status != 'cancelled'";
        if ($startDate !== null && $endDate !== null) {
            $sql .= " AND created_at BETWEEN :startDate AND :endDate";
        }

        $stmt = $this->db->prepare($sql);

        if ($startDate !== null && $endDate !== null) {
            $stmt->bindValue(':startDate', $startDate);
            $stmt->bindValue(':endDate', $endDate);
        }

        $stmt->execute();
        $result = $stmt->fetch();

        return $result['revenue'] ?? 0;
    }

    /**
     * Get recent orders
     * @param int $limit Number of recent orders
     * @return array Array of recent orders
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get pending orders (for admin dashboard)
     * @return array Array of pending orders
     */
    public function getPending() {
        return $this->getByStatus('pending');
    }
}
