<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: eds_client.class.php,v 1.8.4.1 2025/03/14 08:07:35 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "{$class_path}/multicurl.class.php";


class eds_client {

	const WSURL_DEFAULT = 'https://eds-api.ebscohost.com/';

	const UIDAUTH_PATH = 'authservice/rest/UIDAuth';

	const CURL_HTTPHEADER = [
			'Connection: keep-alive',
			'Accept: application/json',
			'Content-Type: application/json',
	];

	const CREATESESSION_PATH = 'edsapi/rest/CreateSession';
	const AUTH_HEADER_KEY  = 'x-authenticationToken';

	const ENDSESSION_PATH = 'edsapi/rest/EndSession';

	const INFO_PATH = 'edsapi/rest/info';
	const SESSION_HEADER_KEY = 'x-sessionToken';

	const SEARCH_PATH = 'edsapi/rest/Search';
	const SEARCHMODE_AVAILABLE_VALUES = [
			'any',
			'bool',
			'all',
			'smart',
	];
	const SEARCHMODE_DEFAULT = 'bool';
	const RESULTSPERPAGE_DEFAULT = 100;
	const RESULTSPERPAGE_MAX = 100;
	const RESULTSINCACHE = 250;
	const MAXCOUNT_DEFAULT = 100;
	const PAGENUMBER_DEFAULT = 1;
	const SORT_AVAILABLE_VALUES = [
			'relevance',
			'date',
			'date2',
	];
	const SORT_DEFAULT = 'date';
	const HIGHLIGHT_AVAILABLE_VALUES = ['y', 'n'];
	const HIGHLIGHT_DEFAULT = 'n';
	const INCLUDEFACETS_AVAILABLE_VALUES = ['y', 'n'];
	const INCLUDEFACETS_DEFAULT = 'n';
	const FACETFILTERS_DEFAULT = [];
	const VIEW_AVAILABLE_VALUES = [
			'title',
			'brief',
			'detailed',
	];
	const VIEW_DEFAULT = 'detailed';
	const ACTIONS_DEFAULT = [];
	const LIMITERS_DEFAULT = [];
	const EXPANDERS_DEFAULT = [];
	const PUBLICATIONID_DEFAULT = '';
	const RELATEDCONTENT_DEFAULT ='';
	const AUTOSUGGEST_AVAILABLE_VALUES = ['y', 'n'];
	const AUTOSUGGEST_DEFAULT = 'n';
	const AUTOCORRECT_AVAILABLE_VALUES = ['y', 'n'];
	const AUTOCORRECT_DEFAULT = 'n';
	const INCLUDEIMAGEQUICKVIEW_AVAILABLE_VALUES = ['y', 'n'];
	const INCLUDEIMAGEQUICKVIEW_DEFAULT = 'n';

	const BOOLEANOPERATOR_AVAILABLE_VALUES = ['AND', 'OR', 'NOT'];
	const BOOLEANOPERATOR_DEFAULT = 'OR';

	const FIELDCODE_AVAILABLE_VALUES = [
			'TX', 	//All Text
			'AU',	//Author
			'TI',	//Title
			'SU',	//Terms
			'SO', 	//Source
			'AB', 	//Abstract
			'IS',	//ISSN
			'IB',	//ISBN
	];
	const FIELDCODE_DEFAULT = 'TX';

	protected $ws_url = '';
	protected $userid = '';
	protected $username = '';
	protected $password = '';
	protected $profile_id = '';

	protected $auth_token = '';
	protected $session_token = '';

	protected $curl_handler;

	protected $curl_method = 'get';
	protected $curl_headers = [];
	protected $raw_params = [];
	protected $curl_params = '';
	protected $curl_url = '';

	protected $curl_requests = [];
	protected $curl_responses;

	protected $error = false;
	protected $error_msg = [];
	protected $result = [];

	static protected $info = [];
	protected $page_number = 1;
	protected $publication_id = '';
	protected $related_content = '';

	const LIMITERS_LANGUAGE_ID = "LA99";
	const LIMITERS_LANGUAGE_DEFAULT = [];
	static protected $languages = [];

	const RETRIEVE_PATH = 'edsapi/rest/Retrieve';
	const EBOOKPREFERREDFORMAT_AVAILABLE_VALUES = ['', 'ebook-epub', 'ebook-pdf'];
	const EBOOKPREFERREDFORMAT_DEFAULT = '';
	protected $record = [];


	/**
	 * constructeur
	 *
	 * @return void
	 */
	public function __construct($userid = '', $password = '', $profile_id = '', $ws_url = '') {

		$this->userid = $userid;
		$this->password = $password;
		$this->profile_id = $profile_id;
		if($ws_url) {
			$this->ws_url = $ws_url;
		} else {
			$this->ws_url = self::WSURL_DEFAULT;
		}
		$this->curl_handler = new multicurl();
		$this->curl_handler->set_external_configure_function('configurer_proxy_curl');
		$this->curl_handler->set_mode(multicurl::MODE_MULTI);
	}


	public function set_username($username) {
		$this->username = $username;
	}

	public function set_password($password) {
		$this->password = $password;
	}

	public function set_profile_id($profile_id) {
		$this->profile_id = $profile_id;
	}

	public function auth() {
		return $this->uidauth();
	}

	/**
	 * Authentification par UID
	 *
	 * @return bool
	 */
	protected function uidauth() {

		$this->auth_token = '';
		$this->curl_url = $this->ws_url.self::UIDAUTH_PATH;
		$this->raw_params = [
				'UserId'	=> $this->userid,
				'Password'	=> $this->password,
		];
		$this->curl_params = json_encode(encoding_normalize::utf8_normalize($this->raw_params));
		$this->curl_headers = [CURLOPT_HTTPHEADER => self::CURL_HTTPHEADER];
		$this->curl_handler->set_mode(multicurl::MODE_MONO);

		$this->curl_handler->add_post(
				$this->curl_url,
				$this->curl_params,
				$this->curl_headers
				);

		$this->curl_handler->run();
		$this->curl_responses = $this->curl_handler->get_responses()[0];
		//var_dump($this->curl_responses);

		if($this->curl_responses['headers']['Status-Code'] != '200') {
			return false;
		}
		$response_body = json_decode($this->curl_responses['body'], true);

		if(is_null($response_body)) {
			$this->error = true;
			$this->error_msg[] = 'auth => json response error';
			return false;
		}
		if(empty($response_body['AuthToken'])) {
			$this->error = true;
			$this->error_msg[] = 'uidauth => no AuthToken provided';
			return false;
		}
		$this->auth_token = $response_body['AuthToken'];
		return true;

	}

	/**
	 * Creation session
	 *
	 * @return bool
	 */
	public function createsession() {

		if($this->auth_token == '') {
			$this->auth();
		}
		$this->session_token = '';
		$this->curl_url = $this->ws_url.self::CREATESESSION_PATH;
		$this->raw_params = [
				'Profile'	=> $this->profile_id,
		];
		$this->curl_params = json_encode(encoding_normalize::utf8_normalize($this->raw_params));
		$this->curl_headers[CURLOPT_HTTPHEADER] = self::CURL_HTTPHEADER;
		$this->curl_headers[CURLOPT_HTTPHEADER][] = self::AUTH_HEADER_KEY.': '.$this->auth_token;

		$this->curl_handler->reset();
		$this->curl_handler->set_mode(multicurl::MODE_MONO);

		$this->curl_handler->add_post(
				$this->curl_url,
				$this->curl_params,
				$this->curl_headers
				);

		$this->curl_handler->run();
		$this->curl_responses = $this->curl_handler->get_responses()[0];

		if($this->curl_responses['headers']['Status-Code'] != '200') {
			return false;
		}
		$response_body = json_decode($this->curl_responses['body'], true);

		if(is_null($response_body)) {
			$this->error = true;
			$this->error_msg[] = 'createsession => json response error';
			return false;
		}
		if(empty($response_body['SessionToken'])) {
			$this->error = true;
			$this->error_msg[] = 'createsession => no SessionToken provided';
			return false;
		}
		$this->session_token = $response_body['SessionToken'];
		return true;
	}


	/**
	 * Fin de session
	 *
	 * @return bool
	 */
	public function endsession() {

		if(!$this->auth_token == '') {
			$this->session_token = '';
			return true;
		}
		if($this->session_token == '') {
			return true;
		}

		$this->curl_url = $this->ws_url.self::ENDSESSION_PATH;
		$this->raw_params = [
				'SessionToken'	=> $this->session_token,
		];
		$this->curl_params = json_encode(encoding_normalize::utf8_normalize($this->raw_params));
		$this->curl_headers[CURLOPT_HTTPHEADER] = self::CURL_HTTPHEADER;
		$this->curl_headers[CURLOPT_HTTPHEADER][] = self::AUTH_HEADER_KEY.': '.$this->auth_token;

		$this->curl_handler->reset();
		$this->curl_handler->set_mode(multicurl::MODE_MONO);

		$this->curl_handler->add_post(
				$this->curl_url,
				$this->curl_params,
				$this->curl_headers
				);

		$this->curl_handler->run();
		$this->curl_responses = $this->curl_handler->get_responses()[0];

		if($this->curl_responses['headers']['Status-Code'] != '200') {
			return false;
		}

		$response_body = json_decode($this->curl_responses->body, true);

		if(is_null($response_body)) {
			$this->error = true;
			$this->error_msg[] = 'endsession => json response error';
			return false;
		}
		if( empty($response_body['IsSuccessful']) || $response_body['IsSuccessful']!='y') {
			$this->error = true;
			$this->error_msg[] = 'endsession => error ending session';
			return false;
		}
		$this->session_token = '';
		return true;
	}


	/**
	 * Ajout requete Recherche
	 * @param array $Queries Tableau de requêtes
	 * [
	 * 	[	'BooleanOperator'	=> string (voir self::BOOLEANOPERATOR_AVAILABLE_VALUES),
	 * 		'FieldCode' 		=> string (voir self::FIELDCODE_AVAILABLE_VALUES),
	 * 		'Term'				=> string
	 * 	],
	 * ]
	 * @param string $SearchMode Mode de recherche (voir self::SEARCHMODE_AVAILABLE_VALUES)
	 * @param int $ResultsPerPage Nombre de résultats par page (max 100)
	 * @param int $PageNumber Numéro de page
	 * @param string $Sort Tri (voir self::SORT_AVAILABLE_VALUES)
	 * @param string $Highlight Mise en évidence de la recherche dans les résultats (n,y)
	 * @param string $IncludeFacets Inclure les facettes (n,y)
	 * @param array $FacetFilters
	 * @param string $View Format du résultat de recherche (voir eds_client;;VIEW_AVAILABLE_VALUES)
	 * @param array $Actions
	 * @param array $Limiters
	 * @param array $Expanders
	 * @param string $PublicationId
	 * @param array $RelatedContent
	 * @param string $AutoSuggest (n,y)
	 * @param string $AutoCorrect (n,y)
	 * @param string $IncludeImageQuickView (n,y)
	 *
	 * @return bool
	 */
	public function add_search_request(
			$Queries,
			$SearchMode = self::SEARCHMODE_DEFAULT,
			$ResultsPerPage = self::RESULTSPERPAGE_DEFAULT,
			$PageNumber = self::PAGENUMBER_DEFAULT,
			$Sort = self::SORT_DEFAULT,
			$Highlight = self::HIGHLIGHT_DEFAULT,
			$IncludeFacets = self::INCLUDEFACETS_DEFAULT,
			$FacetFilters = self::FACETFILTERS_DEFAULT,
			$View = self::VIEW_DEFAULT,
			$Actions = self::ACTIONS_DEFAULT,
			$Limiters = self::LIMITERS_DEFAULT,
			$Expanders = self::EXPANDERS_DEFAULT,
			$PublicationId = self::PUBLICATIONID_DEFAULT,
			$RelatedContent = self::RELATEDCONTENT_DEFAULT,
			$AutoSuggest = self::AUTOSUGGEST_DEFAULT,
			$AutoCorrect = self::AUTOCORRECT_DEFAULT,
			$IncludeImageQuickView = self::INCLUDEIMAGEQUICKVIEW_DEFAULT
			) {

				//test et format $Queries
				if(!is_array($Queries) || empty($Queries)) {
					$this->error = true;
					$this->error_msg[] = 'search => Wrong Queries';
					return false;
				}
				foreach($Queries as $k=>$Query) {
					if(!is_array($Query)) {
						$this->error = true;
						$this->error_msg[] = 'search => Wrong Query';
						return false;
					}
					if(empty($Query['Term'])) {
						$this->error = true;
						$this->error_msg[] = 'search => No Term in Query';
						return false;
					}
					$Query['Term'] = trim($Query['Term']);
					if(empty($Query['Term'])) {
						$this->error = true;
						$this->error_msg[] = 'search => Empty Term in Query';
						return false;
					}
					if(empty($Query['BooleanOperator']) || !in_array($Query['BooleanOperator'], self::BOOLEANOPERATOR_AVAILABLE_VALUES)) {
						$Queries[$k]['BooleanOperator'] = self::BOOLEANOPERATOR_DEFAULT;
					}
					if(empty($Query['FieldCode']) || !in_array($Query['FieldCode'], self::FIELDCODE_AVAILABLE_VALUES)) {
						$Queries[$k]['FieldCode'] =  self::FIELDCODE_DEFAULT;
					}
				}

				//format $SearchMode
				if(!in_array($SearchMode, self::SEARCHMODE_AVAILABLE_VALUES)) {
					$SearchMode = self::SEARCHMODE_DEFAULT;
				}
				//format $ResultsPerPage
				$ResultsPerPage = intval($ResultsPerPage);
				if(!$ResultsPerPage) {
					$ResultsPerPage = self::RESULTSPERPAGE_DEFAULT;
				}
				if ($ResultsPerPage > self::RESULTSPERPAGE_MAX) {
					$ResultsPerPage = self::RESULTSPERPAGE_MAX;
				}
				//format $PageNumber
				$PageNumber = intval($PageNumber);

				//format $Sort
				if(!in_array($Sort, self::SORT_AVAILABLE_VALUES)) {
					$Sort = self::SORT_DEFAULT;
				}

				//format HighLight
				if(!in_array($Highlight, self::HIGHLIGHT_AVAILABLE_VALUES)) {
					$Highlight = self::HIGHLIGHT_DEFAULT;
				}

				//format $IncludeFacets
				if(!in_array($IncludeFacets, self::INCLUDEFACETS_AVAILABLE_VALUES)) {
					$IncludeFacets = self::INCLUDEFACETS_DEFAULT;
				}

				//TODO format $FacetFilters

				//format $View
				if(!in_array($View, self::VIEW_AVAILABLE_VALUES)) {
					$View = self::VIEW_DEFAULT;
				}

				//TODO format $Actions

				//TODO format $Limiters

				//TODO format $Expanders

				//TODO format $PublicationId

				//TODO format $RelatedContent

				//format $AutoSuggest
				if(!in_array($AutoSuggest, self::AUTOSUGGEST_AVAILABLE_VALUES)) {
					$AutoSuggest = self::AUTOSUGGEST_DEFAULT;
				}

				//format $AutoCorrect
				if(!in_array($AutoCorrect, self::AUTOCORRECT_AVAILABLE_VALUES)) {
					$AutoCorrect = self::AUTOCORRECT_DEFAULT;
				}

				//format $IncludeImageQuickView
				if(!in_array($IncludeImageQuickView, self::INCLUDEIMAGEQUICKVIEW_AVAILABLE_VALUES)) {
					$IncludeImageQuickView = self::INCLUDEIMAGEQUICKVIEW_DEFAULT;
				}

				$this->raw_params = [
						'SearchCriteria'	=> [
								'Queries'			=> $Queries,
								'SearchMode'		=> $SearchMode,
								'IncludeFacets'		=> $IncludeFacets,
								'FacetFilters'		=> $FacetFilters,
								'Limiters'			=> $Limiters,
								'Expanders'			=> $Expanders,
								'Sort'				=> $Sort,
								'PublicationId'		=> $PublicationId,
								'RelatedContent'	=> $RelatedContent,
								'AutoSuggest'		=> $AutoSuggest,
								'AutoCorrect'		=> $AutoCorrect,
						],
						'RetrievalCriteria'	=> [
								'View'					=>	$View,
								'ResultsPerPage'		=> $ResultsPerPage,
								'PageNumber'			=> $PageNumber,
								'Highlight'				=> $Highlight,
								'IncludeImageQuickView'	=> $IncludeImageQuickView,
						],
						'Actions'	=> $Actions,
				];

				$this->curl_requests[] = json_encode(encoding_normalize::utf8_normalize($this->raw_params));
				return true;
	}


	public function run_search() {

		if(empty($this->curl_requests)) {
			return false;
		}
		$cs = $this->createsession();
		if(!$cs) {
			return false;
		}

		$this->reset_result();
		$this->reset_errors();

		$this->curl_handler->reset();
		$this->curl_handler->set_mode(multicurl::MODE_MULTI);
		$this->curl_url = $this->ws_url.self::SEARCH_PATH;
		$this->curl_headers[CURLOPT_HTTPHEADER] = self::CURL_HTTPHEADER;
		$this->curl_headers[CURLOPT_HTTPHEADER][] = self::AUTH_HEADER_KEY.': '.$this->auth_token;
		$this->curl_headers[CURLOPT_HTTPHEADER][] = self::SESSION_HEADER_KEY.': '.$this->session_token;
		foreach($this->curl_requests as $curl_request) {
			$this->curl_handler->add_post(
					$this->curl_url,
					$curl_request,
					$this->curl_headers
					);
		}

		$this->curl_handler->run();
		$this->curl_responses = $this->curl_handler->get_responses();
		if(count($this->curl_responses)) {
			foreach($this->curl_responses as $curl_response) {

				if ($curl_response['headers']['Status-Code']!='200') {

					$this->result[$curl_response['id']]['Status'] = ['curl'=>$curl_response['headers']['Status']];
					$this->result[$curl_response['id']]['Content'] = [];

				} else {

					$response = json_decode($curl_response['body'], true);
					$this->result[$curl_response['id']]['Status'] = '200';
					$this->result[$curl_response['id']]['Content'] = $response;
				}
			}
		}
		$this->curl_requests = [];
		return;
	}


	/**
	 * Lecture token authentification
	 *
	 * @return string auth_token
	 */
	public function get_auth_token(){
		return $this->auth_token;
	}


	/**
	 * Lecture token session
	 *
	 * @return string session_token
	 */
	public function get_session_token(){
		return $this->session_token;
	}

	/**
	 * Lecture infos
	 *
	 * @return array info
	 */
	public function get_info(){
		return static::$info;
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

}
