<?php
session_start();

// -----------------------------
// CAPTCHA SETUP
// -----------------------------
if (!isset($_SESSION['login_captcha_a'], $_SESSION['login_captcha_b'])) {
    $_SESSION['login_captcha_a'] = rand(1, 9);
    $_SESSION['login_captcha_b'] = rand(1, 9);
}

$captcha_a = $_SESSION['login_captcha_a'];
$captcha_b = $_SESSION['login_captcha_b'];

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

$errors   = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha_input = trim($_POST['captcha'] ?? '');

    // -----------------------------
    // BASIC VALIDATION
    // -----------------------------
    if ($username === '' || $password === '') {
        $errors[] = "Username and password are required.";
    }

    // -----------------------------
    // CAPTCHA VALIDATION
    // -----------------------------
    $expected_answer = $captcha_a + $captcha_b;

    if ($captcha_input === '' || !ctype_digit($captcha_input)) {
        $errors[] = "Please complete the CAPTCHA.";
    } elseif ((int)$captcha_input !== $expected_answer) {
        $errors[] = "Incorrect CAPTCHA answer.";
    }

    // -----------------------------
    // ONLY PROCESS LOGIN IF NO ERRORS
    // -----------------------------
    if (empty($errors)) {

        $safe_username = mysqli_real_escape_string($mysqli, $username);

        $sql = "SELECT id, username, password_hash
                FROM users
                WHERE username = '{$safe_username}'
                LIMIT 1";

        $result = mysqli_query($mysqli, $sql);

        if ($result && mysqli_num_rows($result) === 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password_hash'])) {

                // Reset CAPTCHA after successful login
                unset($_SESSION['login_captcha_a'], $_SESSION['login_captcha_b']);

                // store in session
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];

                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Invalid username or password.";
            }

        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}

// -----------------------------
// RENDER TEMPLATE
// -----------------------------
echo $twig->render('login.html', [
    'errors'       => $errors,
    'username'     => $username,
    'captcha_a'    => $captcha_a,
    'captcha_b'    => $captcha_b,
    'current_user' => $_SESSION['username'] ?? null
]);
?>
