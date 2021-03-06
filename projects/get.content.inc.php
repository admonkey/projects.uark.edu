<?php
  
echo "<div class='content_super_container'><div class='content_container well'>";

  if ($row["content_key"] === $row["project_key"])
    echo "<h1 class='content_title'>$row[content_title]</h1>";
  elseif (!substr_startswith($row["content_title"], "RE: "))
    echo "<h3 class='content_title'>$row[content_title]</h3>";
  else echo "<h3 class='content_title' style='display:none'>$row[content_title]</h3>";

echo "
  <label class='label label-primary'>
    <a href='$path_web_root/projects/?content_key=$row[project_key]'>Project $row[project_key]</a>
  </label>
  <label class='label label-default'>created</label>
  <label class='label label-info'>$row[content_creation_time]</label>
  <label class='label label-primary'>
    <a href='$path_web_root/Profiles/?user_key=$row[content_createdby_user_key]'>$row[content_createdby_username]</a>
  </label>
";
  
if (!empty($row["content_edited_time"])) echo "
  <label class='label label-default'>edited</label>
  <label class='label label-info'>$row[content_edited_time]</label>
  <label class='label label-primary'>
    <a href='$path_web_root/Profiles/?user_key=$row[content_editedby_user_key]'>$row[content_editedby_username]</a>
  </label>
";

?>

<?php
  // if logged in, activate jqvoter, else link to login
  if(!empty($_SESSION["user_key"])){
    $onclick_upvote = "vote_content($row[content_key],1)";
    $onclick_downvote = "vote_content($row[content_key],-1)";
  } else {
    $onclick_upvote = "$login_page";
    $onclick_downvote = "$login_page";
  }
?>

<!-- jqvoter template -->
<div class='row' style='margin-top:10px'>
  <div class='col-xs-1'>
    <div class='upvote'>
        <a class="upvote <?php if($row["my_vote"]==1) echo "upvote-on"; ?>" title="This is good stuff. Vote it up! (Click again to undo)" onclick='<?php echo "$onclick_upvote"; ?>'></a>
        <span class="count" title="Total number of votes"><?php echo "$row[total_votes]"; ?></span>
        <a class="downvote <?php if($row["my_vote"]==-1) echo "downvote-on"; ?>" title="This is not useful. Vote it down. (Click again to undo)" onclick='<?php echo "$onclick_downvote"; ?>'></a>
        <!--<a class="star" title="Mark as favorite. (Click again to undo)"></a>-->
    </div>
  </div>
  <div class='col-xs-11'>
<?php

echo "
  <p class='content_value' style='margin-top: 10px;'>$row[content_value]</p>

  <content_data id='content_data_key_$row[content_key]'
    project_key='$row[project_key]'
    thread_key='$row[thread_key]'
    parent_content_key='$row[parent_content_key]'
    has_children='$row[has_children]'
    content_key='$row[content_key]'
    content_title='$row[content_title]'
    content_value='$row[content_value]'
    content_createdby_user_key='$row[content_createdby_user_key]'
    content_editedby_user_key='$row[content_editedby_user_key]'
  />";
  
if(!empty($_SESSION["user_key"]))
  echo "<p>
    <label class='label label-primary'><a href='javascript:void(0)' onclick='show_new_content_editor($(this), false)'>Reply</a></label>";
    if($row["authorized_editor"] === "1" || $row["content_createdby_user_key"] == @$_SESSION["user_key"])
      echo "
	<label class='label label-warning'><a href='javascript:void(0)' onclick='show_content_editor($(this))'>Edit</a></label>
	<label class='label label-danger'><a href='javascript:void(0)' onclick='delete_content($row[content_key], $(this), false)'>Delete</a></label>
      ";
  echo "</p></div></div>";

echo "<div class='content_editor_well' style='display:none'></div>
  <div class='children_container' style='margin-top:10px'>";
  
if( !empty($row["has_children"]) )
  echo "
    <label class='label label-success'>
      <a 
	href='javascript:void(0)'
	onclick='fetch_content_list($row[content_key], $(this).closest(\".content_container\").find(\".children_container\"))'
	style='color:white'
      >
	<i class='fa fa-plus-circle'></i> Show Replies
      </a>
    </label>
  ";

echo "</div></div><div class='content_deleted_super_container'></div><div class='content_editor_super_container'></div></div>";
?>
