<script type="text/javascript" language="javascript">
$.ajax({
  url: "/trinfo/tinfo/",
  cache: false,
  success: function(html){
  	$("#load1").fadeOut("normal", function(){
	    $(".ajcontainer").html(html);
	    $(".ajcontainer").fadeIn("normal");
	});
  }
});
</script>
<div class='loadingbar' id='load1' > Loading... <img src="<?= base_url(); ?>css/loading.gif" /> </div>
<div class='ajcontainer'>
</div>
