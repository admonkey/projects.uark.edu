<?php

include_once('_resources/credentials.inc.php');
$include_jquery_ui = true;
$include_tablesorter = true;
$include_mysqlo = true;
require_once('_resources/header.inc.php');

echo "<h1>$section_title</h1>";

?>
<p class='lead'>Make sure you have read <a href='rules.php'>the rules</a>.</p>

<div id='page_controls' class='row'>
	<div class='col-xs-6'><p><a id='show_list_of_threads' href='javascript:fetch_threads()' class='btn btn-primary'>Show Threads</a></p></div>
	<?php if (isset($_SESSION["user_key"])) { ?>
	  <div class='col-xs-6'><p><a href='javascript:create_thread()' class='btn btn-success'>Create New Thread</a></p></div>
	<?php } ?>
</div><!-- /#page_controls.row -->


<div id='list_of_threads_div' class='table-responsive' style='display:none'>
</div><!-- /#list_of_threads_div.table-responsive -->


<div id='thread_div' class='well'>
  <h2 id='thread_name_h2'>Click a thread to show messages.</h2>
  <div id='thread_messages_div'></div>
</div><!-- /#thread_div.well -->


<script>

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

	function hyperlink_row(){
		$("tr").click( function() {
			disable_create_thread();
			var row = $(this);
			var thread_id = row.find("message_data").attr("thread_id");
			var thread_name = row.find("message_data").attr("thread_name");
			fetch_messages(thread_id,thread_name,1);
			row.addClass("bg-primary").siblings().removeClass("bg-primary");
		}).hover( function() {
			$(this).toggleClass("hover");
		});
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

	$(fetch_threads());
	
</script>

<style>
	tr.hover {
		cursor: pointer;
	}
	.message_metadata {
		float: right;
	}
</style>


<?php
if (!isset($_SESSION["user_key"])) { ?>

	<p><a href='<?php echo $login_page ?>' class='btn btn-danger'>Not Logged In</a></p>

<?php } else { ?>

<!-- post message text area -->
<div id='message_div' class='well' style='display:none'>

	<form id='message_form' method='post' role='form' onsubmit='return message_submit()'>

		<input id='message_thread_id' name='message_thread_id' type='hidden'></input>
		
		<div id='thread_name_div' class='form-group' style='display:none'>
			<label for='thread_name'>Thread Name:</label>
			<input id='thread_name' name='thread_name' type='text' class='form-control' disabled required></input>
		</div>

		<div class='form-group'>
			<label for='message_text'>Message (max 140 characters):</label>
			<textarea class='form-control' style='width:100%' maxlength='140' rows='3' id='message_text' name='message_text' required></textarea>
		</div>

		<button type='submit' class='btn btn-primary'>Submit</button>

	</form>

</div>

<!-- message editor template for cloning -->
<div id='message_editor' style='display:none'>
  <form method='post' role='form' onsubmit='return false'>
    <input name='message_id' type='hidden'></input>
    <div class='form-group'>
      <label for='message_text'>Message (max 140 characters):</label>
      <textarea class='form-control' style='width:100%' maxlength='140' rows='3' name='message_text' required></textarea>
    </div>
    <a href='javascript:void(0)' onclick='update_message_submit($(this))' class='btn btn-primary'>Submit</a>
    <a href='javascript:void(0)' onclick='show_editor($(this), true)' class='btn btn-danger'>Cancel</a>
  </form>
</div><!-- /#message_editor -->

<?php } // END if (!isset($_SESSION["user_key"])) ?>

<?php
if ( !empty($_GET["thread_id"]) && is_numeric($_GET["thread_id"]) && $_GET["thread_id"] > 0 ) {?>
<script>
$(function(){
  fetch_messages(<?php echo $_GET["thread_id"] ?>,"",1);
});
</script>
<?php }
?>

<?php require_once('_resources/footer.inc.php');?>
