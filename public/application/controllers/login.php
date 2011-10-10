<?php
class Login extends CI_Controller {

      
       public function __construct()
       {
            parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('encrypt');
        $this->load->model('user_model','', TRUE);
        $this->load->library('mobilecheck');
       }
	

	function index(){
	    $logined = $this->session->userdata('logined');
        if ($logined==TRUE):
        redirect(base_url().'dashboard', 'refresh');
        endif;
        $mbcheck=$this->mobilecheck->check();
        $data['mbcheck']=$mbcheck;
        $data['dcss']=$mbcheck['dcss'];
		$this->load->helper('form');
		$fatt = array('data-ajax' => 'false');
		$data['formop']=form_open('login/check', $fatt);
		$finput=array('name'=>'user', 
					'class'=>'loginput',
					'placeholder'=>'Username');
		$pinput=array('name'=>'pass', 
					'class'=>'loginput',
					'placeholder'=>'Password');
		$fsubmit=array('name'=>'login',
					'class'=>'logo',
					'value'=>'Login');
		$data['formin']=form_input($finput);
		$data['formps']=form_password($pinput);
		$data['formsu']=form_submit($fsubmit);
		$data['ptitle']='VPS Login: '.base_url();

		if($mbcheck['is_mobile']&&$mbcheck['navi']!="iPad"):
			$this->load->view('login/mobile/login_view', $data);
		elseif($mbcheck['navi']=="iPad"):
			$this->load->view('login/ipad/login_view', $data);
		else:
			$this->load->view('login_view', $data);
		endif;
		}
	
	function loginer (){

	$passdir=$this->config->item('vps_pass_dir');
	$newuser= 'testing';
	$pass = 'keytest';
	$encrypted_string = $this->encrypt->encode($pass);
	echo $encrypted_string;
	
	}
	function check(){
	//echo print_r($_POST);

	if (isset($_POST['user'])&&isset($_POST['pass'])):
		$userpoint=$_POST['user'];
		$userpass=$_POST['pass'];
		if ($userpass!=''&&$userinfo=!''):
			$info=$this->user_model->get_user($userpoint);
			$passchecker=$this->encrypt->decode($info[0]->secret);
			if ($passchecker==$userpass):
				$session_info=array('logined'=>TRUE,
					'user_id'=>$info[0]->id,
					'username'=>$info[0]->user,
					'twitter'=>$info[0]->twitter);
				$this->session->set_userdata($session_info);
				redirect(base_url().'dashboard', 'refresh');
				exit();
			endif;
		endif;
	endif;
	$data['msg']='Wrong User/Password';
	$this->load->view('endofline_view', $data);
	}
	function gtfo(){
	$this->session->sess_destroy();
	redirect(base_url(), 'refresh');
	}
	}

/* End of file login.php */
/* Location: ./application/controllers/login.php */