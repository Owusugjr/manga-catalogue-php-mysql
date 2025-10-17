 <?php 
  $mysqli = new mysqli("localhost","***REMOVED***","***REMOVED***","***REMOVED***");

  if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
  }
?>