<?php
require_once("_resources/header.inc.php");

if(!empty($_POST["HTTP_REFERER"])){
  $_SESSION["HTTP_REFERER"] = $_POST["HTTP_REFERER"];
}

require_once("_resources/footer.inc.php");
?>
