<?php
/**
 * Database Connection File
 * Purpose: Establish connection to MySQL database
 * Author: Justin Edwards
 */

// Include configuration file
require_once 'config.php';

// Create connection using mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if connection was successful
if ($conn->connect_error) {
    // Stop execution and show error message
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8 to handle special characters properly
$conn->set_charset("utf8");

?>