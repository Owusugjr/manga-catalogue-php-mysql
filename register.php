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
        // Check if username already exists
        $safe_username = mysqli_real_escape_string($mysqli, $username);
        $check_sql     = "SELECT id FROM users WHERE username = '{$safe_username}'";
        $check_result  = mysqli_query($mysqli, $check_sql);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $errors[] = "This username is already taken.";
        } else {
            // PASSWORD HASHING (from the lecture)
            // 
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $insert_sql = "
                INSERT INTO users (username, password_hash)
                VALUES ('{$safe_username}', '{$password_hash}')
            ";

            if (mysqli_query($mysqli, $insert_sql)) {
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
