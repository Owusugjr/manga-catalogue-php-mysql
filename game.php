<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

// Run SQL query - same idea as the MySQL + PHP notes
$sql     = "SELECT * FROM manga ORDER BY release_year";
$results = mysqli_query($mysqli, $sql);

// How many rows were returned?
$num_rows = mysqli_num_rows($results);

// Load and render template (exactly like the workshop)
echo $twig->render('games.html', [
    'num_rows'     => $num_rows,
    'results'      => $results,
    'current_user' => $_SESSION['username'] ?? null
]);
