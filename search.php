<?php
// assignment/search.php
// Multi-criteria search: title, genre, year (using Twig + MySQL).

require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig   = new \Twig\Environment($loader);

include 'db.php';

// Default values for form + results
$title      = '';
$genre      = '';
$year       = '';
$results    = null;
$num_rows   = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get values from the form (like the POST examples in the lectures)
    // 
    $title = $_POST['title'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $year  = $_POST['year'] ?? '';

    // Basic trimming
    $title = trim($title);
    $genre = trim($genre);
    $year  = trim($year);

    // Build SQL with optional filters (similar to the search example)
    // 
    $sql = "SELECT * FROM manga WHERE 1 = 1";

    if ($title !== '') {
        $safe_title = mysqli_real_escape_string($mysqli, $title);
        $sql .= " AND title LIKE '%{$safe_title}%'";
    }

    if ($genre !== '') {
        $safe_genre = mysqli_real_escape_string($mysqli, $genre);
        $sql .= " AND genre LIKE '%{$safe_genre}%'";
    }

    if ($year !== '') {
        // Only use year if it is numeric
        if (is_numeric($year)) {
            $safe_year = (int)$year;
            $sql .= " AND release_year = {$safe_year}";
        }
    }

    $sql .= " ORDER BY release_year";

    $results  = mysqli_query($mysqli, $sql);
    if ($results) {
        $num_rows = mysqli_num_rows($results);
    }
}

// Render the Twig template
echo $twig->render('search.html', [
    'title'        => $title,
    'genre'        => $genre,
    'year'         => $year,
    'results'      => $results,
    'num_rows'     => $num_rows,
    'current_user' => $_SESSION['username'] ?? null
]);

?>
