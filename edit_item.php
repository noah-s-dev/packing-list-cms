<?php
/**
 * Edit Packing List Item
 */

require_once 'includes/auth.php';
require_once 'includes/packing_lists.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

$itemId = (int)($_GET['id'] ?? 0);
if (!$itemId) {
    header('Location: dashboard.php');
    exit();
}

// Get item details
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("
        SELECT i.*, pl.id as list_id, pl.title as list_title
        FROM items i
        INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
        WHERE i.id = ? AND pl.user_id = ?
    ");
    $stmt->execute([$itemId, $userId]);
    $item = $stmt->fetch();
    
    if (!$item) {
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit();
}

$categories = getAllCategories();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 1);
        $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
        $notes = sanitizeInput($_POST['notes'] ?? '');
        
        $result = updatePackingListItem($itemId, $userId, $name, $quantity, $categoryId, $notes);
        if ($result['success']) {
            $success = $result['message'];
            // Refresh the item data
            $stmt = $pdo->prepare("
                SELECT i.*, pl.id as list_id, pl.title as list_title
                FROM items i
                INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
                WHERE i.id = ? AND pl.user_id = ?
            ");
            $stmt->execute([$itemId, $userId]);
            $item = $stmt->fetch();
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
    <title>Edit Item - Packing List CMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>Packing List CMS</h1>
            </div>
            <div class="nav-menu">
                <a href="view_list.php?id=<?php echo $item['list_id']; ?>" class="btn btn-secondary">Back to List</a>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Edit Item</h2>
            <p>Editing item in: <strong><?php echo htmlspecialchars($item['list_title']); ?></strong></p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="edit_item.php?id=<?php echo $itemId; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="name">Item Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? $item['name']); ?>"
                           maxlength="100" placeholder="e.g., Sunscreen">
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" 
                           value="<?php echo (int)($_POST['quantity'] ?? $item['quantity']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (($_POST['category_id'] ?? $item['category_id']) == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Optional notes about this item"><?php echo htmlspecialchars($_POST['notes'] ?? $item['notes']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Item</button>
                    <a href="view_list.php?id=<?php echo $item['list_id']; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

