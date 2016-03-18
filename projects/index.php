<?php

$include_jquery_ui = true;
$include_tablesorter = true;
$include_mysqli = true;
require_once("_resources/header.inc.php");

if(empty($_GET["content_key"])) echo "<h1>$section_title</h1>";

?>
<p class='lead'>Make sure you have read <a href='rules.php'>the rules</a>.</p>

<div id='page_controls' class='row'>
  <div class='col-xs-4'><p><a btn-text='Projects' btn-show='false' id='show_list_of_projects' href='javascript:toggle_list($("#show_list_of_projects"), $("#list_of_projects_div"))' class='btn btn-warning'>Hide Projects</a></p></div>
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

<div class='well'>
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
			<textarea class='form-control' style='width:100%' maxlength='140' rows='3' id='content_value' name='content_value' required></textarea>
		</div>

		<button type='submit' class='btn btn-primary'>Submit</button>

	</form>

</div>

<!-- message editor template for cloning -->
<div id='message_editor' class='message_editor' style='display:none'>
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
      <textarea class='form-control' style='width:100%' maxlength='140' rows='3' name='content_value' required></textarea>
    </div>
    <a href='javascript:void(0)' onclick='reply_content($(this))' class='btn btn-primary'>Submit</a>
    <a href='javascript:void(0)' onclick='show_new_content_editor($(this), true)' class='btn btn-danger'>Cancel</a>
  </form>
</div><!-- /#message_editor -->

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
	  $("html, body").animate({
	      scrollTop: (insert_div.find(".children_container").last().closest(".content_container").hide().show("highlight", {duration:5000} ).offset().top) - (0.75*screen.height)
	  }, "slow");
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
	insert_div.show("blind");
	history.pushState({}, null, "<?php echo "$path_web_root" ?>/projects/?content_key="+content_key);
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
    fetch_content_table(content_key, $("#list_of_threads_div"));
  } else
    fetch_content(thread_key,$("#thread_div"));
  tr.addClass("bg-primary").siblings().removeClass("bg-primary");
}

function reply_content(element){
  var serialized_data = element.closest("form").serialize();
  var children_container = element.closest(".content_container").children(".children_container");
  var parent_content_key = element.closest(".content_container").children("content_data").attr("content_key");
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
  if (cancel) {
    content_editor_well.hide("slide", function(){
      content_editor_well.html("");
    });
  } else {
    var content_editor = $("#message_editor").clone();
    auto_expand_textarea(content_editor.find("textarea"));
    var parent_content_key = element.closest(".content_container").children("content_data").attr("content_key");
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

$(fetch_content_table(null, $("#list_of_projects_div")));

<?php if(!empty($_GET["content_key"])) echo "$(fetch_content_table($_GET[content_key], $(\"#list_of_threads_div\")));"; ?>

// old Forum javascript ///////////////////////////////////////////////////////////////////////////

function show_editor(element, cancel){
  var message_editor_well = element.parents(".message_wrapper").find(".message_editor_well");
  var message_body_well = element.parents(".message_wrapper").find(".message_body_well");
  if (cancel) {
    message_editor_well.hide("slide", function(){
		      message_editor_well.html("");
		      message_body_well.show("slide");
	      });
  } else {
    var message_editor = $("#message_editor").clone();
	      auto_expand_textarea(message_editor.find("textarea"));
    message_editor.find("[name=message_id]").val(element.parents(".message_metadata").find("message_data").attr("message_id"));
    message_editor.find("textarea").val(message_body_well.find(".message_text").prop("innerHTML").replace(/<br>/g, ""));
	      message_editor_well.html(message_editor.show());
    message_body_well.hide("slide", function(){message_editor_well.show("slide")});
  }
}

function fetch_threads(){
	if ($("#list_of_threads_div").is(":hidden")) {
		$.ajax({url: "threads.ajax.php", 
			success: function(result){
				$("#list_of_threads_div").html(result);
				apply_tablesorter();
				$("#list_of_threads_div").show("blind", function(){
				  $("#show_list_of_threads").text("Hide Threads").addClass("btn-warning").removeClass("btn-primary");
				});
			}
		});
	} else {
		$("#list_of_threads_div").hide("blind", function(){
		  $("#show_list_of_threads").text("Show Threads").removeClass("btn-warning").addClass("btn-primary");
		});
	}
}

function create_thread() {
	$("#list_of_threads_div").hide("blind");
	$("#thread_div").hide("blind", function(){$("#thread_messages_div").html("")});
	$("#message_div").show("blind");
	$("#thread_name").prop("disabled",false);
	$("#thread_name_div").show("blind");
}

function disable_create_thread() {
	$("#thread_name_div").hide("blind");
	$("#thread_name").val("").prop("disabled",true);
}

function fetch_messages(thread_id,thread_name,page_number){
	$.ajax({url: "messages.ajax.php?thread_id=" + thread_id + "&page_number=" + page_number, success: function(result){
		fetch_threads();
		$("#thread_div").hide("blind",function(){
			$("#thread_name_h2").text(thread_name);
			$("#thread_messages_div").html(result);
			$("#thread_div").show("blind", function(){$("#message_div").show("blind")});
		});
		$("#message_thread_id").val(thread_id);
		history.pushState({}, null, "<?php echo "$path_web_root" ?>/Forum/?thread_id="+thread_id);
		ga('send', 'pageview', "<?php echo "$path_web_root" ?>/Forum/?thread_id="+thread_id);
	},cache: false});
}

function message_submit() {
	var serialized_data = $("#message_form").serialize();
	$.post('message.create.ajax.php', serialized_data, function(result) {
		if ( $("#thread_name").val() !== "" ) {
			$("#thread_name_h2").text($("#thread_name").val());
		}
		$("#thread_div").show("blind");
		var new_div = $("<div style='display:none'></div>");
		new_div.html(result).appendTo("#thread_messages_div").show("slide");
		$("#message_text").val("");
		$("#message_thread_id").val(new_div.find("message_data").attr("thread_id"));
		disable_create_thread();
	});
	return false;
}

function update_message_submit(element) {
	var serialized_data = element.parents("form").serialize();
	var message_wapper = element.parents(".message_wrapper");
	$.post('message.update.ajax.php', serialized_data, function(result) {
		message_wapper.hide("slide", function(){
			message_wapper.html(result).show("slide");
		});
	});
	return false;
}

function delete_message(message_id, element, undo){
	var message_wrapper = element.parents(".message_wrapper");
	var message_body_well = message_wrapper.find(".message_body_well");
	var message_editor_well = message_wrapper.find(".message_editor_well");
	if (undo) {
		$.ajax({url: "message.delete.ajax.php?message_id=" + message_id, success: function(result){
			message_wrapper.hide("slide", function(){
				message_editor_well.html("").hide();
				message_body_well.show();
				message_wrapper.show("slide");
			});
		},cache: false});
	} else {
		$.ajax({url: "message.delete.ajax.php?message_id=" + message_id, success: function(result){
			message_wrapper.hide("slide", function(){
				message_body_well.hide();
				message_editor_well.html(result).show();
				message_wrapper.show("slide");
			});
		},cache: false});
	}
}

</script>