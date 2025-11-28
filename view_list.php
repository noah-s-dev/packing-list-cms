<?php
/**
 * View Packing List and Manage Items
 */

require_once 'includes/auth.php';
require_once 'includes/packing_lists.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

$listId = (int)($_GET['id'] ?? 0);
if (!$listId) {
    header('Location: dashboard.php');
    exit();
}

// Get packing list
$packingList = getPackingList($listId, $userId);
if (!$packingList) {
    header('Location: dashboard.php');
    exit();
}

// Get items and categories
$items = getPackingListItems($listId, $userId);
$categories = getAllCategories();
$stats = getPackingListStats($listId, $userId);

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request. Please try again.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_item':
                $name = sanitizeInput($_POST['name'] ?? '');
                $quantity = (int)($_POST['quantity'] ?? 1);
                $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
                $notes = sanitizeInput($_POST['notes'] ?? '');
                
                $result = addPackingListItem($listId, $userId, $name, $quantity, $categoryId, $notes);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'toggle_packed':
                $itemId = (int)($_POST['item_id'] ?? 0);
                $result = toggleItemPackedStatus($itemId, $userId);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'delete_item':
                $itemId = (int)($_POST['item_id'] ?? 0);
                $result = deletePackingListItem($itemId, $userId);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
        }
        
        // Refresh data after any changes
        if ($messageType === 'success') {
            $items = getPackingListItems($listId, $userId);
            $stats = getPackingListStats($listId, $userId);
        }
    }
}

$csrfToken = generateCSRFToken();

// Group items by category
$itemsByCategory = [];
foreach ($items as $item) {
    $categoryName = $item['category_name'] ?: 'Uncategorized';
    $itemsByCategory[$categoryName][] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($packingList['title']); ?> - Packing List CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>Packing List CMS</h1>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="list-header">
            <div class="list-title">
                <h2><?php echo htmlspecialchars($packingList['title']); ?></h2>
                <?php if ($packingList['description']): ?>
                    <p class="list-description"><?php echo htmlspecialchars($packingList['description']); ?></p>
                <?php endif; ?>
                <?php if ($packingList['trip_date']): ?>
                    <p class="list-date">
                        <strong>Trip Date:</strong> <?php echo date('F j, Y', strtotime($packingList['trip_date'])); ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="list-actions">
                <a href="edit_list.php?id=<?php echo $listId; ?>" class="btn btn-secondary">Edit List</a>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stat">
                <span class="stat-number"><?php echo $stats['total_items']; ?></span>
                <span class="stat-label">Total Items</span>
            </div>
            <div class="stat">
                <span class="stat-number"><?php echo $stats['packed_items']; ?></span>
                <span class="stat-label">Packed</span>
            </div>
            <div class="stat">
                <span class="stat-number"><?php echo $stats['unpacked_items']; ?></span>
                <span class="stat-label">Remaining</span>
            </div>
            <div class="stat">
                <span class="stat-number"><?php echo $stats['completion_percentage']; ?>%</span>
                <span class="stat-label">Complete</span>
            </div>
        </div>

        <?php if ($stats['total_items'] > 0): ?>
            <div class="progress-bar large">
                <div class="progress-fill" style="width: <?php echo $stats['completion_percentage']; ?>%"></div>
                <span class="progress-text"><?php echo $stats['completion_percentage']; ?>% Complete</span>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Add Item Form -->
        <div class="add-item-form">
            <h3>Add New Item</h3>
            <form method="POST" action="view_list.php?id=<?php echo $listId; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="action" value="add_item">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Item Name *</label>
                        <input type="text" id="name" name="name" required maxlength="100" 
                               placeholder="e.g., Sunscreen">
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">Select category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <input type="text" id="notes" name="notes" placeholder="Optional notes">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Items List -->
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <h3>No items yet</h3>
                <p>Add your first item to start building your packing list!</p>
            </div>
        <?php else: ?>
            <div class="items-container">
                <?php foreach ($itemsByCategory as $categoryName => $categoryItems): ?>
                    <div class="category-section">
                        <h3 class="category-title"><?php echo htmlspecialchars($categoryName); ?></h3>
                        <div class="items-list">
                            <?php foreach ($categoryItems as $item): ?>
                                <div class="item-row <?php echo $item['is_packed'] ? 'packed' : ''; ?>">
                                    <div class="item-checkbox">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                            <input type="hidden" name="action" value="toggle_packed">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <input type="checkbox" <?php echo $item['is_packed'] ? 'checked' : ''; ?> 
                                                   onchange="this.form.submit()">
                                        </form>
                                    </div>
                                    
                                    <div class="item-details">
                                        <div class="item-name">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                            <?php if ($item['quantity'] > 1): ?>
                                                <span class="item-quantity">(<?php echo $item['quantity']; ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($item['notes']): ?>
                                            <div class="item-notes"><?php echo htmlspecialchars($item['notes']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                            <input type="hidden" name="action" value="delete_item">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-submit checkbox changes for better UX
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    // Add a small delay to show the visual feedback
                    setTimeout(() => {
                        this.form.submit();
                    }, 100);
                });
            });
        });
    </script>
</body>
</html>

