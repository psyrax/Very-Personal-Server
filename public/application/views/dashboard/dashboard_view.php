<script type="text/javascript" language="javascript">
$.ajax({
  url: "/dashboard/mu/",
  cache: false,
  success: function(html){
  	$("#load1").fadeOut("normal", function(){
	    $(".dashpre1").html(html);
	    $(".dash1").fadeIn("normal");
	});
  }
});
</script>
<script type="text/javascript" language="javascript">
$.ajax({
  url: "/dashboard/tr/",
  cache: false,
  success: function(html){
  	$("#load2").fadeOut("normal", function(){
    	$(".dashpre2").html(html);
    	$(".dash2").fadeIn("normal");
	});
  }
});
</script>
<h1>INFO</h1>
<div class='heading'>Megaupload Files</div>
<hr />
<div class='loadingbar' id='load1' >Loading... <img src="<?= base_url(); ?>css/loading.gif" /></div>
<div class='dinfo dash1'>
<pre class='dashpre1 con'></pre>
</div>
<div class='heading'>Torrent Files</div>
<hr />
<div class='loadingbar' id='load2' >Loading... <img src="<?= base_url(); ?>css/loading.gif" /></div>
<div class='dinfo dash2'>
<pre class='dashpre2 con' ></pre>
</div>
