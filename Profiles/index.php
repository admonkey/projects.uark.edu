<?php

$include_mysqli = true;

// if individual profile, then validate user_key greater than zero
if ( !empty($_GET["user_key"]) && is_numeric($_GET["user_key"]) && $_GET["user_key"] > 0 ) {
  $user_key = $_GET["user_key"];
  require_once("profile.inc.php");
}
if (empty($array_profile)) {
  $user_key = false;
  $include_tablesorter = true;
}

require_once("_resources/header.inc.php");

// BEGIN if individual profile
if ( !empty($user_key) && !empty($array_profile) ) {

  echo "<h1>$array_profile[username]</h1>";

  // BEGIN check if my profile
  if (isset($_SESSION["user_key"]) && $_SESSION["user_key"] === $user_key) { 

    // privacy button
    if ($array_profile["private_profile"] === "1")
      { $class = "danger"; $privacy = "Private"; }
    else
      { $class = "success"; $privacy = "Public"; }
    ?>
    <div id='privacy_div_wrapper' style='float:right'>
      <div id='privacy_div' class='well'>
	<?php echo "
	  <p><a href='javascript:privatize_profile($array_profile[private_profile])' class='btn btn-$class'>$privacy</a></p>
	"; ?>
      </div>
    </div>
    <?php
  } // END check if my profile ?>

<div class='well'>
  <div id='directory_callback_data'></div>
</div>

<?php
} // END if individual profile
else // print list of profiles
{

  require_once("profiles.inc.php");

}

?>

<?php require_once("_resources/footer.inc.php");?>

<script>

var rendered_callback_content = "";

$(function () {
  $.ajax({
    url: "https://campusdata.uark.edu/apiv2/people/?$filter=((Uid+eq+'<?php echo $array_profile["email"]?>'))",
    dataType: 'jsonp',
    cache: 'true',
    success: function (data) {
      //recursiveIteration(data);
      rendered_callback_content = "<table>";
      for (var key in data[0]) {
	if(
	  key === "Uid" ||
	  key === "DisplayName" ||
	  key === "Enabled" ||
	  key === "Classifications" ||
	  data[0][key] === ""
	) continue;
	if(key === "Email")
	  //rendered_callback_content += "<label for='"+key+"'>"+key+"</label>: <a href='mailto:"+data[0][key]+"'>"+data[0][key]+"</a><br/>";
	  rendered_callback_content += "<tr><td class='directory_callback_keys'><label for='"+key+"' class='label label-default'>"+key+"</label></td><td><a href='mailto:"+data[0][key]+"'>"+data[0][key]+"</a></td></tr>";
	else
	  rendered_callback_content += "<tr><td class='directory_callback_keys'><label for='"+key+"' class='label label-default'>"+key+"</label></td><td>"+data[0][key]+"</td></tr>";
      }
      rendered_callback_content += "</table>";
      $("#directory_callback_data").append(rendered_callback_content);
    }
  });
});

function recursiveIteration(object) {
  rendered_callback_content = "<ul>";
  for (var property in object) {
    if (object.hasOwnProperty(property)) {
      if (typeof object[property] == "object"){
	rendered_callback_content += "<li>"+property;
	recursiveIteration(object[property]);
	rendered_callback_content += "</li>";
      }else{
	rendered_callback_content += "<li>"+property+" : " + object[property]+"</li>";
      }
    }
  }
  rendered_callback_content += "</ul>";
}

function privatize_profile(current_privacy_value){
  if (current_privacy_value === 1) {
	  new_privacy_value = "0";
  } else  new_privacy_value = "1";
  $.ajax({url: "privatize.ajax.php?user_key=<?php if(!empty($_SESSION["user_key"])) echo $_SESSION["user_key"];?>&privatize=" + new_privacy_value, 
    success: function(result){
	$("#privacy_div").html(result);
    }
  });
}

</script>

<style>
.directory_callback_keys {
  float:right;
  margin-right:5px;
  padding-top:4px;
}
</style>