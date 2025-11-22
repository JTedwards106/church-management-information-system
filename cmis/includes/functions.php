<?php
/**
 * Helper Functions File
 * Purpose: Reusable functions used throughout the system
 * Author: Justin Edwards
 */

/**
 * Sanitize user input to prevent XSS attacks
 * @param string $data - Raw user input
 * @return string - Cleaned data
 */
function sanitize_input($data) {
    $data = trim($data);                    // Remove whitespace
    $data = stripslashes($data);            // Remove backslashes
    $data = htmlspecialchars($data);        // Convert special characters to HTML entities
    return $data;
}

/**
 * Redirect to another page
 * @param string $url - Page to redirect to
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 * @return bool - True if logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the logged-in user's role
 * @return string|null - Role name or null if not logged in
 */
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Check if user has specific role
 * @param string $required_role - Role to check against
 * @return bool - True if user has role, false otherwise
 */
function has_role($required_role) {
    $user_role = get_user_role();
    return $user_role === $required_role;
}

/**
 * Format date to readable format
 * @param string $date - Date string from database
 * @return string - Formatted date (e.g., "January 15, 2025")
 */
function format_date($date) {
    return date("F d, Y", strtotime($date));
}

/**
 * Display success message
 * @param string $message - Message to display
 */
function show_success($message) {
    echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

/**
 * Display error message
 * @param string $message - Error message to display
 */
function show_error($message) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}
?>