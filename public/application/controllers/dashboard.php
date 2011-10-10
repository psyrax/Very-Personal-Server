<?php

class Dashboard extends CI_Controller {
      
  public function __construct()
       {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        //if ($this->session->userdata('logined')):
          $this->load->helper('number');
          $this->load->library('shell2');
          $this->load->library('mobilecheck');
        /*else:
          redirect(base_url(), 'refresh');
        endif;*/
       }
	
  function index() {
    //Basic page and controller info :D
    $mbcheck=$this->mobilecheck->check();
    $data['dcss']=$mbcheck['dcss'];
    $data['style']='dashboard';
    $data['ptitle']='VPS Dashboard';
    //loading template
    if($mbcheck['is_mobile']&&$mbcheck['navi']!="iPad"):
      $this->template->load('template_mobile', 'dashboard/mobile/dashboard_view', $data);
    elseif($mbcheck['navi']=="iPad"):
      $this->load->view('dashboard/ipad/dashboard_view', $data);
    else:
      $this->template->load('template', 'dashboard/dashboard_view', $data);
    endif;
    }
  function mu(){
    $user=$this->config->item('vps_user');
    $pass=$this->config->item('vps_pass');
    $host=$this->config->item('vps_host');
      if ( @$this->shell2->login($user,$pass,$host) ) :
        $this->shell2->exec_cmd("du -a -c -h --max-depth 2 ".$this->config->item('vps_MU_dir'));
        $data['dumu']=$this->shell2->get_output();
      else :
        $data['dumu']=$this->shell2->error;
      endif;
      $this->load->view('dashboard/mu_view', $data);
    }
  function tr(){
    $user=$this->config->item('vps_user');
    $pass=$this->config->item('vps_pass');
    $host=$this->config->item('vps_host');
      if ( @$this->shell2->login($user,$pass,$host) ) :
      $this->shell2->exec_cmd("du -a -c -h --max-depth 1 ".$this->config->item('vps_torrent_dir'));
        $data['dumu']=$this->shell2->get_output();
      else :
        $data['dumu']=$this->shell2->error;
      endif;
      $this->load->view('dashboard/mu_view', $data);
    }
}
/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */
