<?php

// if filename contains ".ajax." or ".bounce.", 
//   or $exclude_html = true; 
// then don't print html header
if ( ! ((strpos(basename($_SERVER["SCRIPT_NAME"]),'.ajax.') !== false) || (strpos(basename($_SERVER["SCRIPT_NAME"]),'.bounce.') !== false) || ( !empty($exclude_html) )) ) { ?>

<!-- BEGIN FOOTER INCLUDE -->

		</div><!-- /#page-content-container -->

	    </div><!-- /#page-content-wrapper -->
	    
<section class="gray" id="section-footer" style="padding: 10px 0px 20px 0px;">
         <div class="container">
            <div class="row" style="padding-top: 30px;">
               <div class="col-sm-7">
<div id='footer-walton-name'><a href="http://walton.uark.edu">Sam M. Walton College of Business</a></div>

<p style="margin-bottom: 35px; padding: 20px;">
  <a href="//walton.uark.edu/directory/" class="btn navbar-btn btn-default" style="margin-top: -15px;">Walton Directory</a> 
  <span style="display:inline-block;"><a style="color: #b7b7b7" title=”facebook” href="https://www.facebook.com/WaltonCollege"><i class="fa fa-3x fa-facebook-square"><!-- content --></i></a>
    <a style="color: #b7b7b7" title="twitter" href="https://twitter.com/uawaltoncollege"><i class="fa fa-3x fa-twitter-square"><!-- content --></i></a>
    <a style="color: #b7b7b7" title="Linkedin" href="https://www.linkedin.com/grp/home?gid=108950"><i class="fa fa-3x fa-fa fa-linkedin-square"><!-- content --></i></a>
    <a style="color: #b7b7b7" title="instagram" href="https://instagram.com/uawaltoncollege/"><i class="fa fa-3x fa-instagram"><!-- content --></i></a>
  </span>
</p>
</div>
               <div class="col-sm-5">
                  <div class="row">
                     <div class="col-md-8 hidden-xs visible-sm visible-md visible-lg"><a href="http://walton.uark.edu"><img alt="Sam M. Walton College of Business at the University of Arkansas" src="<?php echo $path_web_root; ?>/_resources/images/WaltonLogo2.png" class="img-responsive"></a></div>
                     <div class="col-md-4 hidden-xs hidden-sm visible-md visible-lg"><a href="http://accredited.aacsb.edu/students"><img alt="AACSB Accredited" src="<?php echo $path_web_root; ?>/_resources/images/aacsb-large.png" width="120px" class="img-responsive" style="filter: alpha(opacity=30); opacity: 0.3"></a></div>
                  </div>
               </div>
            </div>
         </div>
      </section>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
	<div id="footer-logo"><a href="http://uark.edu">UNIVERSITY OF ARKANSAS</a></div>
	<ul id="footer-global-links" class="list-unstyled">
	  <li><a href="http://www.uark.edu/admissions/index.php">Admissions</a></li>
	  <li><a href="http://www.uark.edu/academics/index.php">Academics</a></li>
	  <li><a href="http://www.uark.edu/campus-life/index.php">Campus Life</a></li>
	  <li><a href="http://www.uark.edu/research/index.php">Research</a></li>
	  <li><a href="http://www.uark.edu/athletics/index.php">Athletics</a></li>
	  <li><a href="http://www.uark.edu/about/index.php">About</a></li>
	</ul>
	<ul id="social-stack" class="nav clearfix list-unstyled">
	    <li><a href="https://www.facebook.com/UofArkansas"><i class="fa fa-facebook"></i> <span class="sr-only">Like us on Facebook</span> </a></li>
	    <li><a href="http://twitter.com/uarkansas"><i class="fa fa-twitter"></i> <span class="sr-only">Follow us on Twitter</span></a></li>
	    <li><a href="http://www.youtube.com/user/UniversityArkansas"><i class="fa fa-youtube"></i> <span class="sr-only">Watch us on YouTube</span></a></li>
	    <li><a href="http://instagram.com/uarkansas"><i class="fa fa-instagram"></i> <span class="sr-only">See us on Instagram</span></a></li>
	    <li><a href="https://plus.google.com/104159281704656057709" rel="publisher"><i class="fa fa-google-plus"></i> <span class="sr-only">Connect with us on Google+</span></a></li>	
	    <li><a href="http://pinterest.com/uofaadmissions/"><i class="fa fa-pinterest"></i> <span class="sr-only">Join us on Pinterest</span></a></li>
	    <li><a href="http://www.linkedin.com/company/university-of-arkansas"><i class="fa fa-linkedin"></i> <span class="sr-only">Connect with us on LinkedIn</span></a></li>
	    <li><a href="https://foursquare.com/uarkansas"><i class="fa fa-foursquare"></i> <span class="sr-only">Find us on FourSquare</span></a></li>
	</ul>
      </div>
    </div>
  </div>
</footer><!-- /FOOTER -->




    </div><!-- /#wrapper -->


  </body>


  <?php
    if ( !empty($include_jquery_ui) ) {
      echo "
	<!-- JQUERY-UI -->
	<script src='$path_web_root/_resources/jquery-ui/jquery-ui.1.11.4.min.js'></script>
	<link rel='stylesheet' href='$path_web_root/_resources/jquery-ui/jquery-ui.1.11.4.min.css'>
	<!-- official content delivery network -->
	<!-- <script src='//code.jquery.com/ui/1.11.4/jquery-ui.min.js'></script> -->
	<!-- <link rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css'> -->

	<!-- auto-expand textarea to fit content -->
	<script>
		function auto_expand_textarea( ta ){ ta.keyup(function(e) {
			while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css('borderTopWidth')) + parseFloat($(this).css('borderBottomWidth'))) {
				$(this).height($(this).height()+1);
			};
		})}

		$(function(){
			$('textarea').each(function(){
				var ta = $(this);
				auto_expand_textarea( ta );
			});
		});
		
	</script>
      ";
    }
  ?>

  <?php
    if ( !empty($include_chartist) ) {
      echo "
	<!-- CHARTIST -->
	<script src='$path_web_root/_resources/chartist/chartist.0.9.4.min.js'></script>
	<link rel='stylesheet' href='$path_web_root/_resources/chartist/chartist.min.css'></link>
	<link rel='stylesheet' href='$path_web_root/_resources/chartist/chartist.custom.css'></link>
      ";
    }
  ?>

  <?php
    if ( !empty($include_fancybox) ) {
      echo "
	<!-- Add fancyBox -->
	<link rel='stylesheet' href='$path_web_root/_resources/fancybox/fancybox.css' type='text/css' media='screen' />
	<script type='text/javascript' src='$path_web_root/_resources/fancybox/fancybox.pack.js'></script>
	<!-- Optionally add helpers - button, thumbnail and/or media -->
	<link rel='stylesheet' href='$path_web_root/_resources/fancybox/fancybox-buttons.css' type='text/css' media='screen' />
	<script type='text/javascript' src='$path_web_root/_resources/fancybox/fancybox-buttons.js'></script>
	<script type='text/javascript' src='$path_web_root/_resources/fancybox/fancybox-media.js'></script>

	<link rel='stylesheet' href='$path_web_root/_resources/fancybox/fancybox-thumbs.css' type='text/css' media='screen' />
	<script type='text/javascript' src='$path_web_root/_resources/fancybox/fancybox-thumbs.js'></script>
      ";
    }
  ?>

<?php if ( !empty($include_tablesorter) ) { ?>
<!-- TABLESORTER -->
<script src='//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js'></script>
<link rel='stylesheet' href='//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css'>

<script>

function apply_tablesorter() {
        $('table').addClass('table table-hover table-striped table-bordered table-condensed tablesorter').DataTable({'autoWidth': false,"order": [[ 0, "desc" ]]});
}
$.fn.dataTableExt.sErrMode = "console";

$.fn.dataTableExt.oApi._fnLog = function (oSettings, iLevel, sMesg, tn) {
  var sAlert = (oSettings === null)
    ? "DataTables warning: "+sMesg
    : "DataTables warning (table id = '"+oSettings.sTableId+"'): "+sMesg
  ;

  if (tn) {
    sAlert += ". For more information about this error, please see "+
              "http://datatables.net/tn/"+tn
    ;
  }

  if (iLevel === 0) {
    if ($.fn.dataTableExt.sErrMode == "alert") {
      alert(sAlert);
    } else if ($.fn.dataTableExt.sErrMode == "thow") {
      throw sAlert;
    } else  if ($.fn.dataTableExt.sErrMode == "console") {
      console.log(sAlert);
    } else  if ($.fn.dataTableExt.sErrMode == "mute") {}

    return;
  } else if (console !== undefined && console.log) {
    console.log(sAlert);
  }
}

$(apply_tablesorter());
</script>

<style>
table.dataTable tbody tr.bg-primary {
  background-color: #337ab7;
}
table.dataTable.no-footer {
  border-bottom: 0px solid #111;
}
.table-hover > tbody > tr:hover {
  background-color: lightblue;
}
</style>

<?php }?>


</html>

<?php
if(!empty($footer_includes)){
  foreach ($footer_includes as $footer_include)
    include_once("$footer_include");
}
?>

<?php
} // END if exclude html

if (!empty($mysqli_connected)) $mysqli_connection->close();
if (!empty($mysqlo_connected)) mysql_close();

?>
