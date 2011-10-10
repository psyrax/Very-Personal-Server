<?php

class Dwnldr extends CI_Controller {
      
  public function __construct()
       {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        //if ($this->session->userdata('logined')):
          $this->load->helper('number');
          $logined = $this->session->userdata('logined');
          $this->load->library('mobilecheck');
          $this->load->model('grabber_model','', TRUE);
          $this->load->model('user_model','', TRUE);
        /*else:
          redirect(base_url(), 'refresh');
        endif;*/
       }
	
  function index() {
    //Basic page and controller info :D
    $mbcheck=$this->mobilecheck->check();
    $data['dcss']=$mbcheck['dcss'];
    $data['style']='grabbers';
    $data['ptitle']='URL Grabber';
    $this->load->helper('form');
   
   //Base URL Form 
    $data['formop']=form_open('dwnldr/grab_urlz');
   
    $finput=array('name'=>'baseurl', 
                  'type'=> 'url',
                  'class'=>'baseurl',
                  'placeholder'=>'Base URL');
    $data['finput']=form_input($finput);

    $fsubmit=array('name'=>'fsubmit',
                   'class'=>'baseurl',
                   'value'=>'Grab URLZ');
    $data['formsu']=form_submit($fsubmit);

    $ftextar=array('name'=>'urlist',
                  'class'=>'baselinks',
                  'placeholder'=>'or link list');
    $data['ftxtarea']=form_textarea($ftextar);

    $data['formposter']=form_hidden('posted', '1');
    $data['formcl']=form_close('');
    $this->template->load('template', 'dwnldr/dwnldr_view', $data);
    }

  function grab_urlz(){
    $mbcheck=$this->mobilecheck->check();
    $data['dcss']=$mbcheck['dcss'];
    $data['style']='grabbers';
    $data['ptitle']='URL Grabber: Selecting links.'; 
    $resultado='';
    $titleclean='';
    if ($_POST['baseurl']!=''):
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$_POST['baseurl']);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $resultado.= curl_exec ($ch);
      preg_match("/<title>(.*)<\/title>/",$resultado,$title);
      $titleclean=str_replace(array('<title>', '</title>'), '', $title['0']);
    endif;
    $resultado.=$_POST['urlist'];
    $links="/http:\/\/www\.megaupload\.com\/\?d=[a-zA-Z0-9]+/";
    preg_match_all($links, $resultado, $coincidencias);
    $towork= array_unique($coincidencias['0']);
    $data['cats']=$this->grabber_model->get_cats();
    $data['findings']=$this->grabber_model->find_links($towork);
    $u_jobs='';
    if ($data['findings']['0']->link!='') :
      $id_jobs=array();
      foreach ($data['findings'] as $k):
        array_push($id_jobs, $k->job_id);
      endforeach;
      $u_jobs=array_unique($id_jobs);
      $data['id_jobs']=$u_jobs;
      foreach ($u_jobs as $K):
        $data['job_info'][$K]=$this->grabber_model->get_job($K);
        $data['job_info'][$K]['owner']=$this->user_model->get_user_id($K);
      endforeach;
    endif;
    $data['grabbedlinks']=$towork;
    $data['downtitle']=$titleclean;
  $this->template->load('template', 'dwnldr/grab_urlz_view', $data);
  }

  function doscript(){
    $mbcheck=$this->mobilecheck->check();
    $data['dcss']=$mbcheck['dcss'];
    $data['style']='grabbers';
    $data['ptitle']='URL Grabber: Script making.';
    $jobarray=array();
    $jobarray['user_id']=$this->session->userdata('user_id');
    $jobarray['type']='MU';
    $jobarray['name']=$_POST['downtitle'];
    $jobarray['cat']=$_POST['cats'];
    $data['made']=$this->grabber_model->create_job($jobarray);
    $links='';
    $link_jobs=array();
    for($i = 0; $i <= $_POST['tlinks']; $i++){
      if(isset($_POST['lcheck'.$i])):  
        $links.=$_POST['llink'.$i]."\n";
        $link_jobs[$i]['job_id']=$data['made'];
        $link_jobs[$i]['link']=$_POST['llink'.$i];
      endif;
    }
    $this->grabber_model->link_jobs($link_jobs);
    $data['links']=$links;
    $dwncln = preg_replace( array('/[^\s\w]/','/\s/'),array('','-'),$_POST['downtitle']);
    $data['downdir']=$dwncln;
    $scripttemplate="#!/bin/sh\n# Remote Script For Megaupload\n# Psyrax <psyrax@opiumgarden.org>\n";
    $data['namefile']=time();
    $data['runfile']='dwndl'.$data['namefile'];
    $data['writepath']='/misc/scripts/'.$this->session->userdata('username').'/';
    $data['runscript']=$scripttemplate."\nnohup '/mnt/MU/megaupload-dl' ".$data['namefile']." &";
    $data['ready']=TRUE; 
    file_put_contents($data['writepath'].$data['namefile'], $data['links']);  
    file_put_contents($data['writepath'].$data['runfile'], $data['runscript']);
    $this->template->load('template', 'dwnldr/doscript_view', $data);
  }
  function do_stuff(){
    $this->load->library('shell2');
    $mbcheck=$this->mobilecheck->check();
    $data['dcss']=$mbcheck['dcss'];
    $data['style']='grabbers';
    $data['ptitle']='URL Grabber: Doing stuff.';
    $user=$this->config->item('vps_user');
    $pass=$this->config->item('vps_pass');
    $host=$this->config->item('vps_host');
    $writepath=$_POST['writepath'];
    $runfile=$_POST['runfile'];
    $downdir=$this->config->item('vps_MU_dir').$this->session->userdata('username')."/".$_POST['downdir'];
    $linkfile=$_POST['linkfile'];
    $localFile=$writepath.$runfile;
    $remoteFile=$downdir."/".$runfile;
    $localFile2=$writepath.$linkfile;
    $remoteFile2=$downdir."/".$linkfile;
    

    if ( $this->shell2->login($user,$pass,$host) ) :
              
                
          //SSH Command
        $this->shell2->exec_cmd("mkdir ".$downdir.";");
        $data['comm1']=$this->shell2->get_output().$downdir." Created";
      
       
        ///uploading script
        if ($this->shell2->send_file($localFile,$remoteFile,0777)) {
          $data['f1rest']= $remoteFile." has been uploaded";
         } else {
          $data['f1rest']=$this->shell2->error;
          } ;

         //uploading file
        if ($this->shell2->send_file($localFile2,$remoteFile2,0777)) {
           $data['f2rest']= $remoteFile2." has been uploaded";
         } else {
            $data['f2rest']=$this->shell2->error;
          } ;

        $this->shell2->exec_cmd("cd ".$downdir."; /bin/sh ".$remoteFile.";");
        sleep(10);
        $this->shell2->exec_cmd("ls -l ".$downdir);
        $data['comm2']=$this->shell2->get_output();
        else :
        $data['comm2']=$this->shell2->error;
      endif;
      $data['blink']="<a href='".base_url()."dwnldr/'>Do new job!</a>";
      $this->template->load('template', 'dwnldr/dostuff_view', $data);
  }
}
?> 