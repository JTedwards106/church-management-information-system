<?php
/**
 * Register User Page
 * Purpose: Form for admins to create new system users
 * Author: Person A
 */

// Start session
session_start();

// Check if user is logged in and is an admin
require_once 'includes/check_access.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Only admin can access this page
if (get_user_role() !== 'admin') {
    $_SESSION['error_message'] = 'Access denied. Admin privileges required.';
    redirect('dashboard.php');
}

// Get errors and old values from session (for form validation feedback)
$errors = $_SESSION['errors'] ?? array();
$values = $_SESSION['old_values'] ?? array(
    'mem_id' => '',
    'username' => '',
    'role' => ''
);
$success = $_SESSION['success_message'] ?? '';

// Clear session data after retrieving it
unset($_SESSION['errors'], $_SESSION['old_values'], $_SESSION['success_message']);

// NOTE: Server-side POST handling has been moved to register_user_validation.php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><?php echo SITE_NAME; ?></a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-user-plus"></i> Register New User</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Success Message -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- General Error -->
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($errors['general']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Registration Form -->
                        <form method="POST" action="register_user_validation.php">
                            
                            <!-- Member ID -->
                            <div class="mb-3">
                                <label for="mem_id" class="form-label">Member ID <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['mem_id']) ? 'is-invalid' : ''; ?>" 
                                       id="mem_id" 
                                       name="mem_id" 
                                       placeholder="e.g., 001" 
                                       value="<?php echo htmlspecialchars($values['mem_id']); ?>"
                                       required>
                                <?php if (isset($errors['mem_id'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['mem_id']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" 
                                       name="username" 
                                       placeholder="Enter username" 
                                       value="<?php echo htmlspecialchars($values['username']); ?>"
                                       required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                <?php endif; ?>
                            </div>
														<!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Min. 8 characters, include number & symbol"
                                       required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                                <small class="form-text text-muted">
                                    Must be at least 8 characters with a number and special character.
                                </small>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="Re-enter password"
                                       required>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Role Selection -->
                            <div class="mb-3">
                                <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                                <select class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">-- Select Role --</option>
                                    <option value="admin" <?php echo ($values['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                    <option value="pastor" <?php echo ($values['role'] === 'pastor') ? 'selected' : ''; ?>>Pastor/Clergy</option>
                                    <option value="ministry_leader" <?php echo ($values['role'] === 'ministry_leader') ? 'selected' : ''; ?>>Ministry Leader</option>
                                    <option value="clerk" <?php echo ($values['role'] === 'clerk') ? 'selected' : ''; ?>>Clerk/Secretary</option>
                                </select>
                                <?php if (isset($errors['role'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['role']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create User
                                </button>
                                <a href="manage_users.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to User List
                                </a>
                            </div>
                            
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// No DB connection opened in this frontend-only file.
?>