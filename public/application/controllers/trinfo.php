<?php

class Trinfo extends CI_Controller {
      
       public function __construct()
       {
            parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        //if ($this->session->userdata('logined')):
            $this->load->library('shell2');
            $this->load->library('TransmissionRPC');
            $this->transmissionrpc->return_as_array = true;
            $this->transmissionrpc->url =$this->config->item('vps_torrent_url');
            $this->load->helper('number');
            $this->load->library('mobilecheck');
        /*else:
          redirect(base_url(), 'refresh');
        endif;*/
       }
	
    function index() { 
        $mbcheck=$this->mobilecheck->check();
        $data['dcss']=$mbcheck['dcss'];
        $data['style']="torrents";
		$data['ptitle']='Torrent Manager';
		//$this->load->view('trinfo_view', $data);
    if($mbcheck['is_mobile']&&$mbcheck['navi']!="iPad"):
      $this->template->load('template_mobile', 'trinfo/mobile/tinit_view', $data);
    elseif($mbcheck['navi']=="iPad"):
      $this->load->view('dashboard/ipad/dashboard_view', $data);
    else:
      $this->template->load('template', 'trinfo/tinit_view', $data);
    endif;
    }
    function tinfo(){
        $mbcheck=$this->mobilecheck->check();
        $listaar=$this->transmissionrpc->get();
        $statsar=$this->transmissionrpc->stats();
        $sinfo=$this->transmissionrpc->sinfo(array( 'alt-speed-down','alt-speed-up'));
        $data['sinfo']=$sinfo;
        $data['torrents']=$listaar['arguments']['torrents'];
        $data['stats']=$statsar['arguments'];
        $data['techo']=byte_format($data['stats']['cumulative-stats']['downloadedBytes']);
        $this->load->helper('form');
        $data['formop']=form_open('trinfo/tadd');
        $finput=array('name'=>'torrenturl', 
                    'class'=>'urlplz',
                        'placeholder'=>'urlplz');
        $fsubmit=array('name'=>'urladd',
                        'class'=>'urladd',
                        'value'=>' ');
        $data['formin']=form_input($finput);
        $data['formsu']=form_submit($fsubmit);
    if($mbcheck['is_mobile']&&$mbcheck['navi']!="iPad"):
              $this->load->view('trinfo/mobile/trinfo_view', $data);
    elseif($mbcheck['navi']=="iPad"):
              $this->load->view('trinfo/ipad/trinfo_view', $data);
    else:
        $this->load->view('trinfo/trinfo_view', $data);
    endif;
    }
    function tadd(){
    	$url=$_POST['torrenturl'];
        $result = $this->transmissionrpc->add( $url, $this->config->item('vps_torrent_dir'));
       redirect('/trinfo', 'location', 301);
    }
    function tstop(){
    	$id = strrchr(uri_string(), '/');
    	$id = str_replace('/', '', $id);
    	$id = (int)$id;
    	$result = $this->transmissionrpc->stop( $id );
       redirect('/trinfo', 'location', 301);
    }
    function tstart(){
    	$id = strrchr(uri_string(), '/');
    	$id = str_replace('/', '', $id);
    	$id = (int)$id;
    	$result = $this->transmissionrpc->start( $id );
       redirect('/trinfo', 'location', 301);
    }
    function tremove(){
    	$id = strrchr(uri_string(), '/');
    	$id = str_replace('/', '', $id);
    	$id = (int)$id;
    	$result = $this->transmissionrpc->remove( $id, false );
       redirect('/trinfo', 'location', 301);
    }
    function tdestroy(){
    	$id = strrchr(uri_string(), '/');
    	$id = str_replace('/', '', $id);
    	$id = (int)$id;
    	$result = $this->transmissionrpc->remove( $id, true );
       redirect('/trinfo', 'location', 301);
    }
    function tspeed(){
    $speedchanger = strrchr(uri_string(), '/');
    $speedchanger = str_replace('/', '', $speedchanger);
    	

    $user=$this->config->item('vps_user');
    $pass=$this->config->item('vps_pass');
    $host=$this->config->item('vps_host');
    //Getting Dir Structure
    if ( $this->shell2->login($user,$pass,$host) ) :
      $this->shell2->exec_cmd($this->config->item('vps_torrent_script')." ".$speedchanger);
      
      $data['speeder']=$this->shell2->get_output();

      else :
      $data['speeder']=$this->shell2->error;
      
      endif;
      redirect('/trinfo', 'location', 301);

    }
}
?>
