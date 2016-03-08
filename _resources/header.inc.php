<?php

require_once((__DIR__)."/resources.inc.php");

sec_session_start();

// if filename contains ".ajax." or ".bounce.", 
//   or $exclude_html = true; 
// then don't print html header
if ( ! ((strpos(basename($_SERVER["SCRIPT_NAME"]),'.ajax.') !== false) || (strpos(basename($_SERVER["SCRIPT_NAME"]),'.bounce.') !== false) || ( !empty($exclude_html) )) ) { ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <title>
    <?php
      if( !empty($page_title) )
        echo $page_title . " | ";
      if( !empty($section_title) )
        echo $section_title . " | ";
      echo $site_title;
    ?>
  </title>
  
  <?php echo "
  
    <!-- favicon -->
    <link rel='icon' href='$path_web_root/favicon.ico'></link>
  
    <!-- JQUERY -->
    <script src='$path_web_root/_resources/jquery/jquery.1.11.2.min.js'></script>

    <!-- BOOTSTRAP -->
    <script src='$path_web_root/_resources/bootstrap/bootstrap.3.3.4.min.js'></script>
    <link rel='stylesheet' href='$path_web_root/_resources/bootstrap/bootstrap.3.3.4.min.css'></link>
    <link rel='stylesheet' href='$path_web_root/_resources/bootstrap/bootstrap.custom.css'></link>
    <link rel='stylesheet' href='$path_web_root/_resources/bootstrap/sidenav.css'></link>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'></script>
      <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
    <![endif]-->
    
    <!-- FONT AWESOME
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'></link> -->
    <link rel='stylesheet' href='$path_web_root/_resources/font-awesome-4.5.0/css/font-awesome.min.css'></link>
    
    <!-- UARK -->
    <link rel='stylesheet' href='$path_web_root/_resources/bootstrap/uark.css'></link>

  ";?>

  <?php
    if ( !empty($include_chartist) ) {
      echo "
	<!-- CHARTIST -->
	<script src='$path_web_root/_resources/chartist/chartist.0.9.4.min.js'></script>
	<script src='$path_web_root/_resources/chartist/chartist-plugin-tooltip.js'></script>
	<script src='$path_web_root/_resources/chartist/chartist-plugin-axistitle.min.js'></script>
	<script src='$path_web_root/_resources/chartist/chartist.custom.js'></script>
	<link rel='stylesheet' href='$path_web_root/_resources/chartist/chartist.min.css'></link>
	<link rel='stylesheet' href='$path_web_root/_resources/chartist/chartist.custom.css'></link>
      ";
    }
  ?>

</head>
<body>
<div id="header-bg"></div>
<div class="container" id="opener" role="banner">
  <a class="brand" href="http://www.uark.edu/">University of Arkansas</a>
  <h1 id="site-heading" style="position: absolute; top: 27px; margin-left: 220px;"><span class="walton-name"><a href="<?php echo $path_web_root;?>/"><?php echo $site_title;?></a></span></h1>
  <h4>&nbsp;</h4>
  <p id="site-heading" style="position: absolute; top: 85px; margin-left: 220px; font-size: 20px;"><span class="walton-sub-name"><a href="//walton.uark.edu">The Sam M. Walton College of Business</a></span></p>
</div><!-- /#opener banner -->

    <nav class="navbar navbar-default">

      <div class="container">

        <div class="navbar-header">

          <!-- Top Nav Toggle -->
          <button id='n' type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

	  <!-- Side Nav Toggle
          <a id="sidenav-toggle" href="javascript:void(0);" onclick="$('#wrapper').toggleClass('toggled')">
	    <img src="<?php echo $path_web_root;?>/_resources/images/favicon.ico"></img>
          </a>
          -->

          <?php // #site_title_brand
	    echo "<a id='site_title_brand' class='navbar-brand' href='$path_web_root/'>$site_title</a>"; 
          ?>

       </div><!-- /.navbar-header -->

	<div id="navbar" class="collapse navbar-collapse">

	    <ul id='top_navigation_menu' class='top_nav nav navbar-nav navigation-menu'>

		<?php

		require_once("$path_real_root/_resources/navigation-menu.php");

		/*
		// get current section if not web root
		if(dirname($_SERVER["SCRIPT_FILENAME"]) != $path_real_root){
		    echo "<li><a class='li_section_title page_link' href='".dirname($_SERVER["SCRIPT_NAME"])."/'>$section_title</a></li>";
		}

		// recurse bottom-up the chain until first node with navigation menu
		$path_relative_section = str_replace($path_web_root, '', dirname($_SERVER["SCRIPT_NAME"]));
		// TODO: create rendered relative path for menu links that can be copied from html source
		// will need "../" substitutions for parent node menus

		do {
		  if(file_exists("$path_real_root$path_relative_section/_resources/navigation-menu.php")){
		    $path_rendered_relative = $path_web_root . $path_relative_section;
		    include("$path_real_root$path_relative_section/_resources/navigation-menu.php");
		    break;
		  }
		  $path_relative_section = dirname($path_relative_section);
		  if($path_relative_section == "/") $path_relative_section = "";
		} while (true);
		*/

		?>

	    </ul>
	    
	    <div id='login_nav_div' class='top_nav pull-right-md pull-left-xs'>
		<ul class="nav navbar-nav navigation-menu">
		  <?php
		    if ( $show_admin_nav_menu || !empty($_SESSION["user_groups"]["ADMIN"]) )
		      echo "<li><a href='$path_web_root/admin/'>Admin</a></li>";
		  ?>
		  <?php
		    if (isset($_SESSION["username"])) {
		      echo "<li id='my_profile'><a href='$path_web_root/Profiles/?user_key=$_SESSION[user_key]'>$_SESSION[username]</a></li>";
		      echo "<li id='logout'><a href='?logout'>Logout</a></li>";
		    }
		    else
		      echo "<li id='login'><a href='$login_page'>Login</a></li>";
		  ?>
	      
		</ul>
	   </div>

	</div><!--/#navbar.nav-collapse -->

      </div><!--/.container -->

    </nav>


	<div id="wrapper">

        <!-- Sidebar 
        <div id="sidebar-wrapper">
            <ul id='current_navigation_menu' class="sidebar-nav navigation-menu">
                <?php //include("$path_real_root/SiteMap/navigation-menu.inc.php"); ?>
            </ul>
            <script>

	      function toggle_nav_item(toggle){
		toggle.find(".glyphicon").toggleClass("glyphicon-plus-sign glyphicon-minus-sign");
		toggle.parent().children("ul").toggle("blind");
	      }

	      /*
	      $("#current_navigation_menu").find("a").each(function(){
		    if ( $(this).attr("href") == "<?php echo $_SERVER['SCRIPT_NAME'];?>" || $(this).attr("href") == "<?php echo dirname($_SERVER['SCRIPT_NAME'])."/";?>" )
		      $(this).parents("ul").show();
	      });
	      */
	      // add active class only to page in sidebar
	      var on_index_page = true;
	      $("#current_navigation_menu").find('a').each(function(){
		if ( $(this).attr("href") == "<?php echo $_SERVER['SCRIPT_NAME'];?>" ){
		  on_index_page = false;
		  //$(this).parents().find(".glyphicon").removeClass("glyphicon-plus-sign").addClass("glyphicon-minus-sign");
		  //$(this).parents("ul").show();
		  $(this).parent().addClass("active");
		}
	      });
	      if (on_index_page) {
		$("#current_navigation_menu").find('a').each(function(){
		  if ( $(this).attr("href") == "<?php echo dirname($_SERVER['SCRIPT_NAME'])."/";?>" )
		    $(this).parent().addClass("active");
		});
	      }
	    </script>
        </div><!-- /#sidebar-wrapper -->
<script>
  $(".top_nav").find("a").each(function(){
    if ( $(this).attr("href") == "<?php echo $_SERVER['SCRIPT_NAME'];?>" || $(this).attr("href") == "<?php echo dirname($_SERVER['SCRIPT_NAME'])."/";?>" )
      $(this).parent().addClass("active");
  });
</script>

        <!-- Page Content -->
        <div id="page-content-wrapper">


		<div id="page-content-container" class='<?php if(!isset($full_width_container)) echo "container "; ?><?php if(!isset($no_well_container)) echo " well' style='background-color:white;"; ?>'>
		  
<!-- BEGIN BODY CONTENT -->
<?php
} // END if exclude html
?>
