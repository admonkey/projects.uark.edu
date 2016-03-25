<?php

// require header for db connections
$include_mysqli = true;
require_once("_resources/header.inc.php");

// get the values
$content_key = $_GET["content_key"];
$vote_value = $_GET["vote_value"];

if(!empty($_GET["user_key"]))
  $user_key = $_GET["user_key"];

// sanitize the values
if (valid_positive_integer($content_key) && valid_positive_integer($user_key)) {

	if ($vote_value == (-2) || $vote_value == (-1) || $vote_value == 1) {
		// call the sql precedue to do the voting
		$sql = "CALL create_vote(?,?,?)";
		// Call create_vote
		if (!($stmt = $mysqli_connection->prepare($sql))) {
			echo "Prepare failed: (" . $mysqli_connection->errno . ") " . $mysqli_connection->error;
		}
		else {
			$stmt->bind_param('iii', $user_key, $content_key, $vote_value);
			if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") ";
			}
		}
	}
}

// require footer to close db connections
require_once("_resources/footer.inc.php");
?>