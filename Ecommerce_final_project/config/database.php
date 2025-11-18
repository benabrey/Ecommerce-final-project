<?php
// Database configuration and connection

define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_finaldb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection using PDO
 * @return PDO Database connection object
 * @throws PDOException if connection fails
 */
function getDatabaseConnection() {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

     $options = [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES => false,
         PDO::ATTR_PERSISTENT => false
     ];

     try {
         $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
         return $pdo;
     } catch (PDOException $e) {
         // Log error and die (in production, log to file instead of displaying)
         die("Database connection failed: " . $e->getMessage());
     }
}
