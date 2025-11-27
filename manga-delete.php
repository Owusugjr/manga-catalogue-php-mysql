<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

// PROTECT PAGE – must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit($id)) {
    echo $twig->render('message.html', [
        'title'        => 'Error',
        'message'      => 'Invalid manga ID.',
        'current_user' => $_SESSION['username'] ?? null
    ]);
    exit;
}

$manga_id = (int)$id;

// Check if manga exists
$sql = "SELECT * FROM manga WHERE id = {$manga_id} LIMIT 1";
$result = mysqli_query($mysqli, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo $twig->render('message.html', [
        'title'        => 'Not found',
        'message'      => 'Manga not found.',
        'current_user' => $_SESSION['username'] ?? null
    ]);
    exit;
}

$manga = mysqli_fetch_assoc($result);

// DELETE manga
$delete_sql = "DELETE FROM manga WHERE id = {$manga_id} LIMIT 1";

if (mysqli_query($mysqli, $delete_sql)) {
    echo $twig->render('message.html', [
        'title'        => 'Success',
        'message'      => 'Manga has been deleted.',
        'current_user' => $_SESSION['username'] ?? null
    ]);
} else {
    echo $twig->render('message.html', [
        'title'        => 'Error',
        'message'      => 'Could not delete manga.',
        'current_user' => $_SESSION['username'] ?? null
    ]);
}
?>
