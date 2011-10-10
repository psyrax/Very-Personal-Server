<?php
$this->load->helper('html');
$this->load->helper('url');
?>
<!DOCTYPE html>
<html>
<head>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script src="<?= base_url()?>js/jquery-blink.js"></script>
 <script type="text/javascript" language="javascript">
	$(document).ready(function(){
	 $('.cursor').blink()
	});
 </script>

<title>VPS: End of line</title>
<link href='http://fonts.googleapis.com/css?family=Geo' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<?php echo  link_tag('css/endofline.css'); ?>
</head>
<body>
<div class='msg'>
<?php if (isset($msg)): ?>
Error Message: <?= $msg ?>
<?php endif; ?>
</div>
<div class='end'>End of line<span class='cursor'>_</span></div>
</body>
</html>