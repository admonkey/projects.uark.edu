<?php

// require header for db connections
$include_mysqli = true;
require_once("_resources/header.inc.php");

if(!empty($_SESSION["user_key"])){
  $user_key = $_SESSION["user_key"];
  if (valid_positive_integer(@$_GET["content_key"])){
    $content_key = $_GET["content_key"];
    if(!empty($_GET["vote_value"])){
      $vote_value = $_GET["vote_value"];
      if ($vote_value == (-2) || $vote_value == (-1) || $vote_value == 1) {
      // BEGIN validation wrapper


// call the sql precedue to do the voting
$sql = "CALL create_vote(?,?,?)";
if (!($stmt = $mysqli_connection->prepare($sql))) {
  echo "Prepare failed: (" . $mysqli_connection->errno . ") " . $mysqli_connection->error;
} else {
  $stmt->bind_param('iii', $user_key, $content_key, $vote_value);
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") ";
  } else {
    $stmt->store_result();
    $stmt->bind_result($response);
    $stmt->fetch();
    echo "$response";
  }
}


      // END validation wrapper
      } else {
        echo "ERROR: invalid vote value!";
      }
    } else {
      echo "ERROR: no vote!";
    }
  } else {
    echo "ERROR: invalid content key!";
  }
} else {
  echo "ERROR: not logged in!";
}


// require footer to close db connections
require_once("_resources/footer.inc.php");
?>