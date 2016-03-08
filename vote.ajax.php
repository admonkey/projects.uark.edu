<?php

// we don't need this block since all database connections are handled in global header
/*
//save results to mysql
$user = "naliu";
$pass = "_YvYuoX09ADpAyIO​";
$db   = "projects_naliu";
//? Which host?
$host = "localhost";
*/

// require header for db connections
$include_mysqli = true;
require_once("_resources/header.inc.php");

// NEED TO SANITIZE USER INPUT!
$content_key = $_GET["content_key"];
$vote_value = $_GET["vote_value"];

// we don't need this since it is auto-generated on table
//$vote_creation_time = time();

if(!empty($_GET["user_key"]))
  $user_key = $_GET["user_key"];

// removed vote_creation_time
//$sql = "INSERT INTO Votes (user_key, content_key, vote_value, vote_creation_time) VALUES ($user_key, $content_key, $vote_value, $vote_creation_time)";
$sql = "INSERT INTO Votes (user_key, content_key, vote_value) VALUES ($user_key, $content_key, $vote_value)";


// we don't need this block since all database connections are handled in global header
/*
mysql_connect( $host, $user, $pass ) or die("Issue with connecting");
mysql_select_db( $db );
mysql_query($sql);
mysql_close();
*/

// this is the old deprecated mysql functions, do not use these as they are insecure
//$result = mysql_query($sql);

// use the mysqli functions: http://php.net/manual/en/book.mysqli.php
if( !empty($mysqli_connected) ){
  $result = $mysqli_connection->query($sql) or die($mysqli_connection->error);
}






// while debugging, go ahead and print out all the values,
// remember to delete/comment-out this block when finished.
$sql = "SELECT * FROM Votes";
if( !empty($mysqli_connected) ){
  $result = $mysqli_connection->query($sql) or die($mysqli_connection->error);
}
// print table header
echo "<table border=1><thead><tr>";
$cols = $result->fetch_fields();
foreach ($cols as $col){
  echo "<th>".$col->name."</th>";
}
// close table header, open body
echo "</tr></thead><tbody>";
// print table data
while($row = $result->fetch_array(MYSQLI_NUM)){
  echo "<tr>";
  foreach($row as $val){
    echo "<td>$val</td>";
  }
  echo "</tr>";
}
// close table
echo "</tbody></table>";




// require footer to close db connections
require_once("_resources/footer.inc.php");
?>