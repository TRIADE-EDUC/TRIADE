<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sms.class.php,v 1.8 2019-05-28 15:08:22 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition des classes d'envoi de sms selon opérateur


class sms_factory {

	public static function make() {
		
		global $empr_sms_config;
		$param_list=array();
		$tab_params=explode(';',$empr_sms_config);	  
		if(is_array($tab_params)) {
			foreach($tab_params as $param){
				$p=explode('=',$param);	
				if(is_array($p)) $param_list[$p[0]]=$p[1];
			}
		}
		if (!$param_list['class_name']) return false;
		$obj = new $param_list['class_name']($param_list);
		return $obj;
	}
} 


class smstrend {
	
	private $login='';
	private $password='';
	private $tpoa='';
	private $messageQty='GOLD';
	private $messageType='PLUS';
	
	function __construct ($param_list) {		
		$this->login=$param_list["login"];
		$this->password=$param_list["password"];
		$this->tpoa=$param_list["tpoa"];
		if ($param_list["messageQty"]) {
			$this->messageQty=$param_list["messageQty"];
		}
		if ($param_list["messageType"]) {
			$this->messageType=$param_list["messageType"];
		}
	}
	
	function send_sms($telephone, $message) {
		global $charset;
		$telephone = preg_replace("/.[^0-9]/", "", $telephone); 
		$telephone = preg_replace("/^[\+|[^0-9]]/", "", $telephone);
		if (substr($telephone, 0, 1) == "0") {
		    $telephone = "+33" . substr($telephone, 1); 
		} else if (substr($telephone, 0, 1) != "+") {
		    return false;
		}
		$fields=array(
			"login"=>$this->login,
			"password"=>$this->password,
			"mobile"=>$telephone,
			"messageQty"=>$this->messageQty,
			"messageType"=>$this->messageType,
			"tpoa"=>$this->tpoa, //$object_message,
			"message"=>$message
		);
		if (strtoupper($charset)!="UTF-8") {			
			foreach ($fields as $key=>$val)$fields[$key]=utf8_encode($val);
		}
		foreach ($fields as $key=>$val) $post[]=$key."=".rawurlencode($val);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.smstrend.net/fra/sendMessageFromPost.oeg");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&",$post));
		$r=curl_exec($ch);
		curl_close($ch);
		
		if($r=="OK") return true;
		return false;
	}

}


class sms_rouenbs {
	
	private $ws;
	private $from='';
	
	function __construct ($param_list) {
		$this->from=$param_list['from'];
		global $class_path;
		require_once($class_path.'/ws_rouenbs.class.php');
		$this->ws = new ws_rouenbs();
	}
	
	function send_sms($telephone, $message) {
		global $charset;
		$r=FALSE;
		$telephone=preg_replace("/.[^0-9]/",'',$telephone);
		$telephone=preg_replace("/^[\+|[^0-9]]/",'',$telephone);
		if (strtoupper($charset)!='UTF-8') {			
			$message = utf8_encode($message);
			$from = utf8_encode($from);
		}
		$r=$this->ws->SendSMS($message,$telephone,$from);
		return $r;
	}

} // fin de déclaration de la classe sms_pmb
  
