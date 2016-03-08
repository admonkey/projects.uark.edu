<?php

$no_well_container = true;
// $page_title = "Home Page";
// $section_title = "Root Section";
require_once("_resources/header.inc.php");

$site_owner = get_current_user();

echo "
  <h1>Welcome to $site_title</h1>
  
  <div class='well'>
    <p>$site_owner has pulled down changes from github.</p>
  </div>
";

require_once("_resources/footer.inc.php");

?>
