<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

// PROTECT THIS PAGE – only logged-in users
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get id from query string
$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit($id)) {
    // invalid id
    echo $twig->render('message.html', [
        'title'        => 'Error',
        'message'      => 'Invalid manga ID.',
        'current_user' => $_SESSION['username'] ?? null
    ]);
    exit;
}

$manga_id = (int)$id;

$errors  = [];
$success = false;

// First, load existing manga row
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

// Default form fields: pre-fill with current values
$title       = $manga['title'];
$genre       = $manga['genre'];
$year        = $manga['release_year'];
$rating      = $manga['google_rating'];
$description = $manga['description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // overwrite with submitted values
    $title       = trim($_POST['title'] ?? '');
    $genre       = trim($_POST['genre'] ?? '');
    $year        = trim($_POST['year'] ?? '');
    $rating      = trim($_POST['rating'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // validation
    if ($title === '') {
        $errors[] = "Title is required.";
    }

    if ($genre === '') {
        $errors[] = "Genre is required.";
    }

    if ($year === '') {
        $errors[] = "Year is required.";
    } elseif (!ctype_digit($year)) {
        $errors[] = "Year must be a whole number.";
    }

    if ($rating === '') {
        $errors[] = "Google rating is required.";
    } elseif (!is_numeric($rating)) {
        $errors[] = "Google rating must be a number (e.g. 4.8).";
    }

    if (empty($errors)) {
        $safe_title       = mysqli_real_escape_string($mysqli, $title);
        $safe_genre       = mysqli_real_escape_string($mysqli, $genre);
        $safe_description = mysqli_real_escape_string($mysqli, $description);

        $safe_year   = (int)$year;
        $safe_rating = (float)$rating;

        $update_sql = "
            UPDATE manga
            SET title = '{$safe_title}',
                release_year = {$safe_year},
                description = '{$safe_description}',
                google_rating = {$safe_rating},
                genre = '{$safe_genre}'
            WHERE id = {$manga_id}
            LIMIT 1
        ";

        if (mysqli_query($mysqli, $update_sql)) {
            $success = true;
        } else {
            $errors[] = "There was a problem updating the manga.";
        }
    }
}

echo $twig->render('manga-edit.html', [
    'id'           => $manga_id,
    'errors'       => $errors,
    'success'      => $success,
    'title'        => $title,
    'genre'        => $genre,
    'year'         => $year,
    'rating'       => $rating,
    'description'  => $description,
    'current_user' => $_SESSION['username'] ?? null
]);
?>
