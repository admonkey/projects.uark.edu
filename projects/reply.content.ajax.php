<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if (valid_positive_integer(@$_POST["parent_content_key"]))
  $parent_content_key = "$_POST[parent_content_key]";
else
  echo "<p>ERROR: invalid parent_content_key</p>";

if (!empty($_SESSION["user_key"])) $user_key = $_SESSION["user_key"];

if (!empty($_POST["content_value"])) $content_value = $_POST["content_value"];

if(
  !empty($user_key) &&
  !empty($parent_content_key) &&
  !empty($content_value) &&
  !empty($mysqli_connected)
){
  $stmt = $mysqli_connection->prepare("CALL create_reply(?,?,?)") or die($mysqli_connection->error);
  $stmt->bind_param('iis', $user_key, $parent_content_key, $content_value);
  $stmt->execute();
  $stmt->close();
  echo "success";
} else echo "incomplete params";

require_once("_resources/footer.inc.php");
?>
