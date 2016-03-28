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

  // TODO: get new project_comment_IDs
  // SELECT * FROM Comment WHERE Comment_Id NOT IN ($project_comment_IDs)

  // get existing deltaprime projects
  $sql_Link_DeltaPrimeProjects = "
    DECLARE @Link_DeltaPrimeProjects TABLE (
      Project_Id INT,
      project_key INT
    );
    INSERT INTO @Link_DeltaPrimeProjects VALUES 
  ";
  $result = $mysqli_connection->query("SELECT * FROM Link_DeltaPrimeProjects") or die($mysqli_connection->error);
  while ($row = $result->fetch_assoc()){
      $sql_Link_DeltaPrimeProjects .= "($row[Project_Id],$row[project_key]),";
  }
  // trim trailing comma and end statement;
  $sql_Link_DeltaPrimeProjects = rtrim($sql_Link_DeltaPrimeProjects, ",") . ";";
  
  // get deltaprime projects, joined with link keys
  $mssql_query = "
    SELECT
      Project_Name AS 'content_title'
      ,CASE WHEN Created_Date IS NULL THEN Changed_Date ELSE Created_Date END AS 'content_creation_time'
      ,Changed_Date AS 'content_edited_time'
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

  $mssql_result = odbc_exec($mssql_connection, $mssql_query);

  // print
  while (odbc_next_result($mssql_result)){
    //odbc_result_all($mssql_result, "border=1");
    
    $sql_insert_new_projects = "
      INSERT INTO Content (
        content_title,
        content_value,
        project_key,
        content_creation_time,
        content_createdby_user_key,
        content_edited_time,
        content_editedby_user_key
      ) VALUES
    ";
    
    $sql_update_old_projects = "";
    $sql_delete_old_projects = "";
      
    while ($row = odbc_fetch_array($mssql_result)){

      // null time if not edited
      if(empty($row["content_edited_time"])) $content_edited_time = "NULL";
      else $content_edited_time = "'$row[content_edited_time]'";

      // if new project
      if (empty($row["project_key"])){
        $sql_insert_new_projects .= "
          INSERT INTO Content (
            content_title,
            content_value,
            project_key,
            content_creation_time,
            content_createdby_user_key,
            content_edited_time,
            content_editedby_user_key
          ) VALUES (
            '$row[content_title]',
            '$row[content_value]',
            $row[project_key],
            '$row[content_creation_time]',-2,
            $content_edited_time,-2
          );
          
          -- ADD LINK HERE
          --INSERT INTO Link_DeltaPrimeProjects VALUES (,);
        ";
      }
      
      // else if deleted
      else if (empty($row["Project_Id"])){
        $sql_delete_old_projects .= " UPDATE Content SET content_deleted = TRUE WHERE content_key = $row[project_key]; ";
      }
      
      // else update project
      else {
        $sql_update_old_projects .= "
          UPDATE Content SET
            content_title = '$row[content_title]',
            content_value = '$row[content_value]',
            content_creation_time = '$row[content_creation_time]',
            content_createdby_user_key = -2,
            content_edited_time = $content_edited_time,
            content_editedby_user_key = -2,
            content_deleted = FALSE
          WHERE content_key = $row[project_key];
        ";
      }
    
    }
  
  }
    

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
