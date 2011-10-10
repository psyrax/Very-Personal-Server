<?php
$this->load->helper('url');
$this->load->helper('html');
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=$ptitle?></title>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="width=768 initial-scale=1 maximum-scale=1.0; user-scalable=0;" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="default" />
		<link rel="apple-touch-icon" href="<?= site_url('itouchicon.png'); ?>" />
		<link rel="apple-touch-icon" sizes="72x72" href="<?= site_url('ipadicon.png'); ?>" />
		<link rel="apple-touch-icon" sizes="114x114" href="<?= site_url('iphone4icon.png'); ?>" />
		<link rel="apple-touch-startup-image" sizes="1004x768" href="<?= site_url('splashland.png'); ?>" />
		<link rel="apple-touch-startup-image" sizes="768x1004" href="<?= site_url('splashportrait.png'); ?>" />
		<?php echo  link_tag('css/screen.css'); ?>
		<?php echo  link_tag('css/template/'.$dcss.'.css'); ?>
		<?php echo  link_tag('css/'.$style.'/'.$dcss.'.css'); ?>
	</head>
	<body>
		<div class='header'>
			VPS TAB
			<div class='header_inner'>
				<div class="head1">
					Tools <hr />
					<ul>
						<li><a href="<?= base_url(); ?>"> Dashboard </a></li>
						<li><a href="<?= base_url(); ?>trinfo">Torrent manager</a></li>
						<li><a href="<?= base_url(); ?>dwnldr">MU Downloader</a></li>
					</ul>
				</div>
				<div class="head2">
					User info<hr />
					<ul>
						<li>@<?= $this->session->userdata('username');?> </li>
						<li><a href="<?= base_url();?>login/gtfo">GTFO!</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class='container'>
			<?= $contents ?>
		</div>
		<div class='footer'>
			Get yours <a href="https://github.com/psyrax/Very-Personal-Server">Code</a> | info @ <a href="http://oglabs.info">OGLabs</a> | <a href="<?= base_url();?>login/gtfo">GTFO!</a>
		</div>
	</body>
</html>