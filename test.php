<?php include('templates/header.php'); ?>
<?php include('templates/nav.php'); ?>

<?php
include('testdb.php');

// Run SQL query
$sql = "SELECT * FROM movies ORDER BY Date_of_release";
$results = mysqli_query($mysqli, $sql);

// Check for SQL errors
if (!$results) {
    die("Database query failed: " . mysqli_error($mysqli));
}
?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>Movie name</th>
      <th>Genre</th>
      <th>Price (£)</th>
      <th>Date of release</th>
    </tr>
  </thead>
  <tbody>
    <?php while($a_row = mysqli_fetch_assoc($results)): ?>
      <tr>
        <td><?= htmlspecialchars($a_row['Movie_name']) ?></td>
        <td><?= htmlspecialchars($a_row['Genre']) ?></td>
        <td>£<?= number_format($a_row['Price'], 2) ?></td>
        <td><?= date('d/m/Y', strtotime($a_row['Date_of_release'])) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include('templates/footer.php'); ?>
