<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: xerfi_client.class.php,v 1.3 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once $class_path."/encoding_normalize.class.php";


class xerfi_client {
	
	const WSDL_URL_DEFAULT = 'http://archimed.xerfi.com/WEBSERVICE_RECHERCHE_WEB/awws/WEBSERVICE_RECHERCHE_V4.awws?wsdl';	
	const SOAP_OPTIONS_DEFAULT = [
			'location' => 'http://archimed.xerfi.com/WEBSERVICE_RECHERCHE_WEB/awws/WEBSERVICE_RECHERCHE_V4.awws',
			'soap_version' => SOAP_1_1,
			'keep_alive' => true,
			'connection_timeout' => 5,
			'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
			'encoding' => 'utf-8',
			'cache_wsdl' => WSDL_CACHE_NONE,
			'trace' => false,
			'exceptions' => true,
			'compression'	=> true,
	];
	
	const SEARCH_METHOD = 'recherche_fédérée';
	const SEARCH_RESULT_CLASS = 'recherche_fédéréeResult';
	const MAXCOUNT_DEFAULT = 100;
	
	protected $wsdl_url = '';
	protected $soap_options = [];
	protected $username = '';
	protected $password = '';

	protected $soap_client = null;
	
	protected $error = false;
	protected $error_msg = [];
	protected $result = '';
	
	/**
	 * constructeur
	 * 
	 * @return void
	 */
	public function __construct($username = '', $password = '', $wsdl_url = '', $soap_options = []) {
		
		$this->username = $username;
		$this->password = $password;
		if($wsdl_url) {
			$this->wsdl_url = $wsdl_url;
		} else {
			$this->wsdl_url = xerfi_client::WSDL_URL_DEFAULT;
		}
		if(is_array($soap_options) && !empty($soap_options)) {
			$this->soap_options = $soap_options;
		} else {
			$this->soap_options = xerfi_client::SOAP_OPTIONS_DEFAULT;
		}
	}
	
	public function set_username($username) {
		$this->username = $username;
	}
	
	public function set_password($password) {
		$this->password = $password;
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
		
		$this->result = [];
	}
	
	/**
	 * Recherche
	 *
	 * @return bool
	 *
	 */
	public function search(
			$query,
			$maxCount = xerfi_client::MAXCOUNT_DEFAULT) {
				
			$this->reset_errors();
			$this->reset_result();
			
			if(!is_string($query) || empty($query)) {
				$this->error = true;
				$this->error_msg[] = 'search => wrong query';
				return false;
			}
			
			$maxCount = intval($maxCount);
			
			$search_params = [
					'sMotsCles'			=> $query,
					'login_utilisateur'	=> $this->username,
					'mdp_utilisateur'	=> $this->password,
			];
			
			$this->get_soap_client();;
			
			try {
			    $r = $this->soap_client->{encoding_normalize::utf8_normalize(xerfi_client::SEARCH_METHOD)}($search_params);
			} catch (Exception $e ) {		
				$this->error = true;
				$this->error_msg[] = "search =>  " . $e->getMessage();
				return false;
			}

			if(!is_a($r, 'StdClass')) {
				$this->error = true;
				$this->error_msg[] = "search =>  wrong result";
				return false;
			}

			if( !property_exists($r, encoding_normalize::utf8_normalize(xerfi_client::SEARCH_RESULT_CLASS)) ) {
				$this->error = true;
				$this->error_msg[] = "search =>  wrong result";
				return false;
			}
			$result_obj =  $r->{encoding_normalize::utf8_normalize(xerfi_client::SEARCH_RESULT_CLASS)};
			if($result_obj->TotalCount === 0 ) {
				return true;
			}
			$nb_results = min($result_obj->TotalCount, $maxCount);
			foreach($result_obj->Etude as $etude) {
				if( count($this->result) >= $nb_results) {
					continue;
				}
				$this->result[] = encoding_normalize::obj2array($etude);
			}
			return true;
	}
	
	protected function get_soap_client() {
		if(is_null($this->soap_client)) {
			$this->soap_client = new SoapClient($this->wsdl_url, $this->soap_options);
		}
	}
	
}
