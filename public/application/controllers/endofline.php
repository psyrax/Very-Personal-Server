<?php
class Endofline extends CI_Controller {

      
       public function __construct()
       {
            parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('encrypt');
        $this->load->model('user_model','', TRUE);
       }
	

	function index(){
    $this->load->view('endofline_view');
    }
 }
 ?>