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

if (isset($_GET["delete"])) $deleted = 1;
else $deleted = 0;

$stmt = $mysqli_connection->prepare("CALL update_content(?, ?, NULL, NULL, ?)") or die($mysqli_connection->error);
$stmt->bind_param('iii', $user_key, $content_key, $deleted);
if(!$stmt->execute())
  echo $stmt->error;
else {
  $stmt->store_result();
  // get variables from result.
  $stmt->bind_result($response);
  $stmt->fetch();

  if (($response === "deleted") && ($deleted === 1))
    echo "
      <div class='content_deleted_container well'>
	<p class='text-danger'>Content Deleted</p>
	<p><label class='label label-warning'><a href='javascript:void(0)' onclick='delete_content($content_key, $(this), true)'>Undo</a></label></p>
      </div>
    ";
  else echo "$response";
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
