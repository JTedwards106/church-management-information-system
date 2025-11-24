<?php
/**
 * Access Control File
 * Purpose: Verify user is logged in before accessing protected pages
 * Author: Justin Edwards
 * Usage: Include this file at the top of any protected page
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include functions file for helper functions
// Load configuration first so constants like SESSION_TIMEOUT are available
require_once 'config.php';

// Include functions file for helper functions
require_once 'functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    // User is not logged in - redirect to login page
    $_SESSION['error_message'] = 'Please log in to access this page.';
    redirect('login.php');
}

// Optional: Check for session timeout
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    // If user has been inactive for too long, log them out
    if ($inactive_time > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    // Start a fresh session to store the timeout message
    session_start();
    $_SESSION['error_message'] = 'Session expired. Please log in again.';
    redirect('login.php');
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>