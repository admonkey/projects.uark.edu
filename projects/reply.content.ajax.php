<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if (valid_positive_integer(@$_POST["parent_content_key"]))
  $parent_content_key = "$_POST[parent_content_key]";
else
  $parent_content_key = NULL;

if (!empty($_SESSION["user_key"])) $user_key = $_SESSION["user_key"];

if (!empty($_POST["content_value"])) $content_value = htmlentities($_POST["content_value"]);

if (empty($_POST["content_title"])) $content_title = NULL;
  else $content_title = htmlentities($_POST["content_title"]);

if(
  !empty($user_key) &&
  !empty($content_value) &&
  !empty($mysqli_connected)
){
  $stmt = $mysqli_connection->prepare("CALL create_content(?,?,?,?)") or die($mysqli_connection->error);
  $stmt->bind_param('iiss', $user_key, $parent_content_key, $content_title, $content_value);
  $stmt->execute();
  $stmt->store_result();
  // get variables from result.
  $stmt->bind_result($new_content_key);
  $stmt->fetch();
  $stmt->close();
  echo "<script>window.location = '?content_key=$new_content_key'</script>";
} else echo "incomplete params";

require_once("_resources/footer.inc.php");
?>
