<?php
$exclude_html = true;
$include_mysqli = true;
$mssql_server = "msenterprise.walton.UARK.EDU";
$mssql_username = "username";
$mssql_password = "password";
$mssql_database = "deltaprime";
$mssql_driver = "ODBC Driver 13 for SQL Server";
require_once("_resources/header.inc.php");


if( !empty($mysqli_connected) ){

  // get existing deltaprime projects
  $sql_Link_DeltaPrimeProjects = "
    DECLARE @Link_DeltaPrimeProjects TABLE (
      Project_Id INT,
      project_key INT
    );
  ";
  $result = $mysqli_connection->query("SELECT * FROM Link_DeltaPrimeProjects") or die($mysqli_connection->error);
  if($result->num_rows > 0){
      $sql_Link_DeltaPrimeProjects .= "
        INSERT INTO @Link_DeltaPrimeProjects VALUES 
      ";
      while ($row = $result->fetch_assoc()){
          $sql_Link_DeltaPrimeProjects .= "($row[Project_Id],$row[project_key]),";
      }
      // trim trailing comma and end statement;
      $sql_Link_DeltaPrimeProjects = rtrim($sql_Link_DeltaPrimeProjects, ",") . ";";
  }

  // get deltaprime projects, joined with link keys
  $mssql_query = "
    SELECT
      Project_Name AS 'content_title'
      ,CASE WHEN Created_Date IS NULL THEN Changed_Date ELSE Created_Date END AS 'creation_time'
      ,Changed_Date AS 'edited_time'
      ,p.Project_Id
      ,l.project_key
      ,CAST(CONCAT(
        '<strong>Project_Reason:</strong> ',Project_Reason,'<br/>
        ','<strong>Business_Need:</strong> ',Business_Need,'<br/>
        ','<strong>Business_Benefit:</strong> ',Business_Benefit
      ) AS TEXT) AS 'content_value'
    FROM [deltaprime].[dbo].[Project_Info] p
    FULL OUTER JOIN @Link_DeltaPrimeProjects l
      ON p.Project_Id = l.Project_Id;
  ";
  
  // combine temp table with query
  $mssql_query = "$sql_Link_DeltaPrimeProjects $mssql_query";
  //die($mssql_query);
  
  // connect
  $mssql_connection = odbc_connect(
    "DRIVER={$mssql_driver};Server=$mssql_server;Database=$mssql_database;",
    $mssql_username, 
    $mssql_password
  ) or die("could not connect: " . odbc_errormsg());

  $mssql_result = odbc_exec($mssql_connection, $mssql_query) or die($mysqli_connection->error);

  // print
  do {
    //odbc_result_all($mssql_result, "border=1");

    while ($row = odbc_fetch_array($mssql_result)){

      // null time if not edited
      if(empty($row["edited_time"])) $content_edited_time = null;
      else $content_edited_time = "$row[edited_time]";

      // if new project
      if (empty($row["project_key"])){
        $sql_insert_new_projects = "
          CALL import_new_deltaprime_project(?,?,?,?,?);
        ";
        //die($sql_insert_new_projects);
        if (!($stmt_insert_project = $mysqli_connection->prepare($sql_insert_new_projects))) {
          echo "Prepare failed: (" . $mysqli_connection->errno . ") " . $mysqli_connection->error;
        } else {
          $stmt_insert_project->bind_param('ssssi', $row["creation_time"], $content_edited_time, $row["content_title"], $row["content_value"], $row["Project_Id"]);
          if (!$stmt_insert_project->execute()) {
            echo "Execute failed: (" . $stmt_insert_project->errno . ") ". $stmt_insert_project->error;
          } else {
            echo "inserted $row[content_title]<br/>";
          }
        }
      }

      // else if deleted
      else if (is_null($row["Project_Id"])){
        $sql_delete_old_projects = "UPDATE Content SET content_deleted = TRUE WHERE content_key = $row[project_key]; ";
        $result = $mysqli_connection->query("$sql_delete_old_projects") or die($mysqli_connection->error);
      }

      // else update project
      else {
        $sql_update_old_projects = "
          UPDATE Content SET
            content_title = ?,
            content_value = ?,
            content_creation_time = ?,
            content_createdby_user_key = -2,
            content_edited_time = ?,
            content_editedby_user_key = -2,
            content_deleted = FALSE
          WHERE content_key = ?;
        ";
        if (!($stmt_update_project = $mysqli_connection->prepare($sql_update_old_projects))) {
          echo "Prepare failed: (" . $mysqli_connection->errno . ") " . $mysqli_connection->error;
        } else {
          $stmt_update_project->bind_param('ssssi', $row["content_title"], $row["content_value"], $row["creation_time"], $content_edited_time, $row["project_key"]);
          if (!$stmt_update_project->execute()) {
            echo "Execute failed: (" . $stmt_update_project->errno . ") ". $stmt_update_project->error;
          } else {
            echo "updated $row[content_title]<br/>";
          }
        }
      }

    }

  } while (odbc_next_result($mssql_result));

  // report no updates
  echo "<br/>";
  if(empty($sql_insert_new_projects)) echo "no new projects<br/><br/>";
  if(empty($sql_update_old_projects)) echo "no updated projects<br/><br/>";
  if(empty($sql_delete_old_projects)) echo "no deleted projects<br/><br/>";


  // get existing deltaprime projects
  $sql_Link_DeltaPrimeProjects = "
    DECLARE @Link_DeltaPrimeProjects TABLE (
      Project_Id INT,
      project_key INT
    );
  ";
  $result = $mysqli_connection->query("SELECT * FROM Link_DeltaPrimeProjects") or die($mysqli_connection->error);
  if($result->num_rows > 0){
      $sql_Link_DeltaPrimeProjects .= "
        INSERT INTO @Link_DeltaPrimeProjects VALUES 
      ";
      while ($row = $result->fetch_assoc()){
          $sql_Link_DeltaPrimeProjects .= "($row[Project_Id],$row[project_key]),";
      }
      // trim trailing comma and end statement;
      $sql_Link_DeltaPrimeProjects = rtrim($sql_Link_DeltaPrimeProjects, ",") . ";";
  }

  // get existing deltaprime comments
  $sql_Link_DeltaPrimeComments = "
    DECLARE @Link_DeltaPrimeComments TABLE (
      Comment_Id INT,
      content_key INT
    );
  ";
  $result = $mysqli_connection->query("SELECT * FROM Link_DeltaPrimeComments") or die($mysqli_connection->error);
  if($result->num_rows > 0){
      $sql_Link_DeltaPrimeComments .= "
        INSERT INTO @Link_DeltaPrimeComments VALUES
      ";
      while ($row = $result->fetch_assoc()){
          $sql_Link_DeltaPrimeComments .= "($row[Comment_Id],$row[content_key]),";
      }
      // trim trailing comma and end statement;
      $sql_Link_DeltaPrimeComments = rtrim($sql_Link_DeltaPrimeComments, ",") . ";";
  }

  // get deltaprime projects, joined with link keys
  $mssql_query = "
    SELECT
      CONCAT('RE: ',Project_Name) AS 'content_title',
      Comment AS 'content_value',
      lp.project_key,
      lc.content_key,
      c.Created_Date AS 'creation_time',
      -2 AS 'content_createdby_user_key',
      c.Changed_Date AS 'edited_time',
      -2 AS 'content_editedby_user_key',
      c.Comment_Id
    FROM Comment c
    JOIN Project_Info p
      ON c.Project_Id = p.Project_Id
    LEFT JOIN @Link_DeltaPrimeProjects lp
      ON c.Project_Id = lp.Project_Id
    LEFT JOIN @Link_DeltaPrimeComments lc
      ON c.Comment_Id = lc.Comment_Id
    WHERE Comment_Type = 'PUBLIC';
  ";

  // combine temp table with query
  $mssql_query = "$sql_Link_DeltaPrimeProjects $sql_Link_DeltaPrimeComments $mssql_query";
  //die($mssql_query);

  // connect
  $mssql_connection = odbc_connect(
    "DRIVER={$mssql_driver};Server=$mssql_server;Database=$mssql_database;",
    $mssql_username, 
    $mssql_password
  ) or die("could not connect: " . odbc_errormsg());

  $mssql_result = odbc_exec($mssql_connection, $mssql_query) or die($mysqli_connection->error);

  // print
  do {
//     odbc_result_all($mssql_result, "border=1");

    while ($row = odbc_fetch_array($mssql_result)){

      // null time if not edited
      if(empty($row["edited_time"])) $content_edited_time = null;
      else $content_edited_time = "$row[edited_time]";

      // if new project
      if (empty($row["content_key"])){
        $sql_insert_new_comments = "
          CALL import_new_deltaprime_comment(?,?,?,?,?,?);
        ";
        //die($sql_insert_new_comments);
        if (!($stmt_insert_comment = $mysqli_connection->prepare($sql_insert_new_comments))) {
          echo "Prepare failed: (" . $mysqli_connection->errno . ") " . $mysqli_connection->error;
        } else {
          //print_r($row);
          $stmt_insert_comment->bind_param('ssissi', $row["content_title"], $row["content_value"], $row["project_key"], $row["creation_time"], $content_edited_time, $row["Comment_Id"]);
          if (!$stmt_insert_comment->execute()) {
            echo "Execute failed: (" . $stmt_insert_comment->errno . ") ". $stmt_insert_comment->error;
          } else {
            echo "inserted $row[content_title]<br/>";
          }
        }
      }

      // else if deleted
      else if (is_null($row["Comment_Id"])){
        $sql_delete_old_comments = "UPDATE Content SET content_deleted = TRUE WHERE content_key = $row[content_key]; ";
        $result = $mysqli_connection->query("$sql_delete_old_comments") or die($mysqli_connection->error);
      }

      // else update project
      else {
        $sql_update_old_comments = "
          UPDATE Content SET
            content_title = ?,
            content_value = ?,
            content_creation_time = ?,
            content_createdby_user_key = -2,
            content_edited_time = ?,
            content_editedby_user_key = -2,
            content_deleted = FALSE
          WHERE content_key = ?;
        ";
        if (!($stmt_update_comment = $mysqli_connection->prepare($sql_update_old_comments))) {
          echo "Prepare failed: (" . $mysqli_connection->errno . ") " . $mysqli_connection->error;
        } else {
          $stmt_update_comment->bind_param('ssssi', $row["content_title"], $row["content_value"], $row["creation_time"], $content_edited_time, $row["content_key"]);
          if (!$stmt_update_comment->execute()) {
            echo "Execute failed: (" . $stmt_update_comment->errno . ") ". $stmt_update_comment->error;
          } else {
            echo "updated $row[content_title]<br/>";
          }
        }
      }

    }

  } while (odbc_next_result($mssql_result));

  // report no updates
  echo "<br/>";
  if(empty($sql_insert_new_comments)) echo "no new comments<br/><br/>";
  if(empty($sql_update_old_comments)) echo "no updated comments<br/><br/>";
  if(empty($sql_delete_old_comments)) echo "no deleted comments<br/><br/>";

  // close
  odbc_close_all();

} else {
  // help connecting to database
  echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
  include("$path_real_root/_resources/SQL/database.help.inc.html");
}







// TODO: update linking table


require_once("_resources/footer.inc.php");
?>
