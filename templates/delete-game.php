<?php
  
// read value from id 
  $id = $_GET['id']; 
  // Connect to database
  include("db.php");
  // define sql query
  $sql = "DELETE FROM manga WHERE id =". $id;
   // Run SQL statement and report errors
  if(!$mysqli -> query($sql)) {
      echo("<h4>SQL error description: " . $mysqli -> error . "</h4>");
  }
  
  // Redirect to list
  header("location:../week1.php");
?>

