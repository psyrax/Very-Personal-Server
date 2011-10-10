<?php
$this->load->helper('url');
$this->load->helper('html');
$this->load->library('session');
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?=$ptitle?></title>
		<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Covered+By+Your+Grace' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="width=768 initial-scale=1 maximum-scale=1.0; user-scalable=0;" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="default" />
		<link rel="apple-touch-icon" href="<?= site_url('itouchicon.png'); ?>" />
		<link rel="apple-touch-icon" sizes="72x72" href="<?= site_url('ipadicon.png'); ?>" />
		<link rel="apple-touch-icon" sizes="114x114" href="<?= site_url('iphone4icon.png'); ?>" />
		<link rel="apple-touch-startup-image" sizes="1004x768" href="<?= site_url('splashland.png'); ?>" />
		<link rel="apple-touch-startup-image" sizes="768x1004" href="<?= site_url('splashportrait.png'); ?>" />
		<?php echo  link_tag('css/less.css'); ?>
		<?php echo  link_tag('css/login/'.$dcss.'.css'); ?>
	</head>
	<body>
		<div class='dline'>
			VPS Login OMG!<br />
			<?php print_r($mbcheck); ?>
			<div class='lform'>
				<?=$formop.$formin.$formps.$formsu; ?></form>
			</div>
		</div>
	</body>
</html>
