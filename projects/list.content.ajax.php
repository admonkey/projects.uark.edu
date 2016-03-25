<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if (valid_positive_integer(@$_GET["parent_content_key"]))
  $parent_content_key = "$_GET[parent_content_key]";
else
  $parent_content_key = "NULL";

if (!empty($_SESSION["user_key"]))
  $user_key = $_SESSION["user_key"];
else
  $user_key = "NULL";

if( !empty($mysqli_connected) ){
    
  $result = $mysqli_connection->query("CALL fetch_children($parent_content_key,$user_key)") or die($mysqli_connection->error);

  if(isset($_GET["list"])){ // BEGIN IF list

    while ($row = $result->fetch_assoc()){

      include("get.content.inc.php");
      
    }

  } else { // END IF list, BEGIN IF table

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
	<tr class='hover' onclick='click_row($(this))'>
	  <td>
	    <content_data
	      project_key='$row[project_key]'
	      thread_key='$row[thread_key]'
	      parent_content_key='$row[parent_content_key]'
	      has_children='$row[has_children]'
	      content_key='$row[content_key]'
	      content_createdby_user_key='$row[content_createdby_user_key]'
	      content_editedby_user_key='$row[content_editedby_user_key]'
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
      ";

  } // END IF table

} else {

  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>

<?php require_once("_resources/footer.inc.php");?>
