<?php

if( !empty($mysqli_connected) ){
    
    $query_profiles="
	SELECT user_key, username, user_creation_time
	FROM `Users`
    ";
    if (empty($_SESSION["user_groups"]["ADMIN"]))
      $query_profiles .= " WHERE private_profile = 0";
    
    $result_profiles = $mysqli_connection->query($query_profiles);
    
    if (!$result_profiles)
      die($mysqli_connection->error);
    else {
      // open table
      echo "
	<h1>".$result_profiles->num_rows." Active Users</h1>
	  <table border=1>
		  <thead>
			  <tr>
				      <th>Username</th>
				      <th>Member Since</th>
			  </tr>
		  </thead>
		  <tbody>
      ";
    }
    
    while ($array_profile = $result_profiles->fetch_array(MYSQLI_ASSOC)) {
      echo "
	<tr>
		<td><user_data user_key='$array_profile[user_key]'</user_data>$array_profile[username]</td>
		<td>$array_profile[user_creation_time]</td>
	</tr>\n";
    }
    
    // close table
    ?>
	    </tbody>
    </table>
    
    <script>
      function hyperlink_row(){
	      $("tr").click( function() {
		      var row = $(this);
		      var user_key = row.find("user_data").attr("user_key");
		      var href = "?user_key=" + user_key;
		      window.location = href;
	      }).hover( function() {
		      $(this).toggleClass("hover");
	      });
      }
      $(hyperlink_row());
    </script>
    <style>
      tr.hover {
	cursor: pointer;
      }
    </style>

    <?php

} else {

    // help connecting to database
    echo "<p class='bg-danger text-danger'>ERROR: Not Connected to Database</p>";
    include("$path_real_root/_resources/SQL/database.help.inc.html");

}
?>
