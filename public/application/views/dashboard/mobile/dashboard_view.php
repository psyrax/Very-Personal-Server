<script type="text/javascript" language="javascript">
$.ajax({
  url: "/dashboard/mu/",
  cache: false,
  success: function(html){
	    $(".dashpre1").html(html);
	    $(".dash1").fadeIn("normal");
  }
});
</script>
<script type="text/javascript" language="javascript">
$.ajax({
  url: "/dashboard/tr/",
  cache: false,
  success: function(html){
    	$(".dashpre2").html(html);
    	$(".dash2").fadeIn("normal");
  }
});
</script>
<h3>Files in disk</h3>
<div data-role="collapsible" data-collapsed="true">
  <h3>Megaupload Files</h3>
  <div class='dinfo dash1'>
    <pre class='dashpre1 con'></pre>
  </div>
</div>
<div data-role="collapsible" data-collapsed="true">
  <h3>Torrent Files</h3>
    <div class='dinfo dash2'>
      <pre class='dashpre2 con' ></pre>
  </div>
</div>
