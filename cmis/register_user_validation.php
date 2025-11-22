<?php
/**
 * Register User Validation Handler
 * Purpose: Process POST from register_user.php and insert new user
 */

// Start session
session_start();

// Load required helpers and configuration
require_once 'includes/check_access.php';
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register_user.php');
}

// Only admin can perform this action
if (get_user_role() !== 'admin') {
    $_SESSION['error_message'] = 'Access denied. Admin privileges required.';
    redirect('dashboard.php');
}

// Helper to get trimmed POST values
function post_trim($key) {
    return trim($_POST[$key] ?? '');
}

$mem_id = post_trim('mem_id');
$username = post_trim('username');
$password = post_trim('password');
$confirm_password = post_trim('confirm_password');
$role = post_trim('role');

$errors = array();

// Validate Member ID
if (empty($mem_id)) {
    $errors['mem_id'] = 'Member ID is required.';
} elseif (!preg_match('/^\d{3}$/', $mem_id)) {
    $errors['mem_id'] = 'Member ID must be 3 digits.';
} else {
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE mem_id = ?");
    $check_stmt->bind_param("s", $mem_id);
    $check_stmt->execute();
    $res = $check_stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $errors['mem_id'] = 'This Member ID already has a user account.';
    }
    $check_stmt->close();
}

// Validate Username
if (empty($username)) {
    $errors['username'] = 'Username is required.';
} elseif (strlen($username) < 3) {
    $errors['username'] = 'Username must be at least 3 characters.';
}

// Validate Password
if (empty($password)) {
    $errors['password'] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors['password'] = 'Password must be at least 8 characters.';
} elseif (!preg_match('/[0-9]/', $password)) {
    $errors['password'] = 'Password must contain at least one number.';
} elseif (!preg_match('/[\W_]/', $password)) {
    $errors['password'] = 'Password must contain at least one special character.';
}

// Confirm password
if ($password !== $confirm_password) {
    $errors['confirm_password'] = 'Passwords do not match.';
}

// Validate Role
$valid_roles = array('admin', 'pastor', 'ministry_leader', 'clerk');
if (empty($role)) {
    $errors['role'] = 'Please select a role.';
} elseif (!in_array($role, $valid_roles)) {
    $errors['role'] = 'Invalid role selected.';
}

// If errors, persist and redirect back
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_values'] = array(
        'mem_id' => $mem_id,
        'username' => $username,
        'role' => $role
    );
    redirect('register_user.php');
}

// Insert user
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (mem_id, username, password, role, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
$stmt->bind_param("ssss", $mem_id, $username, $hashed_password, $role);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'User created successfully!';
    $stmt->close();
    $conn->close();
    redirect('register_user.php');
} else {
    $errors['general'] = 'Error creating user. Please try again.';
    $_SESSION['errors'] = $errors;
    $_SESSION['old_values'] = array(
        'mem_id' => $mem_id,
        'username' => $username,
        'role' => $role
    );
    $stmt->close();
    $conn->close();
    redirect('register_user.php');
}

?>
