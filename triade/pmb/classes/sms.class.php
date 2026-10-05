<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sms.class.php,v 1.15.4.1 2025/04/30 13:28:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition des classes d'envoi de sms selon opérateur


class sms_factory 
{

	public static function make() 
	{
		
		global $empr_sms_config;
	
        /*
         * Les parametres sont definis dans empr_sms_config sous la forme :
         * key1=value1;key2=value2;...
         *
         * Le parametre class_name est obligatoire et definit la classe d'envoi utilisee
         */
        $param_list = [];
        $empr_sms_config = str_replace("\;", "**ESC SEMI COLON**", $empr_sms_config);
		
		$tab_params=explode(';',$empr_sms_config);	  
		if(is_array($tab_params)) {
			foreach($tab_params as $param){
                $pos = strpos($param, '=', 0);
                if($pos !== false) {
                    $key = substr($param, 0, $pos);
                    $value = substr($param, $pos+1);
                    $param_list[$key] = str_replace("**ESC SEMI COLON**", ";", $value);
                }
            }
		}
        if (! $param_list['class_name']) {
            return false;
		}
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
	
	public function __construct ($param_list) {		
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
	
	public function send_sms($telephone, $message) {
		global $charset;
		global $pmb_curl_timeout;
		
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
		if (strtoupper($charset) != "UTF-8") {
		    foreach ($fields as $key => $val) {
		        $fields[$key] = encoding_normalize::utf8_normalize($val);
		    }
		}
		$post=array();
		foreach ($fields as $key=>$val) $post[]=$key."=".rawurlencode($val);
		$timeout=($pmb_curl_timeout*1 ? $pmb_curl_timeout*1 : 5);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.smstrend.net/fra/sendMessageFromPost.oeg");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&",$post));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$r=curl_exec($ch);
		curl_close($ch);
		
		if($r=="OK") return true;
		return false;
	}

}


class sms_rouenbs {
	
	private $ws;
	private $from='';
	
	public function __construct ($param_list) {
		$this->from=$param_list['from'];
		global $class_path;
		require_once($class_path.'/ws_rouenbs.class.php');
		$this->ws = new ws_rouenbs();
	}
	
	public function send_sms($telephone, $message) {
		global $charset;
		
		$r = FALSE;
		$telephone = preg_replace("/.[^0-9]/", '', $telephone);
		$telephone = preg_replace("/^[\+|[^0-9]]/", '', $telephone);
		if (strtoupper($charset) != 'UTF-8') {
		    $message = encoding_normalize::utf8_normalize($message);
		    $from = encoding_normalize::utf8_normalize($from);
		}
		$r = $this->ws->SendSMS($message, $telephone, $from);
		return $r;
	}

}
  

class allmysms {
	
	private $url = 'https://api.allmysms.com/http/9.0/';
	private $login = '';
	private $apikey = '';
	private $tpoa = '';

	
	public function __construct(array $param_list) {
		
		$param_list = encoding_normalize::utf8_normalize($param_list);
		foreach ($param_list as $k=>$v) {
			if(property_exists($this, $k)) {
				$this->$k = $v;
			}
		}
		if( !preg_match("/^[a-z]{1}[a-z|0-9]{0,10}$/i", $this->tpoa)) {
			$this->tpoa = " ";
		}
	}
	
	public function send_sms($telephone, $message) {
		
		$telephone = preg_replace("/[^\+|0-9]/", "", $telephone);
		$message = encoding_normalize::utf8_normalize($message);
				
		$fields = [
				'login'		=> $this->login,
				'apiKey'	=> $this->apikey,
				'message'	=> $message,
				'mobile'	=> $telephone,
				'tpoa'		=> $this->tpoa,
				'coding'	=> 2
				
		];
		$fields_string = http_build_query($fields);
		
		$curl = new Curl();
		$curl->set_option('CURLOPT_CONNECTTIMEOUT', 10);
		$response = $curl->post($this->url, $fields_string);
		
		$response_body = json_decode($response->body, true);
		if( 100 != $response_body['status'] ) {
			return false;
		}
		return true;
	}
	
}

class tunisie_sms {
    
    // "https://api.l2t.io/tn/v0/api/api.aspx?fct=sms&key={{KEY}}&mobile={{MOBILE}}&sms={{SMS}}&sender={{SENDER}}&date={{DATE}}&heure={{HEURE}}";
    // Parametres a indiquer dans > empr > sms_config = key, sender, [url]
    private $url = "https://api.l2t.io/tn/v0/api/api.aspx?";

    private $key = "";
    private $sender = "";
    
    public function __construct(array $param_list) {
        
        $param_list = encoding_normalize::utf8_normalize($param_list);
        foreach ($param_list as $k=>$v) {
            if(property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
    
    public function send_sms($telephone, $message) {
        
        $telephone = preg_replace("/[^\+|0-9]/", "", $telephone);
        $message = encoding_normalize::utf8_normalize($message);
        
        $fields = [
            'fct'       => 'sms',
            'key'       => $this->key,
            'mobile'	=> $telephone,
            'sms'       => $message,
            'sender'	=> $this->sender
        ];
        
        $curl = new Curl();
        $curl->set_option('CURLOPT_CONNECTTIMEOUT', 10);
        $response = $curl->get($this->url, $fields);
        
        $response_body = json_decode(json_encode(simplexml_load_string($response->body, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)),true);
        $status_code = $response_body['status']['status_code'] ?? 0;
        
        if( 200 != $status_code ) {
            return false;
        }
        return true;
    }
}

