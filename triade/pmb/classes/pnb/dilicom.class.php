<?php
use PhpOffice\PhpSpreadsheet\Shared\Date;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dilicom.class.php,v 1.10 2023/12/14 14:03:22 jparis Exp $


require_once($class_path.'/curl.class.php');

class dilicom {
	protected $parameters;
	protected $curl_instance;
	private static $dilicom=null;
	
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
	
	public static function get_instance()
	{
	    if(!empty(self::$dilicom)){
	        return self::$dilicom;
	    }
	    self::$dilicom = new dilicom();
	    return self::$dilicom;
	}
	
	public function query($function = '', $parameters = array()){
		global $pmb_pnb_param_dilicom_url;
		$parameters = array_merge($this->parameters, $parameters);
		$payload = '{}';
		if(is_string($function) && $function != ""){
			$response = $this->curl_instance->post($pmb_pnb_param_dilicom_url.$function, $parameters);
			if(is_object($response) && isset($response->headers) && 200 == $response->headers['Status-Code']){
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
	
	public function get_loan_status($order_line_id = array(), $returnEndedLoan = 0) {
		global $pmb_pnb_param_login, $pmb_pnb_param_password;
		
		if (!is_array($order_line_id)) {
			$order_line_id = array();
		}
		
		$returnEndedLoan = intval($returnEndedLoan);
		
		$function = 'getLoanStatus';
		
		$params = array(
				'glnColl' => $pmb_pnb_param_login,
				'passwordColl' => $pmb_pnb_param_password,
				'orderLineId' => $order_line_id, 
				'returnEndedLoan' => $returnEndedLoan
		);
		
		$response = $this->query($function, $params);
		$response = encoding_normalize::json_decode($response, true);
		return $response;
	}
	
	public function returnLoan($order_line_id,$loan_id){
	    global $pmb_pnb_param_login, $pmb_pnb_param_password;

	    $function = 'returnLoan';
	    
	    $params = array(
	        'login' => $pmb_pnb_param_login,
	        'password' => $pmb_pnb_param_password,
	        'glnContractor' => $pmb_pnb_param_login ,
	        'loanId' => $loan_id,
	        'orderLineId' => $order_line_id,
	    );   

	    $response = $this->query($function, $params);
	    $response = encoding_normalize::json_decode($response, true);
	    	    
	    return dilicom::manage_response($response, "pnb_return_loan_success" ,"pnb_return_loan_fail");
	}
	
	public function extendLoan($order_line_id,$loan_id,$newLoanEndDate){
	    global $pmb_pnb_param_login, $pmb_pnb_param_password;

	    $params = array(
	        'login' => $pmb_pnb_param_login,
	        'password' => $pmb_pnb_param_password,
	        'glnContractor' => $pmb_pnb_param_login ,
	        'loanId' => $loan_id,
	        'orderLineId' => $order_line_id,
	        'date' => $newLoanEndDate
	    );
	    
	    $function = 'extendLoan';
	    $response = $this->query($function, $params);
	    $response = encoding_normalize::json_decode($response, true);
	    $date = new DateTime($newLoanEndDate);
	    $response["loanEndDate"] = $date->format("d/m/Y");
	    	    
	    return dilicom::manage_response($response, "pnb_extend_loan_success" ,"pnb_extend_loan_fail");
	}
	
	
	public static function is_pnb_active(){
	    global $pmb_pnb_param_login, $pmb_pnb_param_password, $pmb_pnb_param_dilicom_url;
	    if(!empty($pmb_pnb_param_login) && !empty($pmb_pnb_param_password) && !empty($pmb_pnb_param_dilicom_url)){
	        return true;
	    }
	    return false;
	}
	
	public static function manage_response($response, $successMsg = "" , $errorMsg = ""){
	    global $msg;
	    
	    if(!empty($response) && !empty($response['returnStatus'])){
	        switch($response['returnStatus']) {
	            case 'OK' :
	                return array("status" => true, "message"=> $msg[$successMsg], "infos" => $response);
	                break;
	            default:
	                //Cas d'erreur générique
	                return array("status" => false, "message" => $msg[$errorMsg], 'infos' => $response);
	                break;
	        }
	    } else {
	        //Message de problème d'accès au WS
	        return array("status" => false, "message" => $msg["pnb_ws_fail"], 'infos' => []);
	    }
	}
}