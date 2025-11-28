<?php
/**
 * Create New Packing List
 */

require_once 'includes/auth.php';
require_once 'includes/packing_lists.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $tripDate = sanitizeInput($_POST['trip_date'] ?? '');
        
        $result = createPackingList($userId, $title, $description, $tripDate);
        if ($result['success']) {
            // Redirect to the new list
            header("Location: view_list.php?id=" . $result['list_id']);
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
    <title>Create New List - Packing List CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>Packing List CMS</h1>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Create New Packing List</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="create_list.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="title">List Title *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                           maxlength="100" placeholder="e.g., Weekend Beach Trip">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Optional description of your trip or event"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="trip_date">Trip Date</label>
                    <input type="date" id="trip_date" name="trip_date" 
                           value="<?php echo htmlspecialchars($_POST['trip_date'] ?? ''); ?>">
                    <small>Optional: When is your trip or event?</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create List</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

