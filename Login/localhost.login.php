<?php
$exclude_html = true;
$include_mysqli = true;
require_once((__DIR__)."/_resources/header.inc.php");

if ($_SERVER["SERVER_NAME"] === "localhost" && !empty($_GET["uid"]) && !empty($_GET["displayName"])) {

  $query = "CALL login_shib_user('$_GET[uid]','$_GET[displayName]');";
  if ( ($result = $mysqli_connection->query($query))===false ) {
    printf("ERROR: Could not process login.", $mysqli_connection->error, $query);
  }
  else {
    $row = $result->fetch_row();
    $_SESSION["user_key"] = $row[0];
    $_SESSION["username"] = $row[1];
    $_SESSION["email"] = $row[2];
    $result->close();
    header("Location: $_SESSION[HTTP_REFERER]");
  }

} else echo "
  <form>
    email:<br/>
    <input name='uid' type='text'/><br/>
    username:<br/>
    <input name='displayName' type='text'/><br/>
    <input type='submit'/>
  </form>
";

require_once((__DIR__)."/_resources/footer.inc.php");
?>
