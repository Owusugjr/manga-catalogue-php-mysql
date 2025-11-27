<?php
// Database connection file - assignment/db.php
// Based on the MySQL + PHP examples from the lecture notes.

$mysqli = new mysqli(
    "localhost",      // Server
    "***REMOVED***",        // Username (same as your mi-linux login)
    "***REMOVED***",  // Your MySQL/password
    "***REMOVED***"       // Database name
);

// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
?>
