<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dilicom_apiapp_client.class.php,v 1.2.10.1 2025/03/14 08:07:35 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "{$class_path}/curl.class.php";


class dilicom_apiapp_client {

    const USER_AGENT = 315;
    const DEFAULT_APPLICATION_NAME = 'PMB';
	const DILICOM_CHECK_TOKEN_URL = "checkToken";
	const DEFAULT_HEADERS = [
	    'Accept'		=> 'application/json',
	    'Content-Type'	=> 'application/x-www-form-urlencoded'
	];

	protected $ws_url = '';
	protected $hmac_key = '';
	protected $api_key = '';

	protected $curl_channel = false;

	protected $curl_method = 'get';
	protected $curl_headers = [];
	protected $curl_params = [];
	protected $curl_url = '';

	protected $curl_response = '';

	protected $error = false;
	protected $error_msg = [];
	protected $result = '';
	protected $headers = [];
	/**
	 * constructeur
	 *
	 * @return void
	 */
	public function __construct($hmac_key = '', $ws_url = '') {

		$this->hmac_key = $hmac_key;
		if($ws_url) {
			$this->ws_url = $ws_url;
		} else {
		    $this->ws_url = dilicom_apiapp_source::DEFAULT_APIAPP_WS_URL;
		}
		$this->curl_channel = new Curl();
	}

	/**
	 *
	 * @param string $api_key
	 *
	 * @return void
	 */
	public function set_api_key($api_key) {
		$this->api_key = $api_key;
	}

	/**
	 * Lecture messages d'erreur
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->error_msg;
	}


	/**
	 * RAZ messages d'erreur
	 *
	 * @return void
	 */
	public function reset_errors() {

		$this->error = false;
		$this->error_msg = [];
	}

	/**
	 * Lecture resultat
	 *
	 * @return array
	 */
	public function get_result() {
		return $this->result;
	}

	/**
	 * RAZ resultat
	 *
	 * @return void
	 */
	public function reset_result() {

		$this->result = '';
	}

	/**
	 * Envoi requete
	 *
	 * @return bool
	 */
	protected function send_request() {
		$this->curl_response = '';
		$this->get_headers();
 		$this->curl_headers = dilicom_apiapp_client::DEFAULT_HEADERS;
 		//Ajout du tableau de headers au curl_headers
 		$this->curl_headers += $this->headers;
		$this->curl_channel->headers = $this->curl_headers;
		$this->curl_response = $this->curl_channel->get($this->curl_url, $this->curl_params);

		if($this->curl_response->headers['Status-Code']!='200') {
			$this->error = true;
			$this->error_msg[] = "curl => ".$this->curl_response->headers['Status'];
			return false;
		} else {
			return true;
		}

	}

    /**
     * Methode permettant la vérification du jeton coté Dilicom
     * @param unknown $tokenValue
     * @param unknown $gln
     * @return boolean
     */
	public function check_token_to_dilicom($tokenValue){
	    global $pmb_pnb_param_login;

	    $this->reset_errors();
	    $this->reset_result();

	    $this->curl_params = [
	        "tokenValue" => $tokenValue,
	        "Library" => $pmb_pnb_param_login
	    ];
	    $this->curl_url = $this->ws_url.self::DILICOM_CHECK_TOKEN_URL;
	    $this->send_request();

	    if($this->error) {
	        return false;
	    }

	    $response_body = json_decode($this->curl_response->body, true);
        $this->result = $response_body;
	    if(is_null($response_body)) {
	        $this->error = true;
	        $this->error_msg[] = 'Dilicom => json response error';
	        return false;
	    }
	    if(empty($response_body)) {
	        $this->error = true;
	        $this->error_msg[] = 'Dilicom => no result provided';
	        return false;
	    }

        if(empty($response_body['tokenStatus']) || $response_body['tokenStatus'] != 'OK') {
            $this->error = true;
            $this->error_msg[] = 'Dilicom => Token error';
            return false;
        }
	    if(empty($response_body['returnStatus']) || $response_body['returnStatus'] != 'OK') {
	        $this->error = true;
	        $this->error_msg[] = 'Dilicom => Error status';
	        return false;
	    }

	    return true;

	}

	/**
	 * Generation des headers pour la vers Dilicom
	 */
	public function get_headers(){
	    global $pmb_version_brut;

	    $date = new DateTime('NOW');
	    $XAuthorization = ($date->getTimestamp())*1000;
	    $XAuthorizationContent = hash_hmac('sha256',$XAuthorization.self::DEFAULT_APPLICATION_NAME, $this->hmac_key, false);

	    $this->headers = [
	        'Application-User-Agent' => self::USER_AGENT,
	        'Application-Version' => $pmb_version_brut,
	        'X-Authorization' => $XAuthorization,
	        'X-Authorization-Content' => $XAuthorizationContent,
	        'Application-Name' => self::DEFAULT_APPLICATION_NAME
	    ];
	}

}
