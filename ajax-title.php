<?php
// ajax-title.php – returns JSON list of matching manga titles

session_start();
include 'db.php';

header('Content-Type: application/json; charset=utf-8');

$q = $_GET['q'] ?? '';
$q = trim($q);

// If query too short, return empty list
if ($q === '' || strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Basic input filtering
$safe = mysqli_real_escape_string($mysqli, $q);

// Search titles that contain the text
$sql = "
    SELECT DISTINCT title
    FROM manga
    WHERE title LIKE '%{$safe}%'
    ORDER BY title
    LIMIT 10
";

$result = mysqli_query($mysqli, $sql);

$titles = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $titles[] = $row['title'];
    }
}

// Output JSON for JavaScript / fetch()
echo json_encode($titles);
