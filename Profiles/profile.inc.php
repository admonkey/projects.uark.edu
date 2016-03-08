<?php

$include_mysqli = true;
require_once((__DIR__)."/_resources/resources.inc.php");

// get profile from database
$query_profile = "SELECT * FROM `Users` WHERE user_key = $user_key";
$result_profile = $mysqli_connection->query($query_profile);
$array_profile = $result_profile->fetch_array(MYSQLI_ASSOC);

// if profile exists
if ( !empty($array_profile) ) {
  // if not private OR browser is user OR admin
  if ( empty($array_profile["private_profile"]) || ( (isset($_SESSION["user_key"]) && $_SESSION["user_key"] === $user_key) || isset($_SESSION["user_groups"]["ADMIN"]) ) ) {

    $page_title = "$array_profile[username]";

  } else {

    unset($array_profile);

  }
}
?>