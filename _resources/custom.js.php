<script>

function save_this_page(url) {
  $.post( "<?php echo $path_web_root ?>/savepage.ajax.php", { HTTP_REFERER: window.location.href } )
    .done(function(data){
      window.location = url;
    });
}

</script>
