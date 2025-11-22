<?php
/**
 * Logout File
 * Purpose: Destroy user session and redirect to login page
 * Author: Justin Edwards
 */

// Start session to access session variables
session_start();

// Include functions file
require_once 'includes/functions.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Set success message for login page
session_start(); // Start new session for the message
$_SESSION['success_message'] = 'You have been logged out successfully.';

// Redirect to login page
redirect('login.php');
?>