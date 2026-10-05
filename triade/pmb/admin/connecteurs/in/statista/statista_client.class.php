<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statista_client.class.php,v 1.2 2020/07/01 14:12:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "{$class_path}/curl.class.php";


class statista_client {
	
	const WSURL_DEFAULT = 'https://fr.statista.com/api/v2/';
			
	const DEFAULT_HEADERS = [
			'Accept'		=> 'application/json',
			'Content-Type'	=> 'application/x-www-form-urlencoded',
	];
	
	const AUTH_HEADER_KEY  = 'X-STATISTA-API-KEY';
	
	const CONTENT_TYPE_AVAILABLE_VALUES = [
			0	=> "statistics",
			1	=> "infographics",
			2	=> "studies",
	];
	const CONTENT_TYPE_DEFAULT = 0;
	
	const PLATFORM_AVAILABLE_VALUES = [
			0	=> "",
			1	=> "de",
			2	=> "en",
			3	=> "fr",
			4	=> "es",
	];
	const PLATFORM_DEFAULT = 0;
	
	const LIMIT_DEFAULT = 20;
	const LIMIT_MAX = 5000;
	
	const DATE_FROM_DEFAULT = '';
	const DATE_TO_DEFAULT = '';
	
	const PREMIUM_AVAILABLES_VALUES = [0,1];
	const PREMIUM_DEFAULT = 1;
	
	const SORT_AVAILABLE_VALUES = [
			0 => "relevance",
			1 => "date",
			2 => "popularity",
	];
	const SORT_DEFAULT = 1;
	
	
	protected $ws_url = '';
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
	
	/**
	 * constructeur
	 * 
	 * @return void
	 */
	public function __construct($api_key = '', $ws_url = '') {
		
		$this->api_key = $api_key;
		if($ws_url) {
			$this->ws_url = $ws_url;
		} else {
			$this->ws_url = statista_client::WSURL_DEFAULT;
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
	 * 
	 * @param string $q = recherche
	 * @param int $content_type = type de contenu (0:statistics|2:infographics|3:studies)
	 * @param string $platform = plateforme de recherche (de|en|fr|es)
	 * @param int $limit = nb résultats demandés
	 * @param string $date_from = date de début (YYYY-MM-DD)
	 * @param string $date_to = date de fin (YYYY-MM-DD)
	 * @param int $premium = inclure contenu premium
	 * @param int $sort = tri (0: relevance {default}, 1: date, 2: popularity)
	 */
	public function search(
			$q, 
			$content_type = statista_client::CONTENT_TYPE_DEFAULT, 
			$platform = statista_client::PLATFORM_DEFAULT, 
			$limit = statista_client::LIMIT_DEFAULT, 
			$date_from = statista_client::DATE_FROM_DEFAULT, 
			$date_to = statista_client::DATE_TO_DEFAULT, 
			$premium = statista_client::PREMIUM_DEFAULT,  
			$sort = statista_client::SORT_DEFAULT
		) {
		
			$this->reset_errors();
			$this->reset_result();
			
			if(!is_string($q)) {
				return;
			}
			//test $content_type
			$content_type = intval($content_type);
			if(!array_key_exists($content_type, statista_client::CONTENT_TYPE_AVAILABLE_VALUES)) {
				$content_type = statista_client::CONTENT_TYPE_DEFAULT;
			}
		
			//test $platform
			if(!in_array($platform, statista_client::PLATFORM_AVAILABLE_VALUES)) {
				$platform = statista_client::PLATFORM_DEFAULT;
			}
			//test $limit
			$limit = intval($limit);
			if(!$limit || ($limit > statista_client::LIMIT_MAX)) {
				$limit = statista_client::LIMIT_DEFAULT;
			}
			
			//test $date_from
			$date_from = DateTime::createFromFormat('YYYY-MM-DD', $date_from);
			if(!$date_from) {
				$date_from = statista_client::DATE_FROM_DEFAULT;
			}
			
			//test $date_to
			$date_to = DateTime::createFromFormat('YYYY-MM-DD', $date_to);
			if(!$date_to) {
				$date_to = statista_client::DATE_TO_DEFAULT;
			}
			
			//test $premium
			$premium = intval($premium);
			if(!in_array($premium, statista_client::PREMIUM_AVAILABLES_VALUES)) {
				$premium = statista_client::PREMIUM_DEFAULT;
			}
			
			//test $sort
			$sort = intval($sort);
			if(!array_key_exists($sort, statista_client::SORT_AVAILABLE_VALUES)) {
				$sort = statista_client::SORT_DEFAULT;
			}
			
			//construction parametres requete
			
			$this->curl_url = statista_client::WSURL_DEFAULT.statista_client::CONTENT_TYPE_AVAILABLE_VALUES[$content_type];
			
			$this->curl_params['q'] = $q;
			
			
			if($platform) {
				$this->curl_params['platform'] = $platform;
			}
			if(!$limit || ($limit != statista_client::LIMIT_DEFAULT)) {
				$this->curl_params['limit'] = $limit;
			}
			if($date_from) {
				$this->curl_params['date_from'] = $date_from;
			}
			if($date_to) {
				$this->curl_params['date_to'] = $date_to;
			}
			if($premium != statista_client::PREMIUM_DEFAULT) {
				$this->curl_params['premium'] = $premium;
			}
			if($sort) {
				$this->curl_params['sort'] = $sort;
			}
			
			$this->send_request();
			
			if($this->error) {
				return false;
			}
			
			$response_body = json_decode($this->curl_response->body, true);
			
			if(is_null($response_body)) {
				$this->error = true;
				$this->error_msg[] = 'search => json response error';
				return false;
			}
			if(empty($response_body)) {
				$this->error = true;
				$this->error_msg[] = 'search => no result provided';
				return false;
			}
			$this->result = $response_body;
			return true;
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
		
 		$this->curl_headers = statista_client::DEFAULT_HEADERS;
 		$this->curl_headers[statista_client::AUTH_HEADER_KEY] = $this->api_key;
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
	
}
