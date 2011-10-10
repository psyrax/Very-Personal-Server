<?php
class Mobilecheck {
	function check(){
			$CI =& get_instance();
			$CI->load->library('user_agent');
			$data['is_mobile']=$CI->agent->is_mobile();
	       	if($CI->agent->is_mobile()) :
				$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
				if( $isiPad == TRUE) :
					$data['navi']=$CI->agent->mobile();
					$data['dcss']='ipad';
					$data['string']=$CI->agent->agent_string();
				else:
					$data['navi']=$CI->agent->mobile();
					$data['dcss']='mobile';
					$data['string']=$CI->agent->agent_string();
				endif;
			else: 
				$data['dcss']='screen';
				$data['navi']='machine';
			endif;
			return $data;
	}
}

?>