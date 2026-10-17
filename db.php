<?php
// Database connection file - assignment/db.php
// Credentials are read from the environment. A local .env file is optional.

$envFromFile = [];
$envPath = __DIR__ . '/.env';
if (is_readable($envPath)) {
    $envLines = file($envPath, FILE_IGNORE_NEW_LINES);
    if ($envLines !== false) {
        foreach ($envLines as $envLine) {
            $envLine = trim($envLine);
            if ($envLine === '' || $envLine[0] === '#') {
                continue;
            }
            if (strncmp($envLine, 'export ', 7) === 0) {
                $envLine = trim(substr($envLine, 7));
            }
            $eqPos = strpos($envLine, '=');
            if ($eqPos === false) {
                continue;
            }
            $envName = trim(substr($envLine, 0, $eqPos));
            $envValue = substr($envLine, $eqPos + 1);
            if ($envName === '') {
                continue;
            }
            $envValue = trim($envValue);
            $envLen = strlen($envValue);
            if ($envLen >= 2) {
                $quote = $envValue[0];
                if (($quote === '"' || $quote === "'") && $envValue[$envLen - 1] === $quote) {
                    $envValue = substr($envValue, 1, $envLen - 2);
                }
            }
            $envFromFile[$envName] = $envValue;
        }
    }
}

$db_host = getenv('DB_HOST');
if ($db_host === false && array_key_exists('DB_HOST', $envFromFile)) {
    $db_host = $envFromFile['DB_HOST'];
}
$db_user = getenv('DB_USER');
if ($db_user === false && array_key_exists('DB_USER', $envFromFile)) {
    $db_user = $envFromFile['DB_USER'];
}
$db_pass = getenv('DB_PASS');
if ($db_pass === false && array_key_exists('DB_PASS', $envFromFile)) {
    $db_pass = $envFromFile['DB_PASS'];
}
$db_name = getenv('DB_NAME');
if ($db_name === false && array_key_exists('DB_NAME', $envFromFile)) {
    $db_name = $envFromFile['DB_NAME'];
}

if ($db_host === false || $db_user === false || $db_pass === false || $db_name === false) {
    error_log('Database configuration is incomplete. Set DB_HOST, DB_USER, DB_PASS, and DB_NAME.');
    echo "Database connection error. Please try again later.";
    exit();
}

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
