<?php
/**
 * Packing List Management Functions
 * 
 * This file contains all functions for managing packing lists and items.
 */

require_once 'config/database.php';

/**
 * Get all packing lists for a user
 * 
 * @param int $userId User ID
 * @return array Array of packing lists
 */
function getUserPackingLists($userId) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT pl.*, 
                   COUNT(i.id) as total_items,
                   COUNT(CASE WHEN i.is_packed = 1 THEN 1 END) as packed_items
            FROM packing_lists pl
            LEFT JOIN items i ON pl.id = i.packing_list_id
            WHERE pl.user_id = ?
            GROUP BY pl.id
            ORDER BY pl.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching packing lists: " . $e->getMessage());
        return [];
    }
}

/**
 * Get a specific packing list by ID
 * 
 * @param int $listId Packing list ID
 * @param int $userId User ID (for security)
 * @return array|null Packing list data or null if not found
 */
function getPackingList($listId, $userId) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT pl.*, 
                   COUNT(i.id) as total_items,
                   COUNT(CASE WHEN i.is_packed = 1 THEN 1 END) as packed_items
            FROM packing_lists pl
            LEFT JOIN items i ON pl.id = i.packing_list_id
            WHERE pl.id = ? AND pl.user_id = ?
            GROUP BY pl.id
        ");
        $stmt->execute([$listId, $userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching packing list: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new packing list
 * 
 * @param int $userId User ID
 * @param string $title List title
 * @param string $description List description
 * @param string $tripDate Trip date (optional)
 * @return array Result array with success status and list ID
 */
function createPackingList($userId, $title, $description = '', $tripDate = null) {
    try {
        $pdo = getDatabaseConnection();
        
        // Validate input
        if (empty($title)) {
            return ['success' => false, 'message' => 'Title is required'];
        }
        
        if (strlen($title) > 100) {
            return ['success' => false, 'message' => 'Title is too long (max 100 characters)'];
        }
        
        // Validate trip date if provided
        if (!empty($tripDate) && !validateDate($tripDate)) {
            return ['success' => false, 'message' => 'Invalid trip date format'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO packing_lists (user_id, title, description, trip_date) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $title, $description, $tripDate ?: null]);
        
        $listId = $pdo->lastInsertId();
        
        return ['success' => true, 'message' => 'Packing list created successfully', 'list_id' => $listId];
        
    } catch (PDOException $e) {
        error_log("Error creating packing list: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to create packing list'];
    }
}

/**
 * Update a packing list
 * 
 * @param int $listId Packing list ID
 * @param int $userId User ID (for security)
 * @param string $title List title
 * @param string $description List description
 * @param string $tripDate Trip date (optional)
 * @return array Result array with success status
 */
function updatePackingList($listId, $userId, $title, $description = '', $tripDate = null) {
    try {
        $pdo = getDatabaseConnection();
        
        // Validate input
        if (empty($title)) {
            return ['success' => false, 'message' => 'Title is required'];
        }
        
        if (strlen($title) > 100) {
            return ['success' => false, 'message' => 'Title is too long (max 100 characters)'];
        }
        
        // Validate trip date if provided
        if (!empty($tripDate) && !validateDate($tripDate)) {
            return ['success' => false, 'message' => 'Invalid trip date format'];
        }
        
        $stmt = $pdo->prepare("
            UPDATE packing_lists 
            SET title = ?, description = ?, trip_date = ? 
            WHERE id = ? AND user_id = ?
        ");
        $result = $stmt->execute([$title, $description, $tripDate ?: null, $listId, $userId]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Packing list not found or no changes made'];
        }
        
        return ['success' => true, 'message' => 'Packing list updated successfully'];
        
    } catch (PDOException $e) {
        error_log("Error updating packing list: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to update packing list'];
    }
}

/**
 * Delete a packing list
 * 
 * @param int $listId Packing list ID
 * @param int $userId User ID (for security)
 * @return array Result array with success status
 */
function deletePackingList($listId, $userId) {
    try {
        $pdo = getDatabaseConnection();
        
        $stmt = $pdo->prepare("DELETE FROM packing_lists WHERE id = ? AND user_id = ?");
        $stmt->execute([$listId, $userId]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Packing list not found'];
        }
        
        return ['success' => true, 'message' => 'Packing list deleted successfully'];
        
    } catch (PDOException $e) {
        error_log("Error deleting packing list: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to delete packing list'];
    }
}

/**
 * Get all items for a packing list
 * 
 * @param int $listId Packing list ID
 * @param int $userId User ID (for security)
 * @return array Array of items
 */
function getPackingListItems($listId, $userId) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT i.*, c.name as category_name
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
            WHERE i.packing_list_id = ? AND pl.user_id = ?
            ORDER BY i.is_packed ASC, c.name ASC, i.name ASC
        ");
        $stmt->execute([$listId, $userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching packing list items: " . $e->getMessage());
        return [];
    }
}

/**
 * Add an item to a packing list
 * 
 * @param int $listId Packing list ID
 * @param int $userId User ID (for security)
 * @param string $name Item name
 * @param int $quantity Item quantity
 * @param int $categoryId Category ID (optional)
 * @param string $notes Item notes (optional)
 * @return array Result array with success status
 */
function addPackingListItem($listId, $userId, $name, $quantity = 1, $categoryId = null, $notes = '') {
    try {
        $pdo = getDatabaseConnection();
        
        // Verify that the packing list belongs to the user
        $stmt = $pdo->prepare("SELECT id FROM packing_lists WHERE id = ? AND user_id = ?");
        $stmt->execute([$listId, $userId]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Packing list not found'];
        }
        
        // Validate input
        if (empty($name)) {
            return ['success' => false, 'message' => 'Item name is required'];
        }
        
        if (strlen($name) > 100) {
            return ['success' => false, 'message' => 'Item name is too long (max 100 characters)'];
        }
        
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Quantity must be at least 1'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO items (packing_list_id, category_id, name, quantity, notes) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$listId, $categoryId ?: null, $name, $quantity, $notes]);
        
        return ['success' => true, 'message' => 'Item added successfully'];
        
    } catch (PDOException $e) {
        error_log("Error adding item: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to add item'];
    }
}

/**
 * Update an item
 * 
 * @param int $itemId Item ID
 * @param int $userId User ID (for security)
 * @param string $name Item name
 * @param int $quantity Item quantity
 * @param int $categoryId Category ID (optional)
 * @param string $notes Item notes (optional)
 * @return array Result array with success status
 */
function updatePackingListItem($itemId, $userId, $name, $quantity = 1, $categoryId = null, $notes = '') {
    try {
        $pdo = getDatabaseConnection();
        
        // Verify that the item belongs to the user
        $stmt = $pdo->prepare("
            SELECT i.id FROM items i
            INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
            WHERE i.id = ? AND pl.user_id = ?
        ");
        $stmt->execute([$itemId, $userId]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Item not found'];
        }
        
        // Validate input
        if (empty($name)) {
            return ['success' => false, 'message' => 'Item name is required'];
        }
        
        if (strlen($name) > 100) {
            return ['success' => false, 'message' => 'Item name is too long (max 100 characters)'];
        }
        
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Quantity must be at least 1'];
        }
        
        $stmt = $pdo->prepare("
            UPDATE items 
            SET name = ?, quantity = ?, category_id = ?, notes = ? 
            WHERE id = ?
        ");
        $stmt->execute([$name, $quantity, $categoryId ?: null, $notes, $itemId]);
        
        return ['success' => true, 'message' => 'Item updated successfully'];
        
    } catch (PDOException $e) {
        error_log("Error updating item: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to update item'];
    }
}

/**
 * Toggle item packed status
 * 
 * @param int $itemId Item ID
 * @param int $userId User ID (for security)
 * @return array Result array with success status
 */
function toggleItemPackedStatus($itemId, $userId) {
    try {
        $pdo = getDatabaseConnection();
        
        // Verify that the item belongs to the user and get current status
        $stmt = $pdo->prepare("
            SELECT i.is_packed FROM items i
            INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
            WHERE i.id = ? AND pl.user_id = ?
        ");
        $stmt->execute([$itemId, $userId]);
        $item = $stmt->fetch();
        
        if (!$item) {
            return ['success' => false, 'message' => 'Item not found'];
        }
        
        $newStatus = !$item['is_packed'];
        
        $stmt = $pdo->prepare("UPDATE items SET is_packed = ? WHERE id = ?");
        $stmt->execute([$newStatus, $itemId]);
        
        return ['success' => true, 'message' => 'Item status updated', 'is_packed' => $newStatus];
        
    } catch (PDOException $e) {
        error_log("Error toggling item status: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to update item status'];
    }
}

/**
 * Delete an item
 * 
 * @param int $itemId Item ID
 * @param int $userId User ID (for security)
 * @return array Result array with success status
 */
function deletePackingListItem($itemId, $userId) {
    try {
        $pdo = getDatabaseConnection();
        
        $stmt = $pdo->prepare("
            DELETE i FROM items i
            INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
            WHERE i.id = ? AND pl.user_id = ?
        ");
        $stmt->execute([$itemId, $userId]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Item not found'];
        }
        
        return ['success' => true, 'message' => 'Item deleted successfully'];
        
    } catch (PDOException $e) {
        error_log("Error deleting item: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to delete item'];
    }
}

/**
 * Get all categories
 * 
 * @return array Array of categories
 */
function getAllCategories() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Validate date format
 * 
 * @param string $date Date string
 * @param string $format Date format (default: Y-m-d)
 * @return bool True if valid, false otherwise
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Get packing list statistics
 * 
 * @param int $listId Packing list ID
 * @param int $userId User ID (for security)
 * @return array Statistics array
 */
function getPackingListStats($listId, $userId) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_items,
                COUNT(CASE WHEN is_packed = 1 THEN 1 END) as packed_items,
                COUNT(CASE WHEN is_packed = 0 THEN 1 END) as unpacked_items,
                COUNT(DISTINCT category_id) as categories_used
            FROM items i
            INNER JOIN packing_lists pl ON i.packing_list_id = pl.id
            WHERE i.packing_list_id = ? AND pl.user_id = ?
        ");
        $stmt->execute([$listId, $userId]);
        $stats = $stmt->fetch();
        
        $stats['completion_percentage'] = $stats['total_items'] > 0 
            ? round(($stats['packed_items'] / $stats['total_items']) * 100, 1)
            : 0;
            
        return $stats;
    } catch (PDOException $e) {
        error_log("Error fetching packing list stats: " . $e->getMessage());
        return [
            'total_items' => 0,
            'packed_items' => 0,
            'unpacked_items' => 0,
            'categories_used' => 0,
            'completion_percentage' => 0
        ];
    }
}
?>

