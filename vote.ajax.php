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

// require footer to close db connections
require_once("_resources/footer.inc.php");
?>