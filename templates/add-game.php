<?php
  
  // Read values from the form
  $title = $_POST["title"];
  $description = $_POST["description"];
  $release_year = $_POST["release_year"];
  $google_rating = $_POST["google_rating"];
  
  // Connect to database
  include("db.php");
  
  // Build SQL statement
  $sql = "INSERT INTO manga(title, description, release_year, google_rating)
          VALUES('{$title}', '{$description}', '{$release_year}', '{$google_rating}')";
  
  // Run SQL statement and report errors
  if(!$mysqli -> query($sql)) {
      echo("<h4>SQL error description: " . $mysqli -> error . "</h4>");
  }
  
  // Redirect to list
  header("location:../week1.php");
?>
