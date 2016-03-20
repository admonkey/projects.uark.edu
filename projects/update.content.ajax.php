<div>
<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if (valid_positive_integer(@$_GET["content_key"])) {
  $content_key = $_GET["content_key"];
  if (!empty($_SESSION["user_key"])){
    $user_key = $_SESSION["user_key"];
      if( !empty($mysqli_connected) ){
      // BEGIN validation wrapper

if (!empty($_GET["content_title"]))
  $content_title = $_GET["content_title"];
else
  $content_title = NULL;

if (!empty($_GET["content_value"]))
  $content_value = $_GET["content_value"];
else
  $content_value = NULL;

$stmt = $mysqli_connection->prepare("CALL update_content(?, ?, ?, ?, NULL)") or die($mysqli_connection->error);
$stmt->bind_param('iiss', $user_key, $content_key, $content_title, $content_value);
if(!$stmt->execute())
  echo $stmt->error;
else {
  $stmt->store_result();
  // get variables from result.
  $stmt->bind_result($response);
  $stmt->fetch();

  echo "$response";
}
$stmt->close();

    // END validation wrapper
    } else {
      // help connecting to database
      echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
      include("$path_real_root/_resources/SQL/database.help.inc.html");
    }
  } else {
    echo "<p class='bg-danger text-danger'>ERROR: Not Logged In</p>";
  }
} else {
  echo "<p class='bg-danger text-danger'>ERROR: Invalid Content ID</p>";
}

require_once("_resources/footer.inc.php");
?>
</div>
