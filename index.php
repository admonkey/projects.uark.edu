<?php

$no_well_container = true;
// $page_title = 'Home Page';
// $section_title = 'Root Section';
$include_mysqli = true;
require_once('_resources/header.inc.php');

$site_owner = get_current_user();


// call the sql precedue to find the total votes in the system.
$sql = 'CALL tot_vote_display';
// Call total vote display
if (!($stmt1 = $mysqli_connection->prepare($sql))) {
	echo 'Prepare failed: (' . $mysqli_connection->errno . ') ' . $mysqli_connection->error;
}
else {
	if (!$stmt1->execute()) {
		echo 'Execute failed: (' . $stmt1->errno . ') ' . $stmt1->error;
	}
	$stmt1->bind_result($votes);
	while ($stmt1->fetch()) {
	}
	/* close statement */
    $stmt1->close();
}

// call the sql precedue to find the total comments in the system.
$sql = 'CALL tot_comment_display';
// Call total vote display
if (!($stmt2 = $mysqli_connection->prepare($sql))) {
	echo 'Prepare failed1: (' . $mysqli_connection->errno . ') ' . $mysqli_connection->error;
}
else {
	if (!$stmt2->execute()) {
		echo 'Execute failed: (' . $stmt2->errno . ') ' . $stmt2->error;
	}
	$stmt2->bind_result($comments);
	while ($stmt2->fetch()) {
	}
}

$votes_total = $votes;
$comments_total = $comments;
echo "
  <h1>Welcome to $site_title</h1>
  
  <div class='well'>
  We got  $votes_total votes in total.
  We got $comments_total comments in total.

  </div>
  
 <script>
(function(w,d,s,g,js,fs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
  js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
}(window,document,'script'));
</script>

<div id='embed-api-auth-container'></div>
<div id='chart-container'></div>
<div id='view-selector-container'></div>


<script>

gapi.analytics.ready(function() {

  /**
   * Authorize the user immediately if the user has already granted access.
   * If no access has been created, render an authorize button inside the
   * element with the ID 'embed-api-auth-container'.
   */
  gapi.analytics.auth.authorize({
    container: 'embed-api-auth-container',
    clientid: '621347112836-tbs542t3ov5i21h3gi3vf1tup7g968ea.apps.googleusercontent.com'
  });


  /**
   * Create a new ViewSelector instance to be rendered inside of an
   * element with the id 'view-selector-container'.
   */
  var viewSelector = new gapi.analytics.ViewSelector({
    container: 'view-selector-container'
  });

  // Render the view selector to the page.
  viewSelector.execute();


  /**
   * Create a new DataChart instance with the given query parameters
   * and Google chart options. It will be rendered inside an element
   * with the id 'chart-container'.
   */
  var dataChart = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:date',
      'start-date': '30daysAgo',
      'end-date': 'yesterday'
    },
    chart: {
      container: 'chart-container',
      type: 'LINE',
      options: {
        width: '100%'
      }
    }
  });


  /**
   * Render the dataChart on the page whenever a new view is selected.
   */
  viewSelector.on('change', function(ids) {
    dataChart.set({query: {ids: ids}}).execute();
  });

});
</script>

";

require_once('_resources/footer.inc.php');

?>
