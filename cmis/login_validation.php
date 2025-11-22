<?php
/**
 * Login Validation File
 * Purpose: Process login form submission and authenticate users
 * Author: Justin Edwards
 */

// Start session to store user data after successful login
session_start();

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

/**
 * Validate login form data
 * @param array $data - Form data from POST
 * @return array - Array of errors (empty if valid)
 */
function validateForm($data) {
    $errors = array();
    
    // Validate Member ID
    if (empty($data['mem_id'])) {
        $errors['mem_id'] = 'Member ID is required.';
    } elseif (!preg_match('/^\d{3}$/', $data['mem_id'])) {
        $errors['mem_id'] = 'Enter a valid 3-digit Member ID (e.g. 002).';
    }

    // Validate Password
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required.';
    }
    
    return $errors;
}

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form values and trim whitespace
    $values = array(
        'mem_id' => trim($_POST['mem_id'] ?? ''),
        'password' => trim($_POST['password'] ?? '')
    );
    
    // Validate form input
    $errors = validateForm($values);
    
    // If validation passed, check credentials against database
    if (empty($errors)) {
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, mem_id, password, role FROM users WHERE mem_id = ? AND status = 'active'");
        $stmt->bind_param("s", $values['mem_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password using password_verify (for hashed passwords)
            if (password_verify($values['password'], $user['password'])) {
                
                // Password is correct - create session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['mem_id'] = $user['mem_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                // Redirect based on user role
                switch ($user['role']) {
                    case 'admin':
                        redirect('dashboard.php'); // Admin sees full dashboard
                        break;
                    case 'pastor':
                        redirect('dashboard.php'); // Pastor dashboard (you can create pastor_dashboard.php later)
                        break;
                    case 'ministry_leader':
                        redirect('dashboard.php'); // Ministry leader dashboard
                        break;
                    case 'clerk':
                        redirect('dashboard.php'); // Clerk dashboard
                        break;
                    default:
                        redirect('dashboard.php'); // Default to main dashboard
                }
                
            } else {
                // Password is incorrect
                $errors['password'] = 'Incorrect password.';
            }
            
        } else {
            // User not found or account is inactive
            $errors['mem_id'] = 'Member ID not found or account is inactive.';
        }
        
        $stmt->close();
    }
    
    // If there are errors, store them in session and redirect back to login
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_values'] = $values;
        redirect('login.php');
    }
    
} else {
    // If accessed directly without POST, redirect to login
    redirect('login.php');
}

// Close database connection
$conn->close();
?>