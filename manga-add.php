<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

// PROTECT THIS PAGE – only logged-in users
if (!isset($_SESSION['user_id'])) {
    // Not logged in: send them to login page
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;

// Default form values
$title        = '';
$genre        = '';
$year         = '';
$rating       = '';
$description  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and trim POST values
    $title       = trim($_POST['title'] ?? '');
    $genre       = trim($_POST['genre'] ?? '');
    $year        = trim($_POST['year'] ?? '');
    $rating      = trim($_POST['rating'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // BASIC VALIDATION / INPUT FILTERING
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
        // Escape strings to avoid SQL injection
        $safe_title       = mysqli_real_escape_string($mysqli, $title);
        $safe_genre       = mysqli_real_escape_string($mysqli, $genre);
        $safe_description = mysqli_real_escape_string($mysqli, $description);

        $safe_year   = (int)$year;
        $safe_rating = (float)$rating;

        $sql = "
            INSERT INTO manga (title, release_year, description, google_rating, genre)
            VALUES (
                '{$safe_title}',
                {$safe_year},
                '{$safe_description}',
                {$safe_rating},
                '{$safe_genre}'
            )
        ";

        if (mysqli_query($mysqli, $sql)) {
            $success = true;

            // Clear form fields after success
            $title       = '';
            $genre       = '';
            $year        = '';
            $rating      = '';
            $description = '';
        } else {
            $errors[] = "There was a problem saving the manga.";
        }
    }
}

// Render template
echo $twig->render('manga-add.html', [
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
