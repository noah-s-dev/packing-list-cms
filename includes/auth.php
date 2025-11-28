<?php
/**
 * Authentication Functions
 * 
 * This file contains all authentication-related functions for the Packing List CMS.
 */

require_once 'config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Register a new user
 * 
 * @param string $username User's chosen username
 * @param string $email User's email address
 * @param string $password User's password (will be hashed)
 * @return array Result array with success status and message
 */
function registerUser($username, $email, $password) {
    try {
        $pdo = getDatabaseConnection();
        
        // Validate input
        $validation = validateRegistrationInput($username, $email, $password);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);
        
        return ['success' => true, 'message' => 'Registration successful'];
        
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Authenticate user login
 * 
 * @param string $username Username or email
 * @param string $password User's password
 * @return array Result array with success status and user data
 */
function loginUser($username, $password) {
    try {
        $pdo = getDatabaseConnection();
        
        // Validate input
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }
        
        // Find user by username or email
        $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed. Please try again.'];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user ID
 * 
 * @return int|null User ID if logged in, null otherwise
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user data
 * 
 * @return array|null User data if logged in, null otherwise
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email']
    ];
}

/**
 * Require user to be logged in (redirect if not)
 * 
 * @param string $redirectTo URL to redirect to if not logged in
 */
function requireLogin($redirectTo = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit();
    }
}

/**
 * Validate registration input
 * 
 * @param string $username Username to validate
 * @param string $email Email to validate
 * @param string $password Password to validate
 * @return array Validation result
 */
function validateRegistrationInput($username, $email, $password) {
    // Check if fields are empty
    if (empty($username) || empty($email) || empty($password)) {
        return ['valid' => false, 'message' => 'All fields are required'];
    }
    
    // Validate username
    if (strlen($username) < 3 || strlen($username) > 50) {
        return ['valid' => false, 'message' => 'Username must be between 3 and 50 characters'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['valid' => false, 'message' => 'Username can only contain letters, numbers, and underscores'];
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    
    if (strlen($email) > 100) {
        return ['valid' => false, 'message' => 'Email address is too long'];
    }
    
    // Validate password
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'Password must be at least 6 characters long'];
    }
    
    return ['valid' => true, 'message' => 'Validation passed'];
}

/**
 * Sanitize input to prevent XSS
 * 
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>

