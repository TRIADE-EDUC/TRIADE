<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dilicom.class.php,v 1.7 2023/04/26 10:15:10 dbellamy Exp $

require_once($class_path.'/curl.class.php');

class dilicom {
	protected $parameters;
	protected $curl_instance;
	
	public function __construct(){
		global $pmb_pnb_param_login, $pmb_pnb_param_password;
		$this->curl_instance = new Curl();
		$this->curl_instance->set_option('CURLOPT_SSL_VERIFYPEER', false);
		$this->curl_instance->set_option('CURLOPT_HTTPAUTH', CURLAUTH_BASIC);
		$this->curl_instance->set_option('CURLOPT_USERPWD', $pmb_pnb_param_login.':'.$pmb_pnb_param_password);
		$this->curl_instance->set_option('CURLOPT_HTTPHEADER', array('Content-Type:application/json'));
		$this->curl_instance->timeout = 10;
		$this->init_parameters();
	}
	
	public function query($function = '', $parameters = array()){
		global $pmb_pnb_param_dilicom_url;
		$parameters = array_merge($this->parameters, $parameters);
		$payload = '{}';
		if(is_string($function) && $function != ""){
			$response = $this->curl_instance->post($pmb_pnb_param_dilicom_url.$function, $parameters);
			if( 200 == $response->headers['Status-Code']){
			    $payload = $response->body;
			}
		}
		return $payload;
	}
	
	protected function init_parameters() {
		global $pmb_pnb_param_login;
		$this->parameters = array(
				'glnContractor' => $pmb_pnb_param_login
		);
	}
	
	public static function is_pnb_active(){
	    global $pmb_pnb_param_login, $pmb_pnb_param_password, $pmb_pnb_param_dilicom_url;
	    if(!empty($pmb_pnb_param_login) && !empty($pmb_pnb_param_password) && !empty($pmb_pnb_param_dilicom_url)){
	        return true;
	    }
	    return false;
	}
}