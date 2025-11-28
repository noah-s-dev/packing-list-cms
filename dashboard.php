<?php
/**
 * Dashboard - Main packing lists overview
 */

require_once 'includes/auth.php';
require_once 'includes/packing_lists.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

// Get user's packing lists
$packingLists = getUserPackingLists($userId);

$message = '';
$messageType = '';

// Handle list deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $listId = (int)($_POST['list_id'] ?? 0);
        $result = deletePackingList($listId, $userId);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
        
        // Refresh the list
        if ($result['success']) {
            $packingLists = getUserPackingLists($userId);
        }
    } else {
        $message = 'Invalid request. Please try again.';
        $messageType = 'error';
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Packing List CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>Packing List CMS</h1>
            </div>
            <div class="nav-menu">
                <span>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h2>Your Packing Lists</h2>
            <a href="create_list.php" class="btn btn-primary">Create New List</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (empty($packingLists)): ?>
            <div class="empty-state">
                <h3>No packing lists yet</h3>
                <p>Create your first packing list to get started organizing your trips!</p>
                <a href="create_list.php" class="btn btn-primary">Create Your First List</a>
            </div>
        <?php else: ?>
            <div class="lists-grid">
                <?php foreach ($packingLists as $list): ?>
                    <div class="list-card">
                        <div class="list-header">
                            <h3><a href="view_list.php?id=<?php echo $list['id']; ?>"><?php echo htmlspecialchars($list['title']); ?></a></h3>
                            <div class="list-actions">
                                <a href="edit_list.php?id=<?php echo $list['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this list?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                        
                        <?php if ($list['description']): ?>
                            <p class="list-description"><?php echo htmlspecialchars($list['description']); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($list['trip_date']): ?>
                            <p class="list-date">
                                <strong>Trip Date:</strong> <?php echo date('F j, Y', strtotime($list['trip_date'])); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="list-stats">
                            <div class="stat">
                                <span class="stat-number"><?php echo $list['total_items']; ?></span>
                                <span class="stat-label">Total Items</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number"><?php echo $list['packed_items']; ?></span>
                                <span class="stat-label">Packed</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number"><?php echo $list['total_items'] - $list['packed_items']; ?></span>
                                <span class="stat-label">Remaining</span>
                            </div>
                        </div>
                        
                        <?php if ($list['total_items'] > 0): ?>
                            <div class="progress-bar">
                                <?php $percentage = round(($list['packed_items'] / $list['total_items']) * 100); ?>
                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                <span class="progress-text"><?php echo $percentage; ?>% Complete</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="list-meta">
                            <small>Created: <?php echo date('M j, Y', strtotime($list['created_at'])); ?></small>
                            <?php if ($list['updated_at'] !== $list['created_at']): ?>
                                <small>Updated: <?php echo date('M j, Y', strtotime($list['updated_at'])); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <footer class="footer">
        <div class="container">
            <div class="text-center my-2">
                <div>
                    <span>© 2025 .  </span>
                    <span class="text- ">Developed by </span>
                    <a href="https://rivertheme.com" class="fw-bold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

