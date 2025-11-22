<?php
/**
 * Configuration File
 * Purpose: Store database credentials and site-wide settings
 * Author: Justin Edwards
 */

// Database configuration
//Define named constants to be used by another file
define('DB_HOST', 'localhost');        // Database server (usually localhost for XAMPP or WAMP)
define('DB_USER', 'root');              // Database username (default for XAMPP or WAMP)
define('DB_PASS', 'access@123!@#');                  // Database password (empty by default for XAMPP or WAMP)
define('DB_NAME', 'cmis_db');           // Your database name

// Site configuration
define('SITE_NAME', 'Church Management System');
define('SITE_URL', 'http://localhost/church-management-information-system/cmis/');

// Session configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
?>