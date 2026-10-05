<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: europresse_client.class.php,v 1.3 2022/02/24 14:32:41 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "{$class_path}/curl.class.php";


class europresse_client {
	
	const WSURL_DEFAULT = 'https://api.cedrom-sni.com/api/';
	
	const AUTH_PATH = 'auth/login';
	const AUTH_PATH_LOGOUT = 'auth/Logout';
	const AUTH_GRANT_TYPE = 'password';
	const AUTH_HEADERS = [
			'Accept'		=> 'application/json',
			'Content-Type'	=> 'application/x-www-form-urlencoded',
	];
	const DOCUMENTS_SEARCH_SIMPLE_PATH = 'v2/documents/Search/Simple';
	const DEFAULT_HEADERS = [
			'Accept'		=> 'application/json',
			'Content-Type'	=> 'application/json',
	];
	const DEFAULT_AUTHORIZATION_HEADER_KEY = 'Authorization';
	const DEFAULT_AUTHORIZATION_HEADER_PREFIX = 'Bearer ';
	
	const DATERANGE_AVAILABLE_VALUES = [
			'TODAY',
			'SINCE_YESTERDAY',
			'DAYS_3',
			'DAYS_7',
			'DAYS_30',
			'MONTHS_3',
			'MONTHS_6',
			'YEARS_1',
			'YEARS_2',
			'ALL',
	];
	const DATERANGE_DEFAULT = 'DAYS_7';
	const MAXCOUNT_DEFAULT = '100';
	const DOCURL_AVAILABLE_VALUES = [
			'http',
			'https',	
	];
	const DOCURL_DEFAULT = 'https';
	const FIELDS_AVAILABLE_VALUES = [
			'',
			'Result',
			'Count',
			'Attachements',
	];
	const FIELDS_DEFAULT = '';
	const SORT_AVAILABLE_VALUES = [
			'',
			'relevance',
			'date',
		];
	const SORT_DEFAULT = 'date';
		
	const QUERIES_DOMAINS_PATH = 'v2/queries/domains';
	
	const CSLANGUAGE_HEADER = 'Cs-Language';
	
	const CSLANGUAGE_AVAILABLE_VALUES = [
			'fr_FR'	=> 'French',
			'en_UK'	=> 'English'
			
	];
	const CSLANGUAGE_DEFAULT = 'French';
	
	const QUERIES_CRITERIA_PATH = 'v2/queries/criteria';
	
	const CATEGORYNAME_AVAILABLE_VALUES = [
			'', 
			'All',
			'Origin',
			'Type',
			'Language',
			'Frequency',
			'Topic',
			'Source',
			'Package',
			'Coverage',
	];
	const CATEGORYNAME_ALL = 'All';
	
	const QUERIES_PUBLICATIONS_PATH = 'v2/queries/publications';
	
	const DOCUMENTS_SEARCH_PATH = 'v2/documents/Search';
	
	const DOCUMENTBASE_AVAILABLE_VALUES = [
			'News',
			'Companies',
			'Biographies',
	];
	const DOCUMENTBASE_DEFAULT = 'News';

	const DOMAINID_DEFAULT = '';
	
	const INCLUDES_DEFAULT = [];
	
	const EXCLUDES_DEFAULT = [];
	
	const STARTDATE_DEFAULT = '';
	const ENDDATE_DEFAULT = '';
	
	const DOCUMENTS_PATH = 'v2/documents';
	
	protected $ws_url = '';
	protected $username = '';
	protected $password = '';
	protected $access_token = '';
	
	protected $curl_channel = false;

	protected $curl_method = 'get';
	protected $curl_headers = [];
	protected $curl_params = [];
	protected $curl_url = '';
	
	protected $curl_response = '';
	
	protected $error = false;
	protected $error_msg = [];
	protected $result = '';
	
	static protected $domains = [];
	static protected $criteria = [];
	static protected $criteria_language = [];
	static protected $criteria_topic = [];
	static protected $criteria_origin = [];
	static protected $criteria_frequency = [];
	static protected $criteria_type = [];
	static protected $criteria_source = [];
	static protected $criteria_package = [];
	static protected $criteria_coverage = [];
	static protected $publications = [];

	/**
	 * constructeur
	 * 
	 * @return void
	 */
	public function __construct($username = '', $password = '', $ws_url = '') {
		
		$this->username = $username;
		$this->password = $password;
		if($ws_url) {
			$this->ws_url = $ws_url;
		} else {
			$this->ws_url = europresse_client::WSURL_DEFAULT;
		}
		$this->curl_channel = new Curl();
	}
	
	
	public function set_username($username) {
		$this->username = $username;
	}
	
	public function set_password($password) {
		$this->password = $password;
	}
	
	public function set_url($ws_url) {
		$this->ws_url = $ws_url;
	}
	/**
	 * Authentification
	 * 
	 * @return bool
	 */
	public function auth_login() {
		
		$this->curl_method = 'post';
		$this->curl_url = $this->ws_url.europresse_client::AUTH_PATH;
		$this->curl_headers = europresse_client::AUTH_HEADERS;
		$this->curl_params = [
				'grant_type' 	=> europresse_client::AUTH_GRANT_TYPE,
				'username'		=> $this->username,
				'password'		=> $this->password,
		];		
		
		$this->send_request();
		
		if($this->error) {
			return false;
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		if(is_null($response_body)) {
			$this->error = true;
			$this->error_msg[] = 'auth => json response error';
			return false;
		}
		if(empty($response_body['access_token'])) {
			$this->error = true;
			$this->error_msg[] = 'auth => no access_token provided';
			return false;
		}
		$this->access_token = $response_body['access_token'];
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
	 * Recherche simple
	 *  
	 * @return bool
	 * 
	 */
	public function documents_search_simple($query, 
			$dateRange = europresse_client::DATERANGE_DEFAULT, 
			$maxCount = europresse_client::MAXCOUNT_DEFAULT, 
			$docUrl = europresse_client::DOCURL_DEFAULT, 
			$fields = europresse_client::FIELDS_DEFAULT, 
			$sort = europresse_client::SORT_DEFAULT) {
		
		$this->reset_errors();
		$this->reset_result();
				
		if($this->access_token == '') {
			$this->auth_login();
		}
		if($this->error) {
			return false;
		}
		$this->curl_method = 'get';
		$this->curl_url = $this->ws_url.europresse_client::DOCUMENTS_SEARCH_SIMPLE_PATH;
		$this->curl_headers = europresse_client::DEFAULT_HEADERS;
		$this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
		
		$this->curl_params = [
				'query' 		=> $query,
				'dateRange'		=> $dateRange,
				'maxCount'		=> $maxCount,
				'docUrl'		=> $docUrl,
				'fields'		=> $fields,
				'sort'			=> $sort,
		];
		
		$this->send_request();
		
		if($this->error) {
			return false;
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		$this->auth_logout();
		if( is_null($response_body) || empty($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'search => json response error';
			return false;
		}
		$this->result = $response_body;
		return true;
	}
	

	/**
	 * Recherche
	 *
	 * @return bool
	 *
	 */
	public function documents_search(
			$query,
			$documentBase = europresse_client::DOCUMENTBASE_DEFAULT,
			$domainId = europresse_client::DOMAINID_DEFAULT,
			$includes = europresse_client::INCLUDES_DEFAULT,
			$excludes = europresse_client::EXCLUDES_DEFAULT,
			$maxCount = europresse_client::MAXCOUNT_DEFAULT,
			$docUrl = europresse_client::DOCURL_DEFAULT,
			$dateRange = europresse_client::DATERANGE_DEFAULT,
			$startDate = europresse_client::STARTDATE_DEFAULT,
			$endDate = europresse_client::ENDDATE_DEFAULT,
			$fields = europresse_client::FIELDS_DEFAULT,
			$sort = europresse_client::SORT_DEFAULT, 
			$CsLanguage = europresse_client::CSLANGUAGE_DEFAULT
			) {
				
			$this->reset_errors();
			$this->reset_result();
			
			if($this->access_token == '') {
				$this->auth_login();
			}
			if($this->error) {
				return false;
			}
			
			if(!in_array($CsLanguage, europresse_client::CSLANGUAGE_AVAILABLE_VALUES)) {
				$CsLanguage = europresse_client::CSLANGUAGE_DEFAULT;
			}
			
			$this->curl_method = 'get';
			$this->curl_url = $this->ws_url.europresse_client::DOCUMENTS_SEARCH_PATH;
			$this->curl_headers = europresse_client::DEFAULT_HEADERS;
			$this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
			$this->curl_headers[europresse_client::CSLANGUAGE_HEADER] = $CsLanguage;
			$this->curl_params = [
					'query' 		=> $query,
					'documentBase'	=> $documentBase,
					'domainId'		=> $domainId,
					'includes'		=> $includes,
					'excludes'		=> $excludes,
					'maxCount'		=> $maxCount,
					'docUrl'		=> $docUrl,
					'dateRange'		=> $dateRange,
					'startDate'		=> $startDate,
					'endDate'		=> $endDate,
					'fields'		=> $fields,
					'sort'			=> $sort,
			];
			
			$this->send_request();
			
			if($this->error) {
				return false;
			}
			
			$response_body = json_decode($this->curl_response->body, true);
			$this->auth_logout();
			if( is_null($response_body) || empty($response_body) ) {
				$this->error = true;
				$this->error_msg[] = 'search => json response error';
				return false;
			}
			$this->result = $response_body;
			return true;
	}
	
	/**
	 * Recuperation document
	 */
	public function documents($documentId) {
		
		$this->reset_errors();
		$this->reset_result();
		
		if($this->access_token == '') {
			$this->auth_login();
		}
		if($this->error) {
			return false;
		}

		$this->curl_method = 'get';
		$this->curl_url = $this->ws_url.europresse_client::DOCUMENTS_PATH."/".$documentId;
		$this->curl_headers = europresse_client::DEFAULT_HEADERS;
		$this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
		$this->curl_params = [
		];
		
		$this->send_request();
		
		if($this->error) {
			return false;
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		$this->auth_logout();
		if( is_null($response_body) || empty($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'documents => json response error';
			return false;
		}
		$this->result = $response_body;
		return true;
	}
	
	/**
	 * Recuperation domaines / langage
	 *
	 * @param string $cs_language
	 *
	 * @return array
	 *
	 */
	public function get_domains($CsLanguage = europresse_client::CSLANGUAGE_DEFAULT) {
				
		if(count(static::$domains)) {
			return static::$domains;
		}
		
		$this->reset_errors();
		
		if($this->access_token == '') {
			$this->auth_login();
		}
		if($this->error) {
			return [];
		}
		if(array_key_exists($CsLanguage, europresse_client::CSLANGUAGE_AVAILABLE_VALUES)) {
			$CsLanguage = europresse_client::CSLANGUAGE_AVAILABLE_VALUES[$CsLanguage];
		} else if(!in_array($CsLanguage, europresse_client::CSLANGUAGE_AVAILABLE_VALUES)) {
			$CsLanguage = europresse_client::CSLANGUAGE_DEFAULT;
		}
		
		$this->curl_method = 'get';
		$this->curl_url = $this->ws_url.europresse_client::QUERIES_DOMAINS_PATH;
		$this->curl_headers = europresse_client::DEFAULT_HEADERS;
		$this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
		$this->curl_headers[europresse_client::CSLANGUAGE_HEADER] = $CsLanguage;
		$this->curl_params = [
		];
		
		$this->send_request();
		
		if($this->error) {
			return [];
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		$this->auth_logout();
		if( is_null($response_body) || empty($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'domains => json response error';
			return [];
		}
		
		if(!is_array($response_body) || !count($response_body)) {
			return [];
		}
		
		foreach($response_body as $v) {
			static::$domains[$v['domainId']] = $v['domainName'];
		}
		return static::$domains;
	}
	
	/**
	 * Recuperation criteres / categorie
	 *
	 * @param string $category_name
	 *
	 * @return array
	 *
	 */
	public function get_criteria($category_name = europresse_client::CATEGORYNAME_ALL) {
		
		$this->reset_errors();
		
		if($this->access_token == '') {
			$this->auth_login();
		}
		if($this->error) {
			return [];
		}
		
		if(!in_array($category_name, europresse_client::CATEGORYNAME_AVAILABLE_VALUES)) {
			$category_name = europresse_client::CATEGORYNAME_ALL;
		}
		
		$this->curl_method = 'get';
		$this->curl_url = $this->ws_url.europresse_client::QUERIES_CRITERIA_PATH;
		$this->curl_headers = europresse_client::DEFAULT_HEADERS;
		$this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
		
		$this->curl_params = [
				'categoryName'	=> $category_name,
		];
		
		$this->send_request();
		
		if($this->error) {
			return [];
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		$this->auth_logout();
		if( is_null($response_body) || empty($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'criteria => json response error';
			return [];
		}
		
		if(!is_array($response_body) || !count($response_body)) {
			return [];
		}
		
		foreach($response_body as $v) {
			static::$criteria[$v['criterionCategory']][$v['criterionId']] = $v['criterionName'];
		}
		
		if(!empty($response_body['LANGUAGE'])) {
			static::$criteria_language = $response_body['LANGUAGE'];
		}
		return static::$criteria;
	}
	

	/**
	 * Recuperation criteres origine
	 *
	 * @return array
	 *
	 */
	public function get_criteria_origin() {
		
		if(count(static::$criteria_origin)) {
			return static::$criteria_origin;
		}
		
		static::$criteria_origin = [];
		$c = $this->get_criteria('Origin');
		
		if (!empty($c['GEO_FROM'])) {
			static::$criteria_origin['GEO_FROM'] = $c['GEO_FROM'];
		}
		if (!empty($c['COUNTRY'])) {
			static::$criteria_origin['COUNTRY'] = $c['COUNTRY'];
		}
		if (!empty($c['STATE'])) {
			static::$criteria_origin['STATE'] = $c['STATE'];
		}
		if (!empty($c['CITY'])) {
			static::$criteria_origin['CITY'] = $c['CITY'];
		}
		return static::$criteria_origin;
	}
	
	/**
	 * Recuperation criteres type
	 *
	 * @return array
	 *
	 */
	public function get_criteria_type() {
		
		if(count(static::$criteria_type)) {
			return static::$criteria_type;
		}
		
		static::$criteria_type = [];
		$c = $this->get_criteria('Type');
		
		if (!empty($c['TYPE'])) {
			static::$criteria_type['TYPE'] = $c['TYPE'];
		}
		if (!empty($c['SUBTYPE'])) {
			static::$criteria_type['SUBTYPE'] = $c['SUBTYPE'];
		}
		return static::$criteria_type;}
	
	/**
	 * Recuperation criteres langage
	 *
	 * @return array
	 *
	 */
	public function get_criteria_language() {
		
		if(count(static::$criteria_language)) {
			return static::$criteria_language;
		}
		
		static::$criteria_language = [];
		$c = $this->get_criteria('Language');
		
		if(!empty($c['LANGUAGE'])) {
			static::$criteria_language = $c['LANGUAGE'];
		}
		return static::$criteria_language;
	}
	
	/**
	 * Recuperation criteres frequence
	 *
	 * @return array
	 *
	 */
	public function get_criteria_frequency() {
		
		if(count(static::$criteria_frequency)) {
			return static::$criteria_frequency;
		}
		
		static::$criteria_frequency = [];
		$c = $this->get_criteria('Frequency');
		
		if(!empty($c['FREQUENCY'])) {
			static::$criteria_frequency = $c['FREQUENCY'];
		}
		return static::$criteria_frequency;
	}
	
	/**
	 * Recuperation criteres sujet
	 *
	 * @return array
	 *
	 */
	public function get_criteria_topic() {

		if(count(static::$criteria_topic)) {
			return static::$criteria_topic;
		}
		
		static::$criteria_topic = [];
		$c = $this->get_criteria('Topic');
		
		if(!empty($c['DOMAIN'])) {
			static::$criteria_topic = $c['DOMAIN'];
		}
		return static::$criteria_topic;
	}
	
	/**
	 * Recuperation criteres source
	 *
	 * @return array
	 *
	 */
	public function get_criteria_source() {
		
		if(count(static::$criteria_source)) {
			return static::$criteria_source;
		}
		
		static::$criteria_source = [];
		$c = $this->get_criteria('Source');
		if(!empty($c['SOURCE']) ) {
			static::$criteria_source = $c['SOURCE'];
		}
		return static::$criteria_source;
	}
	
	/**
	 * Recuperation criteres package
	 *
	 * @return array
	 *
	 */
	public function get_criteria_package() {
		
		if(count(static::$criteria_package)) {
			return static::$criteria_package;
		}
		
		static::$criteria_package = [];
		$c = $this->get_criteria('Package');
		if(!empty($c['PACKSOURCE']) ) {
			static::$criteria_package = $c['PACKSOURCE'];
		}
		return static::$criteria_package;
	}

	/**
	 * Recuperation criteres geographique
	 *
	 * @return array
	 *
	 */
	public function get_criteria_coverage() {
		
		if(count(static::$criteria_coverage)) {
			return static::$criteria_coverage;
		}
		
		static::$criteria_coverage = [];
		$c = $this->get_criteria('Coverage');
		if(!empty($c['GEO_COVER']) ) {
			static::$criteria_coverage = $c['GEO_COVER'];
		}
		return static::$criteria_coverage;
	}
	
	/**
	 * Recuperation publications accessibles
	 *	 *
	 * @return array
	 *
	 */
	public function get_publications() {
		
		if(count(static::$publications)) {
			return static::$publications;
		}
		$this->reset_errors();
		
		if($this->access_token == '') {
			$this->auth_login();
		}
		if($this->error) {
			return [];
		}
		$this->curl_method = 'get';
		$this->curl_url = $this->ws_url.europresse_client::QUERIES_PUBLICATIONS_PATH;
		$this->curl_headers = europresse_client::DEFAULT_HEADERS;
		$this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
		
		$this->curl_params = [];
		
		$this->send_request();
		
		if($this->error) {
			return [];
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		$this->auth_logout();
		if( is_null($response_body) || empty($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'publications => json response error';
			return [];
		}
		
		if(!is_array($response_body) || !count($response_body)) {
			return [];
		}
		
		foreach($response_body as $v) {
			static::$publications[$v['pubId']] = $v['pubName'];
		}
		return static::$publications;
	}
	
	/**
	 * Envoi requete
	 * 
	 * @return bool
	 */
	protected function send_request() {
		
		$this->curl_response = '';
		
		$this->curl_channel->headers = $this->curl_headers;
		switch ($this->curl_method) {
			case 'post' : 
				$this->curl_response = $this->curl_channel->post($this->curl_url, $this->curl_params);
				break;
			case 'get' :
			default :
				$this->curl_response = $this->curl_channel->get($this->curl_url, $this->curl_params);
				break;
		}
		
		if($this->curl_response->headers['Status-Code']!='200') {
			$this->error = true;
			$this->error_msg[] = "curl => ".$this->curl_response->headers['Status'];
			return false;
		} else {
			return true;
		}
		
	}
	
	/**
	 * logout
	 *
	 * @return bool
	 */
	public function auth_logout() {
	    if ($this->access_token) {
	        $this->curl_method = 'get';
	        $this->curl_url = $this->ws_url.europresse_client::AUTH_PATH_LOGOUT;
	        $this->curl_headers = europresse_client::DEFAULT_HEADERS;
	        $this->curl_headers[europresse_client::DEFAULT_AUTHORIZATION_HEADER_KEY] = europresse_client::DEFAULT_AUTHORIZATION_HEADER_PREFIX.$this->access_token;
	        
	        $this->send_request();
	        if($this->error) {
	            return false;
	        }
	        $response_body = json_decode($this->curl_response->body, true);
	        if(is_null($response_body)) {
	            $this->error = true;
	            $this->error_msg[] = 'logout => json response error';
	            return false;
	        }
	        $this->access_token = "";
	    }
	    return true;
	}
	
}
