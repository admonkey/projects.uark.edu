<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if (valid_positive_integer(@$_GET["parent_content_key"]))
  $parent_content_key = "$_GET[parent_content_key]";
else
  $parent_content_key = "NULL";

if( !empty($mysqli_connected) ){
    
  $result = $mysqli_connection->query("CALL fetch_children($parent_content_key)") or die($mysqli_connection->error);

  // open table
  echo "
    <table border=1>
      <thead>
	<tr>
	  <th>Title</th>
	  <th>Created</th>
	  <th>Created By</th>
	  <th>Last Edited</th>
	  <th>Edited By</th>
	</tr>
      </thead>
      <tbody>
  ";

  // data
  while ($row = $result->fetch_assoc())
    echo "
      <tr>
	<td>
	  <content_data
	    content_title='$row[content_title]'
	    content_value='$row[content_value]'
	    project_key='$row[project_key]'
	    thread_key='$row[thread_key]'
	    parent_content_key='$row[parent_content_key]'
	    has_children='$row[has_children]'
	    content_key='$row[content_key]'
	    content_creation_time='$row[content_creation_time]'
	    content_createdby_user_key='$row[content_createdby_user_key]'
	    content_createdby_username='$row[content_createdby_username]'
	    content_edited_time='$row[content_edited_time]'
	    content_editedby_user_key='$row[content_editedby_user_key]'
	    content_editedby_username='$row[content_editedby_username]'
	  />
	  $row[content_title]
	</td>
	<td>$row[content_creation_time]</td>
	<td>$row[content_createdby_username]</td>
	<td>$row[content_edited_time]</td>
	<td>$row[content_editedby_username]</td>
      </tr>
    ";
  
    // close table
    echo "
	</tbody>
      </table>
      
      <script>
	$(hyperlink_row());
      </script>
    ";

} else {

  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>

<?php require_once("_resources/footer.inc.php");?>
