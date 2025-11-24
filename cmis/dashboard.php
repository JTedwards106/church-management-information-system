<?php
/**
 * Dashboard Page
 * Purpose: Main page after login - shows system overview and statistics
 * Author: Justin Edwards
 */

// Start session
session_start();

// Check if user is logged in
require_once 'includes/check_access.php';

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get user information
$user_role = get_user_role();
$user_id = $_SESSION['user_id'];
$mem_id = $_SESSION['mem_id'];

// Query to get total members
$total_members_query = "SELECT COUNT(*) as total FROM members WHERE status != 'inactive'";
$total_members_result = $conn->query($total_members_query);
$total_members = $total_members_result->fetch_assoc()['total'] ?? 0;

// Query to get total ministries
$total_ministries_query = "SELECT COUNT(*) as total FROM ministries";
$total_ministries_result = $conn->query($total_ministries_query);
$total_ministries = $total_ministries_result->fetch_assoc()['total'] ?? 0;

// Query to get today's attendance count (you'll need an attendance table)
$today = date('Y-m-d');
$attendance_query = "SELECT SUM(count) as total FROM attendance WHERE date = '$today'";
$attendance_result = $conn->query($attendance_query);
$today_attendance = $attendance_result->fetch_assoc()['total'] ?? 0;

// Query to get upcoming birthdays (this week)
$week_start = date('Y-m-d');
$week_end = date('Y-m-d', strtotime('+7 days'));
$birthday_query = "SELECT COUNT(*) as total FROM members 
                   WHERE DATE_FORMAT(dob, '%m-%d') BETWEEN DATE_FORMAT('$week_start', '%m-%d') 
                   AND DATE_FORMAT('$week_end', '%m-%d')";
$birthday_result = $conn->query($birthday_query);
$upcoming_birthdays = $birthday_result->fetch_assoc()['total'] ?? 0;

// Query to get recent events (next 7 days)
$events_query = "SELECT COUNT(*) as total FROM events WHERE date BETWEEN '$week_start' AND '$week_end'";
$events_result = $conn->query($events_query);
$upcoming_events = $events_result->fetch_assoc()['total'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/dashboard.css">
</head>
<body>
    
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    
                    <!-- Show different menu items based on role -->
                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_users.php">Manage Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register_user.php">Add User</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($mem_id); ?> (<?php echo ucfirst($user_role); ?>)</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Dashboard Content -->
    <div class="container mt-4">
        
        <h2 class="mb-4">Dashboard Overview</h2>
        
        <!-- Statistics Cards Row -->
        <div class="row">
            
            <!-- Total Members Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Members</h6>
                                <h2><?php echo $total_members; ?></h2>
                            </div>
                            <div>
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Ministries Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Active Ministries</h6>
                                <h2><?php echo $total_ministries; ?></h2>
                            </div>
                            <div>
                                <i class="fas fa-church fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Today's Attendance Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Today's Attendance</h6>
                                <h2><?php echo $today_attendance; ?></h2>
                            </div>
                            <div>
                                <i class="fas fa-clipboard-check fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upcoming Events Card -->
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Upcoming Events</h6>
                                <h2><?php echo $upcoming_events; ?></h2>
                            </div>
                            <div>
                                <i class="fas fa-calendar-alt fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Quick Alerts Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5><i class="fas fa-bell"></i> Quick Alerts</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Birthday Alert -->
                        <?php if ($upcoming_birthdays > 0): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-birthday-cake"></i>
                                <strong><?php echo $upcoming_birthdays; ?> birthday(s)</strong> this week!
                            </div>
                        <?php endif; ?>
                        
                        <!-- Low Attendance Alert (Example) -->
                        <?php if ($today_attendance < 50): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Low attendance today: only <?php echo $today_attendance; ?> members present.
                            </div>
                        <?php endif; ?>
                        
                        <!-- No Alerts -->
                        <?php if ($upcoming_birthdays == 0 && $today_attendance >= 50): ?>
                            <p class="text-muted">No alerts at this time.</p>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity Section (Optional) -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Recent Members</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Query to get 5 most recent members
                        $recent_members_query = "SELECT first_name, last_name, date_joined 
                                                FROM members 
                                                ORDER BY date_joined DESC 
                                                LIMIT 5";
                        $recent_members_result = $conn->query($recent_members_query);
                        
                        if ($recent_members_result->num_rows > 0): ?>
                            <ul class="list-group">
                                <?php while ($member = $recent_members_result->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                        <small class="text-muted float-end"><?php echo format_date($member['date_joined']); ?></small>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No recent members found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-star"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($user_role === 'admin' || $user_role === 'clerk'): ?>
                                <a href="add_member.php" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Add New Member
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($user_role === 'admin' || $user_role === 'ministry_leader'): ?>
                                <a href="record_attendance.php" class="btn btn-success">
                                    <i class="fas fa-clipboard-check"></i> Record Attendance
                                </a>
                            <?php endif; ?>
                            
                            <a href="view_reports.php" class="btn btn-info">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>