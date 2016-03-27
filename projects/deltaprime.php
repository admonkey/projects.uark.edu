<?php
$exclude_html = true;
$include_mysqli = true;
$mssql_server = "msenterprise.walton.UARK.EDU";
$mssql_username = "username";
$mssql_password = "password";
$mssql_database = "deltaprime";
$mssql_driver = "ODBC Driver 13 for SQL Server";
require_once("_resources/header.inc.php");

// TODO: get existing deltaprime projects
// $project_info_IDs = SELECT Project_Id FROM Link_DeltaPrimeProjects;

// TODO: get existing deltaprime comments
// $project_comment_IDs = SELECT Comment_Id FROM Link_DeltaPrimeProjects;

// connect
$mssql_connection = odbc_connect(
  "DRIVER={$mssql_driver};Server=$mssql_server;Database=$mssql_database;",
  $mssql_username, 
  $mssql_password
) or die("could not connect: " . odbc_errormsg());

// query
// TODO: get new project_info_IDs
// SELECT * FROM Project_Info WHERE Project_Id NOT IN ($project_info_IDs)

// TODO: get new project_comment_IDs
// SELECT * FROM Comment WHERE Comment_Id NOT IN ($project_comment_IDs)
$mssql_query = "select * from [deltaprime].[dbo].[COMMENT]";
$mssql_result = odbc_exec($mssql_connection, $mssql_query);

// print
odbc_result_all($mssql_result, "border=1");

// close
odbc_close_all();

// TODO: insert new projects and comments

require_once("_resources/footer.inc.php");
?>
