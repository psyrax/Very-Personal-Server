<?php
$this->load->helper('url');
$this->load->helper('html');
$this->load->library('session');
?>
<!DOCTYPE html>
<html>
<head>
<title><?=$ptitle?></title>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link href='http://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
<?php echo  link_tag('blueprint/screen.css'); ?>
<?php echo  link_tag('blueprint/'.$style); ?>
</head>
<body>
<div class='header'>
<span class='head1'><a href="<?= base_url() ?>"> VPS </a> </span>
<span class='head2'> <a href="<?= base_url() ?>index.php/trinfo">Torrent manager</a> | <a href="<?= base_url() ?>index.php/dwnldr">MU Downloader</a></span>
<span class='head3'> About | @<?= $this->session->userdata('username');?> <a href="<?= base_url();?>index.php/login/gtfo">GTFO!</a></span>
</div>
<div class='container'>
<?= $contents ?>
</div>
<div class='footer'>Get yours <a href="https://github.com/psyrax/Very-Personal-Server">Code</a> | info @ <a href="http://oglabs.info">OGLabs</a></div>
</body>
</html>