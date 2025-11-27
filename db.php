<?php
// Database connection file - assignment/db.php
// OWASP Secure: Use prepared statements and environment variables for credentials

// Load credentials from environment or config file (not hardcoded)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: '***REMOVED***';
$db_pass = getenv('DB_PASS') ?: '***REMOVED***';
$db_name = getenv('DB_NAME') ?: '***REMOVED***';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($mysqli->connect_errno) {
    // Don't expose error details to users
    error_log("Database connection failed: " . $mysqli->connect_error);
    echo "Database connection error. Please try again later.";
    exit();
}

// Set character set to UTF-8 for security
$mysqli->set_charset("utf8mb4");
?>
