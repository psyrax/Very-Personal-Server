<?php
$this->load->helper('url');
$this->load->helper('html');
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=$ptitle?></title>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.js"></script>
	</head>
	<body>
	<div data-role="page"> 
		<div data-role="header">
			VPS Mobile
		</div> 
		<div data-role="content">	
			<div data-role="navbar" class="ui-bar">
				<ul>
						<li><a href="<?= base_url(); ?>trinfo/tinfo"  data-icon="gear" data-prefetch>Torrent manager</a></li>
						<li><a href="<?= base_url(); ?>dwnldr" data-icon="add">MU Downloader</a></li>
				</ul>
			</div>	
			<?= $contents ?>
		</div>  
		<div data-role="footer" class="ui-bar">
			<div data-role="controlgroup" data-type="horizontal">			
				<a href="https://github.com/psyrax/Very-Personal-Server" data-role="button" data-role="button" data-role="button" data-icon="star">Code</a><a href="http://oglabs.info" data-role="button" data-role="button" data-icon="info">OGLabs</a><a href="<?= base_url();?>login/gtfo"data-role="button"  data-icon="remove">GTFO!</a>
			</div>
		</div> 
	</div> 

	</body>
</html>