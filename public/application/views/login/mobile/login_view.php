<?php
$this->load->helper('url');
$this->load->helper('html');
$this->load->library('session');
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=$ptitle?> Mobile Edition</title>
		<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Covered+By+Your+Grace' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.js"></script>
	</head>
	<body>
	<div data-role="page"> 
		<div data-role="header">VPS Mobile Login!</div> 
		<div data-role="content">			
			<div class='lform'>
				<?=$formop.'<br />'.$formin.'<br />'.$formps.'<br />'.$formsu; ?></form>
			</div>
		</div> 
		<div data-role="footer">Footer</div> 
	</div> 
			
			<?php //print_r($mbcheck); ?>
	</body>
</html>