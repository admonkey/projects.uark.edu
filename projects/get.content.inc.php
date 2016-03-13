<?php
  
echo "<div class='content_container well'>";

  if ($row["content_key"] === $row["project_key"])
    echo "<h1>$row[content_title]</h1>";
  elseif ($row["content_key"] === $row["thread_key"])
    echo "<h2>$row[content_title]</h2>";
  elseif (!substr_startswith($row["content_title"], "RE: "))
    echo "<h3>$row[content_title]</h3>";

echo "
  <label class='label label-default'>created</label>
  <label class='label label-info'>$row[content_creation_time]</label>
  <label class='label label-primary'>
    <a href='$path_web_root/Profiles/?user_key=$row[content_createdby_user_key]'>$row[content_createdby_username]</a>
  </label>
  <label class='label label-primary'>
    <a href='$path_web_root/projects/?content_key=$row[project_key]'>Project $row[project_key]</a>
  </label>
";
  
if (!empty($row["content_editedby_user_key"])) echo "
  <p>
    <label class='label label-primary'>
      <a href='$path_web_root/Profiles/?user_key=$row[content_editedby_user_key]'>$row[content_editedby_username]</a>
    </label>
  </p>
  <p><label class='label label-info'>$row[content_edited_time]</label></p>
";
  
echo "
  <p style='margin-top: 10px;'>$row[content_value]</p>

  <content_data id='content_data_key_$row[content_key]'
    project_key='$row[project_key]'
    thread_key='$row[thread_key]'
    parent_content_key='$row[parent_content_key]'
    has_children='$row[has_children]'
    content_key='$row[content_key]'
    content_createdby_user_key='$row[content_createdby_user_key]'
    content_editedby_user_key='$row[content_editedby_user_key]'
  />
  
  <p><label class='label label-primary'><a href='javascript:void(0)' onclick='show_new_content_editor($(this), false)'>Reply</a></label></p>
  <div class='content_editor_well well' style='display:none'></div>
  <div class='children_container' style='margin-top:10px'>";
  
if(!empty($row["has_children"]))
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

echo "</div></div>";
?>
