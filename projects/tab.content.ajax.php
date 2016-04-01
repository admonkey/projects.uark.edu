<?php
$include_mysqli = true;
require_once("_resources/header.inc.php");

if( !empty($mysqli_connected) ){

  $result = $mysqli_connection->query("CALL fetch_projects()") or die($mysqli_connection->error);

  // open table
  echo "
    <table border=1>
      <thead>
        <tr>
          <th>Votes</th>
          <th>Title</th>
          <th>Created</th>
          <th>Created By</th>
          <th>Updated</th>
          <th>Comments</th>
        </tr>
      </thead>
      <tbody>
  ";

  // data
  while ($row = $result->fetch_assoc()){
    if(empty($row["total_votes"])) $total_votes = "0";
    else $total_votes = $row["total_votes"];
    echo "
      <tr class='hover' onclick='click_row($(this))'>
        <td>$total_votes</td>
        <td>
          <content_data
            project_key='$row[content_key]'
            content_key='$row[content_key]'
          />
          $row[content_title]
        </td>
        <td>$row[content_creation_time]</td>
        <td>$row[content_createdby_username]</td>
        <td>$row[last_updated]</td>
        <td>$row[total_comments]</td>
      </tr>
    ";
  }

  // close table
  echo "
      </tbody>
    </table>
  ";


} else {

  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>

<?php require_once("_resources/footer.inc.php");?>
