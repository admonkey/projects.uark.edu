<?php

$include_jquery_ui = true;
$include_tablesorter = true;
$include_mysqli = true;
require_once("_resources/header.inc.php");

?>

<?php
if(empty($_GET["content_key"])) {
  $show_projects_link = "javascript:toggle_list($(\"#show_list_of_projects\"), $(\"#list_of_projects_div\"))";
  $show_projects_class = "class='btn btn-warning'>Hide Projects";
} else {
  $show_projects_link = "./";
  $show_projects_class = "class='btn btn-success'>Show Projects";
}
?>

<div id='page_controls' class='row'>
  <div class='col-xs-4'><p><a btn-text='Projects' btn-show='false' id='show_list_of_projects' href='<?php echo "$show_projects_link"; ?>' <?php echo "$show_projects_class"; ?></a></p></div>
  <!--<div class='col-xs-4'><p><a id='show_list_of_threads' href='javascript:fetch_threads()' class='btn btn-primary'>Show Threads</a></p></div>-->
  <?php if (isset($_SESSION["user_key"])) { ?>
    <div class='col-xs-4'><p><a href='javascript:create_project()' class='btn btn-success'>Create New Project</a></p></div>
  <?php } ?>
</div><!-- /#page_controls.row -->


<div id='list_of_projects_div' class='table-responsive' style='display:none'>
</div><!-- /#list_of_projects_div.table-responsive -->

<div id='project_content_div' <?php if(empty($_GET["content_key"])) echo "style='display:none'"; ?>>
<?php if(!empty($_GET["content_key"])) include("read.content.ajax.php"); ?>
</div><!-- /#list_of_projects_div.table-responsive -->

<div class='well' style='display:none'>
<div id='list_of_threads_div' class='table-responsive' style='display:none'>
</div><!-- /#list_of_threads_div.table-responsive -->

<div id='thread_div' style='display:none'>
</div><!-- /#thread_div.well -->
</div>

<?php
if (!isset($_SESSION["user_key"])) { ?>

	<p><a href='<?php echo $login_page ?>' class='btn btn-danger'>Not Logged In</a></p>

<?php } else { ?>

<!-- post message text area -->
<div id='message_div' class='well' style='display:none'>

	<form id='message_form' method='post' role='form' onsubmit='return submit_project($(this))'>

		<input id='message_thread_id' name='message_thread_id' type='hidden'></input>
		
		<div id='thread_name_div' class='form-group'>
			<label for='content_title'>Project Title:</label>
			<input id='content_title' name='content_title' type='text' class='form-control' required></input>
		</div>

		<div class='form-group'>
			<label for='content_value'>Message (max 1000 characters):</label>
			<textarea class='form-control' style='width:100%' maxlength='1000' rows='3' id='content_value' name='content_value' required></textarea>
		</div>

		<button type='submit' class='btn btn-primary'>Submit</button>

	</form>

</div>

<!-- message editor template for cloning -->
<div id='message_editor' class='message_editor well' style='display:none'>
  <form method='post' role='form' onsubmit='return false'>
    <input name='parent_content_key' type='hidden'></input>

    <div id='content_title_div' class='form-group' style='display:none'>
      <label for='content_title'>Title:</label>
      <input id='content_title' name='content_title' type='text' class='form-control' disabled required></input>
    </div>
    <!-- $(this).prev(".content_title_div").show().find("#content_title").prop("disabled",false); -->
    <p onclick='$(this).hide().closest(".message_editor").find("#content_title_div").show("slide").find("#content_title").prop("disabled",false).focus()'><label class='label label-success'><a href='javascript:void(0)' style='color:white'><i class='fa fa-plus-circle'></i> Add Title</a></label></p>

    <div class='form-group'>
      <label for='content_value'>Message (max 1000 characters):</label>
      <textarea class='form-control' style='width:100%' maxlength='1000' rows='3' name='content_value' required></textarea>
    </div>
    <a href='javascript:void(0)' onclick='reply_content($(this))' class='btn btn-primary'>Submit</a>
    <a href='javascript:void(0)' onclick='show_new_content_editor($(this), true)' class='btn btn-danger'>Cancel</a>
  </form>
</div><!-- /#message_editor -->

<!-- message editor template for cloning -->
<div id='update_editor' class='message_editor well' style='display:none'>
  <form method='post' role='form' onsubmit='return false'>
    <input name='content_key' type='hidden'></input>

    <div id='content_title_div' class='form-group' style='display:none'>
      <label for='content_title'>Title:</label>
      <input id='content_title' name='content_title' type='text' class='form-control' disabled required></input>
    </div>
    <!-- $(this).prev(".content_title_div").show().find("#content_title").prop("disabled",false); -->
    <p onclick='$(this).hide().closest(".message_editor").find("#content_title_div").show("slide").find("#content_title").prop("disabled",false).focus()'><label class='label label-success'><a href='javascript:void(0)' style='color:white'><i class='fa fa-plus-circle'></i> Add Title</a></label></p>

    <div class='form-group'>
      <label for='content_value'>Message (max 1000 characters):</label>
      <textarea class='form-control' style='width:100%' maxlength='1000' rows='3' name='content_value' required></textarea>
    </div>
    <a href='javascript:void(0)' onclick='update_content($(this))' class='btn btn-primary'>Submit</a>
    <a href='javascript:void(0)' onclick='show_new_content_editor($(this), true)' class='btn btn-danger'>Cancel</a>
  </form>
</div><!-- /#message_editor -->


<!-- jqvoter template -->
<div id="templates" class="hidden">
    <div class="upvote">
        <a class="upvote" title="This is good stuff. Vote it up! (Click again to undo)"></a>
        <span class="count" title="Total number of votes"></span>
        <a class="downvote" title="This is not useful. Vote it down. (Click again to undo)"></a>
        <a class="star" title="Mark as favorite. (Click again to undo)"></a>
    </div>
</div>

<?php } // END if (!isset($_SESSION["user_key"])) ?>

<?php require_once("_resources/footer.inc.php");?>


<style>
  tr.hover {
    cursor: pointer;
  }
  .message_metadata {
    float: right;
  }
  label {
    margin-right: 5px;
  }
</style>


<script>

function isset(variable) {
    return typeof variable !== typeof undefined ? true : false;
}

function toggle_list(toggle_btn, toggle_div){
  if(toggle_btn.attr("btn-show") === "true"){
    toggle_div.show("blind");
    toggle_btn.text("Hide "+toggle_btn.attr("btn-text")).addClass("btn-warning").removeClass("btn-success").attr("btn-show", "false");
  } else {
    toggle_div.hide("blind");
    toggle_btn.text("Show "+toggle_btn.attr("btn-text")).removeClass("btn-warning").addClass("btn-success").attr("btn-show", "true");
  }
}

function fetch_content_table(parent_content_key, insert_div){
  insert_div.hide("blind", function(){
    $("#thread_div").hide("blind");
    $.ajax({url: "list.content.ajax.php?parent_content_key="+parent_content_key,
      success: function(result){
	insert_div.html(result);
	apply_tablesorter();
	insert_div.show("blind");
      }
    });
  });
}

function fetch_content_list(parent_content_key, insert_div){
  insert_div.hide("blind", function(){
    $.ajax({url: "list.content.ajax.php?list&parent_content_key="+parent_content_key,
      success: function(result){
	insert_div.html(result);
	apply_tablesorter();
	insert_div.show("blind", function(){
	  insert_div.closest(".content_container").hide().show("highlight", {duration:2000});
	  insert_div.alternateNestedBgColor(['white', '#f5f5f5']);
	  // scroll to newest content
	  /*
	  $("html, body").animate({
	      scrollTop: (insert_div.find(".children_container").last().closest(".content_container").hide().show("highlight", {duration:5000} ).offset().top) - (0.75*screen.height)
	  }, "slow");
	  */
	  insert_div.find(".upvote").upvote();
	});
      }
    });
  });
}

function fetch_content(content_key, insert_div){
  insert_div.hide("blind", function(){
    $.ajax({url: "read.content.ajax.php?content_key="+content_key, 
      success: function(result){
	insert_div.html(result);
	insert_div.find(".upvote").upvote();
	insert_div.show("blind");
	history.pushState({}, null, "<?php echo "$path_web_root" ?>/projects/?content_key="+content_key);
	ga('send', 'pageview');
	if(insert_div.is($("#thread_div")))
	  fetch_content_list(content_key, $("#thread_div").find(".children_container"));
      }
    });
  });
}

function click_row(tr){
  var content_key = tr.find("content_data").attr("content_key");
  var thread_key = tr.find("content_data").attr("thread_key");
  if(! thread_key){
    toggle_list($("#show_list_of_projects"), $("#list_of_projects_div"));
    fetch_content(content_key,$("#project_content_div"));
    //fetch_content_table(content_key, $("#list_of_threads_div"));
  } else
    fetch_content(thread_key,$("#thread_div"));
  tr.addClass("bg-primary").siblings().removeClass("bg-primary");
}

function reply_content(element){
  var serialized_data = element.closest("form").serialize();
  console.log("reply: serialized_data = " + serialized_data);
  var children_container = element.closest(".content_container").children(".children_container");
  console.log("reply: children_container = " + children_container);
  var parent_content_key = element.closest(".content_container").find("content_data").attr("content_key");
  console.log("reply: parent_content_key = " + parent_content_key);
  $.post('reply.content.ajax.php', serialized_data, function(result) {
    children_container.hide("slide", function(){
      var content_editor_well = element.closest(".content_container").children(".content_editor_well");
      content_editor_well.hide("slide", function(){
	content_editor_well.html("");
	children_container.html("");
	fetch_content_list(parent_content_key, children_container);
      });
    });
  });
}

function submit_project(element){
  var serialized_data = element.closest("form").serialize();
  //var children_container = element.closest(".content_container").children(".children_container");
  //var parent_content_key = element.closest(".content_container").children("content_data").attr("content_key");
  $.post('reply.content.ajax.php', serialized_data, function(result) {
    
    $("#message_div").html(result);
    
    /*
    children_container.hide("slide", function(){
      var content_editor_well = element.closest(".content_container").children(".content_editor_well");
      content_editor_well.hide("slide", function(){
	content_editor_well.html("");
	children_container.html("");
	fetch_content_list(parent_content_key, children_container);
      });
    });
    */
  });
  return false;
}

function show_new_content_editor(element, cancel){
  var content_editor_well = element.closest(".content_container").children(".content_editor_well");
  var content_editor_super_container = element.closest(".content_super_container").children(".content_editor_super_container");
  if (cancel) {
    content_editor_super_container.hide("slide", function(){
      element.closest(".content_super_container").children(".content_container").show("slide");
      content_editor_super_container.html("").show();
    });
    content_editor_well.hide("slide", function(){
      content_editor_well.html("");
    });
  } else {
    var content_editor = $("#message_editor").clone();
    auto_expand_textarea(content_editor.find("textarea"));
    var parent_content_key = element.closest(".content_container").find("content_data").attr("content_key");
    console.log("parent key = " + parent_content_key);
    content_editor.find("[name=parent_content_key]").val(parent_content_key);
    //message_editor.find("textarea").val(message_body_well.find(".message_text").prop("innerHTML").replace(/<br>/g, ""));
    content_editor_well.hide("slide", function(){
      content_editor_well.html(content_editor.show());
      content_editor_well.show("slide", function(){
	$("html, body").animate({
	    scrollTop: content_editor_well.offset().top
	}, "slow");
	content_editor.find("textarea").focus()
      });
    });
  }
}

function create_project() {
	$("#list_of_threads_div").hide("blind");
	$("#list_of_projects_div").hide("blind");
	$("#thread_div").hide("blind", function(){$("#thread_messages_div").html("")});
	$("#message_div").show("blind");
}

function delete_content(content_key, element, undo){
	var content_super_container = element.closest(".content_super_container");
	var content_deleted_super_container = content_super_container.children(".content_deleted_super_container");
	var content_container = content_super_container.children(".content_container");
	if (undo) {
		$.ajax({url: "delete.content.ajax.php?content_key=" + content_key, success: function(result){
			content_super_container.hide("slide", function(){
				content_deleted_super_container.hide();
				content_container.show();
				content_super_container.show("slide");
			});
		},cache: false});
	} else {
		$.ajax({url: "delete.content.ajax.php?delete&content_key=" + content_key, success: function(result){
			content_super_container.hide("slide", function(){
				content_container.hide();
				content_deleted_super_container.html(result).show();
				content_super_container.show("slide");
			});
		},cache: false});
	}
}

function show_content_editor(element){
  var content_super_container = element.closest(".content_super_container");
  console.log(content_super_container);
  var content_container = content_super_container.children(".content_container");
  console.log(content_container);
  var content_editor_super_container = content_super_container.children(".content_editor_super_container");
  console.log(content_editor_super_container);
  var content_editor = $("#update_editor").clone();
  auto_expand_textarea(content_editor.find("textarea"));
  var content_key = content_container.find("content_data").attr("content_key");
  console.log(content_key);
  var content_title = content_container.find(".content_title").first().text();
  console.log(content_title);
  var content_value = content_container.find(".content_value").first().text();
  console.log(content_value);
  content_editor.find("[name=content_key]").val(content_key);
  content_editor.find("[name=content_title]").val(content_title);
  content_editor.find("[name=content_value]").val(content_value);
  content_super_container.hide("slide", function(){
    content_editor_super_container.html(content_editor.show());
    content_container.hide();
    content_super_container.show("slide", function(){
      $("html, body").animate({
	  scrollTop: content_editor_super_container.offset().top
      }, "slow");
      content_editor.find("textarea").focus()
    });
  });
}

function update_content(element){
  var content_super_container = element.closest(".content_super_container");
  var content_container = content_super_container.children(".content_container");
  var content_editor_super_container = content_super_container.children(".content_editor_super_container");
  var serialized_data = element.closest("form").serialize();
  
  //var children_container = element.closest(".content_container").children(".children_container");
  var content_key = content_container.find("content_data").attr("content_key");
  $.post('update.content.ajax.php', serialized_data, function(result) {
    content_super_container.hide("slide", function(){
      content_editor_super_container.html("");
      fetch_content(content_key, content_super_container);
    });
  });
}

function vote_content(content_key,vote_value){
  $.ajax({url: "vote.ajax.php?content_key=" + content_key + "&vote_value=" + vote_value, success: function(result){
    console.log(result);
  }});
}

// http://stackoverflow.com/questions/10055299/are-alternate-nested-styles-possible-in-css#answer-10055729
jQuery(function($) {
    $.fn.alternateNestedBgColor = function(colors) {
        // While not a great optimization, length of the colors array always stays the same
        var l = colors.length;

        // Itterate over all element in possible array
        // jQuery best practice to handle initializing multiple elements at once
        return this.each(function() {
            var $sub = $(this), i = 0; 

            // Executes code, at least once
            do {

                // Set bg color for current $sub element
                $sub.css('backgroundColor', colors[i++ % l]);
                // Set $sub to direct children matching given selector
                $sub = $sub.parents(".well");

            // Will repeat the do section if the condition returns true
            } while ($sub.length > 0);
        });
    };
});

$(function(){
  
  $(".upvote").upvote();
});

<?php if(!empty($_GET["content_key"])) {
  echo "$(fetch_content_table($_GET[content_key], $(\"#list_of_threads_div\")));";
} else {
  ?>$(fetch_content_table(null, $("#list_of_projects_div")));<?php
}
?>

</script>
<script src="<?php echo "$path_web_root/"; ?>_resources/jqvote/lib/jquery.upvote.js"></script>
<script src="<?php echo "$path_web_root/"; ?>_resources/jqvote/lib/jquery.upvote.js"></script>
<link rel="stylesheet" href="<?php echo "$path_web_root/"; ?>_resources/jqvote/lib/jquery.upvote.css"></link>