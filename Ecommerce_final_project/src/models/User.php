<?php
// User model for database operations related to users
class User
{
    private $db; // Database connection

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Find a user by ID
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Find a user by email
     * @param string $email User email
     * @return array|null User data or null if not found
     */
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email =?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Find a user by username
     * @param string $username Username
     * @return array|null User data or null if not found
     */
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT *FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Create a new user
     * @param array $data User data (username, email, password, first_name, last_name, etc.)
     * @return int|bool User ID if successful, false otherwise
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
        INSERT INTO users (username, email, password_hash, first_name, last_name, role, address)
        VALUES(?,?,?,?,?,?,?)
        ");
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $success = $stmt->execute([
            $data['username'],
            $data['email'],
            $passwordHash,
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['role'] ?? 'customer',
            $data['address']??''
        ]);
        if ($success) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update a user's information
     * @param int $id User ID
     * @param array $data Data to update
     * @return bool True if successful, false otherwise
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete a user
     * @param int $id User ID
     * @return bool True if successful, false otherwise
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id =?");
        $success = $stmt->execute([$id]);
        return $success;
    }

    /**
     * Get all users
     * @param int $limit Number of users to retrieve (optional)
     * @param int $offset Offset for pagination (optional)
     * @return array Array of user data
     */
    public function getAll($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM users";
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->db->prepare($sql);
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users ?: [];
    }

    /**
     * Verify user login credentials
     * @param string $email User's email
     * @param string $password Plain text password
     * @return array|bool User data if valid, false if invalid
     */
    public function verifyCredentials($email, $password)
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        unset($user['password_hash']);
        return $user;
    }

    /**
     * Check if email already exists
     * @param string $email Email to check
     * @param int $excludeId User ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function emailExists($email, $excludeId = null) {
        if($excludeId !== null){
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        }else{
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if username already exists
     * @param string $username Username to check
     * @param int $excludeId User ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function usernameExists($username, $excludeId = null) {
        if($excludeId !== null){
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeId]);
        }else{
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update user password
     * @param int $id User ID
     * @param string $newPassword New plain text password
     * @return bool True if successful, false otherwise
     */
    public function updatePassword($id, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$passwordHash, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get users by role
     * @param string $role Role to filter by ('admin', 'customer')
     * @return array Array of users with specified role
     */
    public function getByRole($role) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}