<?php
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
";
?>
