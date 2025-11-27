<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

$errors   = [];
$success  = false;
$username = '';

// Generate or reuse CAPTCHA
if (!isset($_SESSION['captcha_a'], $_SESSION['captcha_b'])) {
    $_SESSION['captcha_a'] = rand(1, 9);
    $_SESSION['captcha_b'] = rand(1, 9);
}

$captcha_a = $_SESSION['captcha_a'];
$captcha_b = $_SESSION['captcha_b'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST values
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $password2     = $_POST['password2'] ?? '';
    $captcha_input = trim($_POST['captcha'] ?? '');

    // INPUT FILTERING / VALIDATION
    if ($username === '') {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if ($password === '' || $password2 === '') {
        $errors[] = "Both password fields are required.";
    } elseif ($password !== $password2) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // CAPTCHA CHECK
    $expected_captcha = $captcha_a + $captcha_b;
    if ($captcha_input === '' || !ctype_digit($captcha_input)) {
        $errors[] = "Please enter a numeric answer for the CAPTCHA.";
    } elseif ((int)$captcha_input !== $expected_captcha) {
        $errors[] = "CAPTCHA answer is incorrect.";
    }

    if (empty($errors)) {
        // OWASP: Use prepared statements to prevent SQL injection
        // Check if username already exists
        $stmt_check = $mysqli->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        if (!$stmt_check) {
            $errors[] = "Database error. Please try again later.";
        } else {
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();

            if ($check_result && $check_result->num_rows > 0) {
                $errors[] = "This username is already taken.";
                $stmt_check->close();
            } else {
                $stmt_check->close();
                
                // PASSWORD HASHING (from the lecture)
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Use prepared statement for INSERT
                $stmt_insert = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                if (!$stmt_insert) {
                    $errors[] = "There was a problem saving your account.";
                } else {
                    $stmt_insert->bind_param("ss", $username, $password_hash);
                    
                    if ($stmt_insert->execute()) {
                        $success  = true;
                        $username = '';

                        // Regenerate CAPTCHA for the next registration
                        $_SESSION['captcha_a'] = rand(1, 9);
                        $_SESSION['captcha_b'] = rand(1, 9);
                        $captcha_a = $_SESSION['captcha_a'];
                        $captcha_b = $_SESSION['captcha_b'];
                    } else {
                        $errors[] = "There was a problem saving your account.";
                    }
                    $stmt_insert->close();
                }
            }
        }
    }
}

// Render template
echo $twig->render('register.html', [
    'errors'      => $errors,
    'success'     => $success,
    'username'    => $username,
    'captcha_a'   => $captcha_a,
    'captcha_b'   => $captcha_b,
    'current_user'=> $_SESSION['username'] ?? null
]);
