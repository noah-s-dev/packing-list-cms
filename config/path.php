<?php
/**
 * Path Configuration
 * 
 * This file handles base path configuration to ensure redirects work correctly
 * without including port numbers or absolute URLs.
 */

/**
 * Get the base path of the application
 * This ensures redirects work correctly regardless of server configuration
 * 
 * @return string Base path (e.g., '/packing-list-cms' or '')
 */
function getBasePath() {
    // Get the script directory relative to document root
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptName);
    
    // If script is in root, return empty string
    // Otherwise return the directory path
    if ($scriptDir === '/' || $scriptDir === '\\') {
        return '';
    }
    
    // Return the directory path (e.g., '/packing-list-cms')
    return rtrim($scriptDir, '/\\');
}

/**
 * Get a relative URL path
 * Ensures the path is relative and doesn't include port numbers
 * 
 * @param string $path Relative path (e.g., 'dashboard.php')
 * @return string Relative path with base path if needed
 */
function getRelativePath($path) {
    $basePath = getBasePath();
    
    // If path already starts with http:// or https://, return as is
    if (preg_match('/^https?:\/\//', $path)) {
        return $path;
    }
    
    // Ensure path starts with /
    if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
    }
    
    // Combine base path with relative path
    return $basePath . $path;
}

/**
 * Redirect to a relative path
 * This function ensures redirects use relative paths without port numbers
 * 
 * @param string $path Relative path to redirect to
 * @param bool $exit Whether to exit after redirect (default: true)
 */
function redirect($path, $exit = true) {
    // Remove any leading slashes to ensure relative path
    $path = ltrim($path, '/');
    
    // Use relative redirect (browser will handle it correctly)
    header('Location: ' . $path);
    
    if ($exit) {
        exit();
    }
}
?>

