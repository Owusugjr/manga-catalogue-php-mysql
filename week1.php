<?php include ('templates/header.php'); ?>
<?php include ('templates/nav.php'); ?>
<?php
    include ('templates/db.php');

        // Run SQL query
      $sql = "SELECT * FROM manga ORDER BY release_year";
      $results = mysqli_query($mysqli, $sql);
?>

<form action="templates/search-games.php" method="post">
  <input class="form-control" type="text" name="keywords" placeholder="Search">
  <input class="btn btn-primary" type="submit" value="Go!">
</form>

    <table class = "table table-striped">
      <?php while($a_row = mysqli_fetch_assoc($results)):?>
        <tr>
          <td><a href= "templates/game-details.php?id=<?=$a_row['id']?>"><?=$a_row['title']?></a></td>
          <td><?=$a_row['google_rating']?></a></td>
         </tr>
      <?php endwhile;?>
    </table>



<?php include ('templates/footer.php'); ?>



