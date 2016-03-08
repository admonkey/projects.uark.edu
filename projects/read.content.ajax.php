<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if (valid_positive_integer(@$_GET["content_key"]))
  $content_key = "$_GET[content_key]";
else
  $content_key = "NULL";

if( !empty($mysqli_connected) ){
    
  $result = $mysqli_connection->query("CALL read_content($content_key)") or die($mysqli_connection->error);

  $row = $result->fetch_assoc();
  
  echo "
    <h1>$row[content_title]</h1>
    
    <p>
      <label class='label label-primary'>
	<a href='$path_web_root/Profiles/?user_key=$row[content_createdby_user_key]'>$row[content_createdby_username]</a>
      </label>
    </p>
    <p><label class='label label-info'>$row[content_creation_time]</label></p>
  ";
    
  if (!empty($row["content_editedby_user_key"])) echo "
    <p>
      <label class='label label-primary'>
	<a href='$path_web_root/Profiles/?user_key=$row[content_editedby_user_key]'>$row[content_editedby_username]</a>
      </label>
    </p>
    <p><label class='label label-info'>$row[content_edited_time]</label></p>
  ";
    
  echo "
    <p>$row[content_value]</p>
    
    

    <content_data id='content_data_key_$row[content_key]'
      project_key='$row[project_key]'
      thread_key='$row[thread_key]'
      parent_content_key='$row[parent_content_key]'
      has_children='$row[has_children]'
      content_key='$row[content_key]'
      content_createdby_user_key='$row[content_createdby_user_key]'
      content_editedby_user_key='$row[content_editedby_user_key]'
    />
  ";

} else {

  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>

<?php require_once("_resources/footer.inc.php");?>
