<?php
/**
 * Database Configuration
 * 
 * This file contains database connection settings for the Packing List CMS.
 * Modify these settings according to your MySQL server configuration.
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'packing_list_cms');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Create a PDO database connection
 * 
 * @return PDO Database connection object
 * @throws PDOException If connection fails
 */
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new PDOException("Database connection failed. Please check your configuration.");
    }
}

/**
 * Test database connection
 * 
 * @return bool True if connection successful, false otherwise
 */
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        $pdo->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>

