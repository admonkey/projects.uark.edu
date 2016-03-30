<?php
$page_title = "PHP";
require_once("_resources/header.inc.php");
?>

<div class='well'>
<h1>Notes on PHP</h1>
<ul>
  <li>What is a server-side <a href='http://php.net/manual/en/function.include.php'>include</a>?
    <ul>
      <li><code>&lt;?php include("path/to/file"); ?&gt;</code></li>
      <li><code>&lt;?php include_once("path/to/file"); ?&gt;</code></li>
      <li><code>&lt;?php require("path/to/file"); ?&gt;</code></li>
      <li><code>&lt;?php require_once("path/to/file"); ?&gt;</code></li>
    </ul>
  </li>
</ul>
</div><!-- /.well -->

<?php require_once('_resources/footer.inc.php');?>
