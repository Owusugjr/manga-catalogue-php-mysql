<?php
  // Connect to database and run SQL query
  include("db.php");

  // Grabs id value from URL
  $id = $_GET['id'];

  $sql = "SELECT * FROM manga WHERE id = {$id}";
  $rst = mysqli_query($mysqli, $sql);
  $a_row = mysqli_fetch_assoc($rst);
?>

<h1><?=$a_row['title']?></h1>
<p><?=$a_row['description']?></p>

<a href="../week1.php"><< Back to list</a>
