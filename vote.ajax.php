<?php

// require header for db connections
$include_mysqli = true;
require_once("_resources/header.inc.php");

// SANITIZE USER INPUT!
$content_key = $_GET["content_key"];
$vote_value = $_GET["vote_value"];

if(!empty($_GET["user_key"]))
  $user_key = $_GET["user_key"];

// Check if vote already exists.

$sql = "SELECT vote_value FROM Votes WHERE user_key = $user_key AND content_key = $content_key";

if( !empty($mysqli_connected) ){
  $result = $mysqli_connection->query($sql) or die($mysqli_connection->error);
}

// Update the vote if it already exists.
if(!empty($result)){
  $sql = "UPDATE Votes SET vote_value = $vote_value WHERE user_key = $user_key AND content_key = $content_key"; 
  if( !empty($mysqli_connected) ){
	$result = $mysqli_connection->query($sql) or die($mysqli_connection->error);
  }
}
// Insert a new record if the vote if it does not exist.
else {
	$sql = "INSERT INTO Votes (user_key, content_key, vote_value) VALUES ($user_key, $content_key, $vote_value)";
	if( !empty($mysqli_connected) ){
	$result = $mysqli_connection->query($sql) or die($mysqli_connection->error);
	}
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