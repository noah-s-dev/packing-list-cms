<?php
/**
 * User Login Page
 */

require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $result = loginUser($username, $password);
        if ($result['success']) {
            // Redirect to dashboard or intended page
            $redirectTo = $_GET['redirect'] ?? 'dashboard.php';
            header("Location: $redirectTo");
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Packing List CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="back-home">
            <a href="index.php" class="back-link">← Back to Home</a>
        </div>
        <div class="auth-form">
            <h1>Welcome Back</h1>
            <p>Login to access your packing lists</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="Enter your username or email">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
            
        </div>
        
        <div class="text-center my-2">
            <div>
                <span>© 2025 .  </span>
                <span class="text- ">Developed by </span>
                <a href="https://rivertheme.com" class="fw-bold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
            </div>
        </div>
    </div>
</body>
</html>

