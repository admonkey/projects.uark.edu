<?php
$include_mysqli = true;
require_once("_resources/resources.inc.php");

if (valid_positive_integer(@$_GET["content_key"]))
  $content_key = "$_GET[content_key]";
else
  $content_key = "NULL";

if( !empty($mysqli_connected) ){
    
  $result = $mysqli_connection->query("CALL read_content($content_key)") or die($mysqli_connection->error);

  $row = $result->fetch_assoc();
  
  include("get.content.inc.php");

} else {

  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>
<?php if($_SERVER["SCRIPT_FILENAME"] === (__FILE__)) require_once("_resources/footer.inc.php");?>