<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajax demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <h1>Game search</h1>
      <form class="row g-3">
        <div class="col-auto">
          <input type="text" class="form-control" id="searchBox" placeholder="Keywords">
        </div>
      </form>	
      <p id="results"></p>
    </div>
  </body>
</html>

<script>
document.getElementById("searchBox").addEventListener("keyup", doSearch);

function doSearch() {
  let keywords = document.getElementById("searchBox").value;

  fetch('https://mi-linux.wlv.ac.uk/~in9352/ajax/ajax.php?search=' + keywords)
  .then(response => response.json())
  .then(response => {

    document.getElementById("results").innerHTML = '';

    response.forEach(manga => {
      document.getElementById("results").innerHTML += manga.title + ' ';
    });
  });
}
</script>
