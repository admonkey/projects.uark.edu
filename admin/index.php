<?php

include_once('_resources/credentials.inc.php');
//$page_title = "Home Page";
require_once('_resources/header.inc.php');

echo "<h1>$section_title</h1>"; ?>

<div class='well'>
<h2>Local Links to Website Components</h2>
<ul>
<?php echo "

  <li>
    Systems
    <ul>
      <li><a href='$path_web_root/Login/'>Authentication Login Mechanisms</a></li>
    </ul>
  </li>

  <li>
    Templates
    <ul>
      <li><a href='$path_web_root/Forms/'>Forms</a></li>
      <li><a href='$path_web_root/Tables/'>Tables</a></li>
      <li><a href='$path_web_root/FancyBox/'>FancyBox</a></li>
      <li><a href='$path_web_root/Embed/'>Mobile Responsive Video Embed</a></li>
    </ul>
  </li>

  <li>
    Notes
    <ul>
      <li><a href='$path_web_root/admin/php.php'>PHP</a></li>
      <li><a href='$path_web_root/SubTree/'>git subtree</a></li>
    </ul>
  </li>

"; ?>
</ul>
</div><!-- /.well -->

<?php require_once('_resources/footer.inc.php');?>
