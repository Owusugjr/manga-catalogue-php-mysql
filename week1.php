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

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add A Manga</button>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Your Manga</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action = "templates/add-game.php" method="post">
          <div class="mb-3">
            <label for="title" class="col-form-label">Name of Manga:</label>
            <input type="text" class="form-control" id="title" - name = "title">
          </div>
          <div class="mb-3">
            <label for="description" class="col-form-label">Manga Description:</label>
            <textarea class="form-control" id="description" - name = "description"></textarea>
          </div>
          <div class="mb-3">
            <label for="release_year" class="col-form-label">Manga Release Year:</label>
            <input type="year" class="form-control" id="release_year" - name = "release_year">
          </div>
          <div class="mb-3">
            <label for="google_rating" class="col-form-label">Manga Google Rating:</label>
            <input type="number" class="form-control" id="google_rating" - name = "google_rating">
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             <button type="submit" class="btn btn-primary">Add Manga</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include ('templates/footer.php'); ?>



