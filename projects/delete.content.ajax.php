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



if (isset($_GET["restore"])) $deleted = 0;
else $deleted = 1;

/*// update to mysqli
$sql="CALL Forum_proc_Delete_Message($user_key, $content_key, $deleted)";
$result = mysql_query($sql) or die(mysql_error());
*/
if ($deleted == 1)
  echo "
    <div>
      <p class='text-danger'>Message Deleted</p>
      <p><a href='javascript:void(0)' onclick='delete_message($content_key + \"&restore\", $(this), true)'><label class='label label-warning'>Undo</label></a></p>
    </div>
  ";



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
