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
			<div data-role="header" class="header">
				Torrent Manager
			</div>
			<div data-role="content">

<?php
$this->load->helper('url');
$this->load->helper('number');
/** Torrent Information
*
*/
function s2dhs($s){
		$d = intval($s/86400);
		$s -= $d*86400;

		$h = intval($s/3600);
		$s -= $h*3600;

		$m = intval($s/60);
		$s -= $m*60;

		if ($d) $str = $d . 'd ';
		if ($h) $str .= $h . 'h ';
		if ($m) $str .= $m . 'm ';
		if ($s) $str .= $s . 's';
		return $str;
	}
$rtime=s2dhs($stats['cumulative-stats']['secondsActive']);

$colorvar=0;
arsort($torrents);
?>

<div class='tlist'>
<?php 
foreach ($torrents as $tinfo):
		$ttotal=byte_format($tinfo['sizeWhenDone']);
		$tgot=byte_format($tinfo['haveValid']);
		$adate=$tinfo['addedDate'];
		$ddate='';
		$ptitime=$tinfo['name'];
		//if (strlen($ptitime)>45) $ptitime=substr($tinfo['name'],0,45)." [...]";
		//if ($tinfo['desiredAvailable']>0)
		//$ddate=strftime("%w %B %G", $tinfo['desiredAvailable']);
		$tinfodown=byte_format($tinfo['rateDownload']);
		$tinfoup=byte_format($tinfo['rateUpload']);
		$tleft=byte_format($tinfo['leftUntilDone']);
		$scurl=FALSE;
		switch($tinfo['status']):
		case 1:
		$status='Checking';
		break;	
		case 2:
		$status='Await Check';
		break;
		case 4:
		$status='Downloading';
		$scurl=site_url('trinfo/tstop').'/'.$tinfo['id'];
		$scname='<img src=\''.base_url().'css/torrents/stop.png\' border=\'0\'>';
		break;
		case 8:
		$status='Seeding';
		$scurl=site_url('trinfo/tstop').'/'.$tinfo['id'];
		$scname='<img src=\''.base_url().'css/torrents/stop.png\' border=\'0\'>';
		break;
		case 16:
		$status='Stopped';
		$scurl=site_url('trinfo/tstart').'/'.$tinfo['id'];
		$scname='<img src=\''.base_url().'css/torrents/play.png\' border=\'0\'>';
		break;
		default:
		$status='It\'s a mistery';
		endswitch;
		$tremove=site_url('trinfo/tremove').'/'.$tinfo['id'];
		$tdestroy=site_url('trinfo/tdestroy').'/'.$tinfo['id'];
		if ($colorvar==0)$colorvar=1;
		switch ($colorvar):
		case 1:
		$scotch=base_url().'css/torrents/gback.png';
		break;
		case 2:
		$scotch=base_url().'css/torrents/oback.png';
		break;
		case 3:
		$scotch=base_url().'css/torrents/pback.png';
		break;
		case 4:
		$scotch=base_url().'css/torrents/bback.png';
		break;
		endswitch;
?>


<div class='tcontainer'>
<div class='titem var<?= $colorvar?>'>
	<div class="titemt"><?= $ptitime ?></div>
	
	Status: <span class="tval"><?= $status ?></span><br />
	<span class="tval"><?= $tinfo['percentDone']*100?>%&nbsp;(<?= $tgot ?>/<?= $ttotal ?>) </span><br />
	Left: <span class="tval"><?= $tleft ?> </span><br />
	D: <span class="tval"><?= $tinfodown ?></span>
	U: <span class="tval"><?= $tinfoup ?></span>
	<br />
	Added: <span class="tval"><?= strftime("%w %B %G", $adate)?> <?= $ddate ?></span>
</div>
	<div class='tplay'>
	<?php if ($scurl!=FALSE):?>
	<a href='<?= $scurl ?>'><?= $scname ?></a>
	<?php endif ?>
	<a href='<?= $tremove ?>'><img src="<?= base_url()?>css/torrents/remove.png" border="0"></a>&nbsp;<a href='<?= $tdestroy ?>'><img src="<?= base_url()?>css/torrents/delete.png" border="0"></a>
	</div>
</div>
<hr />
<?php 
endforeach;
	$tdown=byte_format($stats['cumulative-stats']['downloadedBytes']);
	$tup=byte_format($stats['cumulative-stats']['uploadedBytes']);
	$cdown=byte_format($stats['downloadSpeed']);
	$cup=byte_format($stats['uploadSpeed']);
	?>
</div>
<div class='tcontrol'>
 <div class ='tinfog'>INFO</div>
	<div class='tinfoc'>
	Down: <span class='tcspan'><?= $cdown ?>/s </span> | 
	Up: <span class='tcspan'><?= $cup ?>/s<br /></span>
	Total Download:  <span class='tcspan'><?= $techo ?> <br /></span>
	Total Upload: <span class='tcspan'><?= $tup ?><br /></span>
	Up Time: <span class='tcspan'><?= $rtime ;?><br /></span>
	<div class="tmotion">
	<a href='<?= site_url('trinfo/tspeed/slow')?>'><img src="<?= base_url()?>css/torrents/slow.png" border="0" /></a> <a href='<?= site_url('trinfo/tspeed/speed')?>'><img src="<?= base_url()?>css/torrents/speed.png" border="0" /></a>
	</div>
	</div>
<div class='tinfod'>
	ADD
	</div>
	<div class='tinfoc'>
	GET SOME MORE TORRENTS
	<div class='addform'>
	<?php
	/** Torrent Add
	*
	*/
	echo $formop.$formin.$formsu;
	?>
	</form>
	</div>
	</div>
	<div class="tinfof">
	Usage: <hr />
	<img src="<?= base_url()?>css/torrents/stop.png" /> Stop Downloading Torrent<br />
	<img src="<?= base_url()?>css/torrents/play.png" /> Resume Torrent Downloading<br />
	<img src="<?= base_url()?>css/torrents/remove.png" /> Delete Torrent<br />
	<img src="<?= base_url()?>css/torrents/delete.png" /> Delete Torrent and Files <br />
	<img src="<?= base_url()?>css/torrents/slow.png" /> Enable Speed Limit<br />
	<img src="<?= base_url()?>css/torrents/speed.png" /> Disable Speed Limit<br />
	<br />
	Credits:<hr />
	Drop Box Icon by <a class='tinfofl' target='_blank' href='http://2Shi.deviantart.com/'>*2Shi</a> @deviantart<br />
	Icons Supratim Nayak / <a class='tinfofl' target='_blank' href='http://iconebula.com'>iconebula.com</a><br />
	<a href="http://ipapun.deviantart.com/art/Devine-Icons-137555756?q=boost%3Apopular%20in%3Acustomization%2Ficons%2Fdock&qo=44">Devine Icons</a><br />
	Interface by <a class='tinfofl' target='_blank' href='http://funkymonstruoslab.net'>funkymonstruoslab.net</a>
	</div>
</div>
	</div>
	<div data-role="footer" class="ui-bar">
			<div data-role="controlgroup" data-type="horizontal">			
				<a href="https://github.com/psyrax/Very-Personal-Server" data-role="button" data-role="button" data-role="button" data-icon="star">Code</a><a href="http://oglabs.info" data-role="button" data-role="button" data-icon="info">OGLabs</a><a href="<?= base_url();?>login/gtfo"data-role="button"  data-icon="remove">GTFO!</a>
			</div>
	</body>
</html>