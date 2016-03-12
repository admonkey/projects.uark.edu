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
  
  echo "<div class='content_container well'>";

  if ($row["content_key"] === $row["project_key"])
    echo "<h1>$row[content_title]</h1>";
  elseif ($row["content_key"] === $row["thread_key"])
    echo "<h2>$row[content_title]</h2>";
  else
    echo "<h3>$row[content_title]</h3>";
  
  include("get.content.inc.php");
  
  echo "<div class='children_container' style='margin-top:10px'></div></div>";

} else {

  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>
<?php if($_SERVER["SCRIPT_FILENAME"] === (__FILE__)) require_once("_resources/footer.inc.php");?>