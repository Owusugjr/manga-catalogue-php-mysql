<?php
session_start();

// assignment/index.php
// Homepage showing top 5 manga by rating using Twig.

// Load Twig (same as game.php)
require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

// Connect to database
include 'db.php';

// Get top 5 manga ordered by google_rating (highest first)
$sql     = "SELECT * FROM manga ORDER BY google_rating DESC LIMIT 5";
$results = mysqli_query($mysqli, $sql);

// Count how many rows (just in case less than 5 exist)
$num_rows = mysqli_num_rows($results);

// Render the Twig template
echo $twig->render('index.html', [
    'num_rows'     => $num_rows,
    'top_manga'    => $results,
    'current_user' => $_SESSION['username'] ?? null
]);
