<h1>Search results</h1>
<?php

  // Connect to database and run SQL query
  include("db.php");

  // Read value from form
  $keywords = $_POST['keywords'];  

  // Run SQL query
  $sql = "SELECT * FROM manga 
          WHERE title LIKE '%{$keywords}%' 
          ORDER BY release_year";
          
  $results = mysqli_query($mysqli, $sql);
?>

<table>
  <?php while($a_row = mysqli_fetch_assoc($results)):?>
    <tr>
      <td><a href="game-details.php?id=<?=$a_row['id']?>"><?=$a_row['title']?></a></td>
      <td><?=$a_row['google_rating']?></td>
    </tr>
  <?php endwhile;?>
</table>
	Notes on the 
