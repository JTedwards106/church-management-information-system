<?php
/**
 * Manage Users Page
 * Purpose: View, edit, and deactivate system users (Admin only)
 * Author: Justin Edwards
 */

// Start session
session_start();

// Check if user is logged in and is an admin
require_once 'includes/check_access.php';
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Only admin can access this page
if (get_user_role() !== 'admin') {
    $_SESSION['error_message'] = 'Access denied. Admin privileges required.';
    redirect('dashboard.php');
}

// Get success/error messages from session
$success = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Handle user status toggle (activate/deactivate)
if (isset($_GET['action']) && isset($_GET['user_id'])) {
  $user_id = intval($_GET['user_id']);
  $action = $_GET['action'];
    
    // Prevent admin from deactivating themselves
  if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error_message'] = 'You cannot deactivate your own account.';
    redirect('manage_users.php');
  }
    
  if ($action === 'deactivate') {
    // Deactivate user
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
  	  $_SESSION['success_message'] = 'User deactivated successfully.';
      } else {
        $_SESSION['error_message'] = 'Error deactivating user.';
      }
      $stmt->close();
    redirect('manage_users.php');
        
    } elseif ($action === 'activate') {
        // Activate user
    	  $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'User activated successfully.';
        } else {
            $_SESSION['error_message'] = 'Error activating user.';
        }
        $stmt->close();
        redirect('manage_users.php');
    }
}

// Query to get all users
$users_query = "SELECT user_id, mem_id, username, role, status, created_at 
                FROM users 
                ORDER BY created_at DESC";
$users_result = $conn->query($users_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS (for table search/sort functionality) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
                    <a class="nav-link active" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register_user.php">Add User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users-cog"></i> Manage Users</h2>
            <a href="register_user.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>
        
        <!-- Success Message -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h5>System Users</h5>
            </div>
            <div class="card-body">
                
                <?php if ($users_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Member ID</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['mem_id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php 
                                                // Display role with nice formatting
                                                echo ucwords(str_replace('_', ' ', $user['role'])); 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo format_date($user['created_at']); ?></td>
                                        <td>
                                                <a class="btn btn-sm me-2 text-white bg-primary"  href=".php">Edit User</a>
                                            <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                                
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <!-- Deactivate Button -->
                                                    <a href="manage_users.php?action=deactivate&user_id=<?php echo $user['user_id']; ?>" 
                                                       class="btn btn-sm btn-warning"
                                                       onclick="return confirm('Are you sure you want to deactivate this user?');">
                                                        <i class="fas fa-ban"></i> Deactivate
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Activate Button -->
                                                    <a href="manage_users.php?action=activate&user_id=<?php echo $user['user_id']; ?>" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> Activate
                                                    </a>
                                                <?php endif; ?>
                                                
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-user"></i> Current User</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No users found. 
                        <a href="register_user.php">Create your first user</a>.
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
        
        <!-- Legend -->
        <div class="mt-3">
            <small class="text-muted">
                <strong>Role Definitions:</strong><br>
                <span class="badge bg-primary">Administrator</span> - Full system access<br>
                <span class="badge bg-primary">Pastor</span> - View/edit membership, attendance, reports<br>
                <span class="badge bg-primary">Ministry Leader</span> - Update group attendance, view ministry records<br>
                <span class="badge bg-primary">Clerk</span> - Enter members/events, generate standard reports
            </small>
        </div>
        
    </div>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "pageLength": 10,           // Show 10 entries per page
                "order": [[4, "desc"]],     // Sort by created date (newest first)
                "language": {
                    "search": "Search users:",
                    "lengthMenu": "Show _MENU_ users per page"
                }
            });
        });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>