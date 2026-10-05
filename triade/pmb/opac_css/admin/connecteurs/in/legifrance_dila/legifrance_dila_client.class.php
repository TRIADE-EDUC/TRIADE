<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: legifrance_dila_client.class.php,v 1.5 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "{$class_path}/multicurl.class.php";


class legifrance_dila_client {

	
	//URL d'accès à l'API
	const WSURL_DEFAULT = "https://api.aife.economie.gouv.fr/dila/legifrance-beta/lf-engine-app";
	//URL de demande de token d'authentification
	const OAUTH_TOKEN_ENDPOINT_DEFAULT = "https://oauth.aife.economie.gouv.fr/api/oauth/token";
	const GRANT_TYPE = "client_credentials";
	const SCOPE = "openid";
	
	const CURL_OAUTH_HTTPHEADERS = [
			'Connection: keep-alive',
			'Accept: application/json',
			'Content-Type: application/x-www-form-urlencoded',
	];
	
	const CURL_HTTPHEADERS = [
			'Connection: keep-alive',
			'Accept: application/json',
			'Content-Type: application/json',
	];
	const AUTH_HEADER_KEY = "Authorization: Bearer ";
	
	
	//Paramètres méthode : Liste paginée des codes
	//Chemin
	const LIST_CODE_PATH = "/list/code";
	//Nombre maximum de résultats par page
	const LIST_CODE_MAX_PAGESIZE = 100;
	//Liste des tris possibles
	const LIST_CODE_SORT_AVAILABLE_VALUES = ["TITLE_ASC"];
	//Tri par défaut
	const LIST_CODE_SORT_DEFAULT = "TITLE_ASC";
	//Liste des états juridiques
	const LIST_CODE_STATE_AVAILABLE_VALUES = ["VIGUEUR","ABROGE","ABROGE_DIFF","VIGUEUR_DIFF","VIGUEUR_ETEN","PERIME","ANNULE","MODIFIE","DISJOINT","SUBSTITUE","TRANSFERE","INITIALE","MODIFIE_MORT_NE","SANS_ETAT","DENONCE","REMPLACE","VIGUEUR_NON_ETEN"];
	//Liste des états juridiques filtrés par défaut
	const LIST_CODE_STATE_DEFAULT = [];
	
	//Paramètres méthode : Recherche générique des documents indexés
	//Chemin
	const SEARCH_PATH = "/search";
	//Liste des fonds
	const SEARCH_FOND_AVAILABLES_VALUES = ["ALL","ACCO","CETAT","CIRC","CNIL","CODE_DATE","CODE_ETAT","CONSTIT","JORF","JURI","KALI","LODA_DATE","LODA_ETAT"];
	//Fond par défaut
	const SEARCH_FOND_DEFAULT = "ALL";
	//Liste des opérateurs
	const SEARCH_OPERATEUR_AVAILABLE_VALUES = ["ET","OU"];
	//Opérateur par défaut
	const SEARCH_OPERATEUR_DEFAULT = "ET";
	//Liste des types de recherche
	const SEARCH_TYPERECHERCHE_AVAILABLE_VALUES = ["UN_DES_MOTS","EXACTE","TOUS_LES_MOTS_DANS_UN_CHAMP","AUCUN_DES_MOTS"];
	//Type de recherche par défaut
	const SEARCH_TYPERECHERCHE_DEFAULT = "UN_DES_MOTS";
	//Liste des champs de recherche par fonds
	const SEARCH_TYPECHAMP_AVAILABLE_VALUES = [
			"ALL"			=> ["ALL","TITLE"],
			"ACCO"			=> ["ALL","TITLE","RAISON_SOCIALE","IDCC"],
			"CETAT"			=> ["ALL","TITLE","NOR","NUM_DEC","ABSTRATS","NUM_AFFAIRE","TEXTE","RESUMES"],
			"CIRC"			=> ["ALL","TITLE","NOR","RESUME_CIRC","TEXTE_REF"],
			"CNIL"			=> ["ALL","TITLE"],
			"CODE_DATE"		=> ["ALL","TITLE","TABLE","NUM_ARTICLE","ARTICLE"],
			"CODE_ETAT"		=> ["ALL","TITLE","TABLE","NUM_ARTICLE","ARTICLE"],
			"CONSTIT"		=> ["ALL","TITLE","NOR","NUM_DEC","TEXTE"],
			"JORF"			=> ["ALL","TITLE","NOR","NUM","NUM_ARTICLE","ARTICLE","VISA","NOTICE","VISA_NOTICE","TRAVAUX_PREP","SIGNATURE","NOTA"],
			"JURI"			=> ["ALL","TITLE","ABSTRATS","TEXTE","RESUMES"],
			"KALI"			=> ["ALL","TITLE","IDCC","MOTS_CLES","ARTICLE"],
			"LODA_DATE"		=> ["ALL","TITLE","NOR","NUM","NUM_ARTICLE","ARTICLE","VISA","NOTICE","VISA_NOTICE","TRAVAUX_PREP","SIGNATURE","NOTA"],
			"LODA_ETAT"		=> ["ALL","TITLE","NOR","NUM","NUM_ARTICLE","ARTICLE","VISA","NOTICE","VISA_NOTICE","TRAVAUX_PREP","SIGNATURE","NOTA"]
	];
	//Champ de recherche par défaut
	const SEARCH_TYPECHAMP_DEFAULT = "ALL";
	//Liste des facettes de recherche par fonds
	const SEARCH_FACETTE_AVAILABLE_VALUES = [
			"ALL" => [
					"FOND" => [
							"type" 	=> "valeurs",
							"strict" => "Y",
							"ref"	=> "SEARCH_FACETTE_ALL_FOND",
					],
			],
			"ACCO"	=> [
					"THEME"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_ACCO_THEME",
					],
					"SIGNATAIRE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_ACCO_SIGNATAIRE",
					],
			],
			"CETAT"	=> [
					"JURIDICTION_NATURE"	=> [
							"type" 	=> "multiValeurs",
							"ref"	=> "SEARCH_FACETTE_CETAT_JURIDICTION_NATURE",
					],
					"PUBLICATION_RECUEIL"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CETAT_PUBLICATION_RECUEIL",
					],
			],
			"CIRC"	=> [
					"DOMAINE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CIRC_DOMAINE",
					],
					"MOTS_CLEFS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CIRC_MOTS_CLEFS",
					],
					"OPPOSABILITE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CIRC_OPPOSABILITE",
					],
					"MIN_DEPOSANT"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CIRC_MIN_DEPOSANT",
					],
					"MIN_CONCERNE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CIRC_MIN_CONCERNE",
					],
			],
			"CNIL"	=> [
			],
			"CODE_DATE"	=> [
					"NOM_CODE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CODE_NOM_CODE",
					],
			],
			"CODE_ETAT"	=> [
					"NOM_CODE"	=> [
							"type" 	=> "multiValeurs",
							"ref"	=> "SEARCH_FACETTE_CODE_NOM_CODE",
					],
					"TEXT_LEGAL_STATUS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CODE_STATUS",
					],
					"ARTICLE_LEGAL_STATUS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CODE_STATUS",
					],
			],
			"CONSTIT"	=> [
					"NATURE_CONSTIT"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_NATURE_CONSTIT",
					],
					"NATURE_NORME_AUTRE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_NATURE_NORME_AUTRE",
					],
					"SOLUTION_CONSTIT"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_SOLUTION_CONSTIT",
					],
					"NATURE_ELCT"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_NATURE_ELCT",
					],
					"SOLUTION_ELECT"	=> [
							"type" 		=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_SOLUTION_ELECT",
					],
					"NATURE_AUTRE"	=> [
							"type" 		=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_NATURE_AUTRE",
					],
					"SOLUTION_AUTRE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_CONSTIT_SOLUTION_AUTRE",
					],
			],
			"JORF"	=> [
					"NATURE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JORF_NATURE",
					],
					"EMETTEUR"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JORF_EMETTEUR",
					],
					"AUTORITE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JORF_AUTORITE",
					],
					"NOR"	=> [
							"type" 	=> "valeurs",
					],
					"DATE_VERSION"	=> [
							"type" 		=> "date",
					],
					"DATE_PUBLICATION"	=> [
							"type" 		=> "date",
					],
			],
			"JURI"	=> [
					"PREMIER_DEGRE_TYPE_JURIDICTION"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JURI_PREMIER_DEGRE_TYPE_JURIDICTION",
					],
					"CASSATION_FORMATION"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JURI_CASSATION_FORMATION",
					],
					"CASSATION_TYPE_PUBLICATION_BULLETIN"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JURI_CASSATION_TYPE_PUBLICATION_BULLETIN",
					],
					"JURIDICTION_JUDICIAIRE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JURI_JURIDICTION_JUDICIAIRE",
					],
					"CASSATION_NATURE_DECISION"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JURI_CASSATION_NATURE_DECISION",
					],
					"APPEL_SIEGE_APPEL"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_JURI_APPEL_SIEGE_APPEL",
					]
			],
			"KALI"	=> [
					"ACTIVITE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_KALI_ACTIVITE",
					],
					"TEXTE_BASE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_KALI_TEXTE_BASE",
					],
					"LEGAL_STATUS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_KALI_STATUS",
					],
					"ARTICLE_LEGAL_STATUS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_KALI_STATUS",
					],
			],
			"LODA_DATE"	=> [
					"NATURE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_LODA_NATURE",
					],
			],
			"LODA_ETAT"	=> [
					"TEXT_NATURE"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_LODA_NATURE",
					],
					"TEXT_LEGAL_STATUS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_LODA_STATUS",
					],
					"ARTICLE_LEGAL_STATUS"	=> [
							"type" 	=> "valeurs",
							"ref"	=> "SEARCH_FACETTE_LODA_STATUS",
					],
			],
	];
	//Facette par défaut
	const SEARCH_FACETTE_DEFAULT = [];
	//Liste des tris par_fonds
	const SEARCH_SORT_AVAILABLE_VALUES = [
			"ALL"		=> [""],
			"ACCO"		=> ["","PERTINENCE","DATE_DESC","DATE_ASC"],
			"CETAT"		=> ["","PERTINENCE","DATE_DESC","DATE_ASC"],
			"CIRC"		=> ["","PERTINENCE","SIGNATURE_DATE_DESC","SIGNATURE_DATE_ASC"],
			"CNIL"		=> [""],
			"CODE_DATE"	=> [""],
			"CODE_ETAT"	=> [""],
			"CONSTIT"	=> ["","PERTINENCE","DATE_DESC","DATE_ASC"],
			"JORF"		=> ["","PERTINENCE","SIGNATURE_DATE_DESC","SIGNATURE_DATE_ASC","PUBLICATION_DATE_DESC","PUBLICATION_DATE_ASC"],
			"JURI"		=> ["","PERTINENCE","DATE_DESC","DATE_ASC"],
			"KALI"		=> ["","PERTINENCE","MODIFICATION_DATE_DESC","PUBLICATION_DATE_DESC","PUBLICATION_DATE_ASC"],
			"LODA_DATE"	=> ["","PERTINENCE","SIGNATURE_DATE_DESC","SIGNATURE_DATE_ASC"],
			"LODA_ETAT"	=> ["","PERTINENCE","SIGNATURE_DATE_DESC","SIGNATURE_DATE_ASC"],
	];
	//Tri par défaut
	const SEARCH_SORT_DEFAULT = "";
	//Page par défaut
	const SEARCH_PAGENUMBER_DEFAULT = 1;
	//Nombre maximum de résultats par page
	const SEARCH_PAGESIZE_MAX = 100;
	//Types de paginations
	const SEARCH_TYPEPAGINATION_AVAILABLES_VALUES = ["DEFAUT","ARTICLE"];
	//Type de pagination par défaut
	const SEARCH_TYPEPAGINATION_DEFAULT = "DEFAUT";
	
	
	const CONSULT_TEXT_JURI_PATH = '/consult/juri';
	const CONSULT_TEXT_LEGI_PATH = '/consult/legiPart';
	const CONSULT_TEXT_JORF_PATH = '/consult/jorf';
	const CONSULT_TEXT_CODE_PATH = '/consult/code';
	const CONSULT_TEXT_KALI_PATH = '/consult/kaliText';
	const CONSULT_TEXT_LODA_PATH = '/consult/lawDecree';
	const CONSULT_TEXT_ACCO_PATH = '/consult/acco';
	const CONSULT_TEXT_CNIL_PATH = '/consult/cnil';
	const CONSULT_TEXT_CIRC_PATH = '/consult/circulaire';
	const CONSULT_ARTICLE_PATH = '/consult/getArticle';	
	
	
	protected $config_filename = __DIR__."/legifrance_dila.json";
	protected $config_filename_subst = __DIR__."/legifrance_dila_subst.json";
	protected $config = [];
	
	protected $ws_url = "";
	protected $oauth_token_endpoint = "";
	protected $client_id = "";
	protected $client_secret = "";
	protected $access_token = "";
	
	protected $curl_handler = false;
		
	protected $curl_requests = [];
	protected $curl_responses = [];
	
	protected $error = false;
	protected $error_msg = [];
	
	protected $result = [];
		
	
	/**
	 * 
	 * @param string $client_id
	 * @param string $client_secret
	 * @param string $ws_url
	 * @param string $oauth_token_endpoint
	 */
	public function __construct($client_id = '', $client_secret = '', $ws_url = '', $oauth_token_endpoint = '') {
		
		$this->get_config();
		
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		
		if($ws_url) {
			$this->ws_url = $ws_url;
		} else {
			$this->ws_url = self::WSURL_DEFAULT;
		}
		if($oauth_token_endpoint) {
			$this->oauth_token_endpoint = $oauth_token_endpoint;
		} else {
			$this->oauth_token_endpoint = self::OAUTH_TOKEN_ENDPOINT_DEFAULT;
		}
		$this->curl_handler = new multicurl();
		$this->curl_handler->set_external_configure_function('configurer_proxy_curl');
		$this->curl_handler->set_mode(multicurl::MODE_MULTI);
	}
	
	
	
	/**
	 *  Demande token access
	 * @return boolean
	 */
	public function query_access_token() {

		if('' !== $this->access_token ) {
			return true;
		}
		$curl_url = $this->oauth_token_endpoint;
		$curl_params = [
				"grant_type" => self::GRANT_TYPE,
				"client_id" => $this->client_id,
				"client_secret" => $this->client_secret,
				"scope" => self::SCOPE,
		];
		$curl_headers = [CURLOPT_HTTPHEADER => self::CURL_OAUTH_HTTPHEADERS];
		$this->curl_handler->set_mode(multicurl::MODE_MONO);
		
		$this->curl_handler->add_post(
				$curl_url,
				$curl_params,
				$curl_headers
				);
		
		$this->curl_handler->run();
		$this->curl_responses[0] = $this->curl_handler->get_responses()[0];
		//var_dump($this->curl_responses[0]);
		
		if($this->curl_responses[0]['headers']['Status-Code'] != '200') {
			$this->error = true;
			$this->error_msg[] = __METHOD__.' => invalid request';
			return false;
		}
		$response_body = json_decode($this->curl_responses[0]['body'], true);
		//var_dump($response_body);
		
		if(is_null($response_body)) {
			$this->error = true;
			$this->error_msg[] = __METHOD__.' => json response error';
			return false;
		}
		if(empty($response_body['access_token'])) {
			$this->error = true;
			$this->error_msg[] = __METHOD__.' => no access_token provided';
			return false;
		}
		$this->access_token = $response_body['access_token'];
		return true;
		
	}
		

	/**
	 * 
	 * @param string $codeName 
	 * @param number $pageNumber
	 * @param int $pageSize
	 * @param string $sort
	 * @param array $states
	 * @return boolean
	 */
	public function query_list_code($codeName='', $pageNumber=1, $pageSize=self::LIST_CODE_MAX_PAGESIZE, $sort=self::LIST_CODE_SORT_DEFAULT, $states =[]) {
		
		$this->query_access_token();
		if(!$this->access_token) {
			return false;
		}
		
		$this->reset_result();
		$this->reset_errors();
		
		$codeName = trim($codeName);
		$pageNumber = intval($pageNumber);
		if(!$pageNumber) {
			$pageNumber=1;
		}
		if($pageSize > self::LIST_CODE_MAX_PAGESIZE) {
			$pageSize = self::LIST_CODE_MAX_PAGESIZE;
		}
		if(!in_array($sort, self::LIST_CODE_SORT_AVAILABLE_VALUES)) {
			$sort = self::LIST_CODE_SORT_DEFAULT;
		}
		if(!is_array($states)) {
			$states = self::LIST_CODE_STATE_DEFAULT;
		} else {
			$tmp_states = [];
			foreach($states as $state) {
				if(in_array($state, self::LIST_CODE_STATE_AVAILABLE_VALUES)) {
					$tmp_states[]=$state;
				}
			}
			$states =$tmp_states;				
		}
		
		$curl_url = $this->ws_url.self::LIST_CODE_PATH;
		$raw_params = [
				'codeName'		=> $codeName,
				'pageNumber'	=> $pageNumber,
				'pageSize'		=> $pageSize,
				'sort'			=> $sort,
				'states'		=> $states,
		];
		$curl_params = json_encode(encoding_normalize::utf8_normalize($raw_params));
		//var_dump($curl_params);
		$curl_headers = [CURLOPT_HTTPHEADER => self::CURL_HTTPHEADERS];
		$curl_headers[CURLOPT_HTTPHEADER][] = self::AUTH_HEADER_KEY.$this->access_token;
		
		$this->curl_handler->reset();
		$this->curl_handler->set_mode(multicurl::MODE_MONO);
		$this->curl_handler->add_post(
				$curl_url,
				$curl_params,
				$curl_headers
				);
		
		$this->curl_handler->run();
		$this->curl_responses[0] = $this->curl_handler->get_responses()[0];
		//var_dump($this->curl_responses[0]);
		
		if($this->curl_responses[0]['headers']['Status-Code'] != '200') {
			$this->error = true;
			$this->error_msg[] = __METHOD__.' => invalid request';
			return false;
		}
		$response_body = json_decode($this->curl_responses[0]['body'], true);
		
		if(is_null($response_body) || empty($response_body)) {
			$this->error = true;
			$this->error_msg[] = __METHOD__.' => json response error';
			return false;
		}
		$this->result = $response_body;
		return true;

	}
	
	
/*
 Structure objet Recherche :
[
  "fond" => "ALL",  							// obligatoire, Fonds sur lequel appliquer la recherche, 1 des valeurs de SEARCH_FOND_AVAILABLES_VALUES
    "recherche" =>								// obligatoire
        "champs" => [],							// obligatoire, voir structure objet champs 
        "filtres" => [],						// optionnel, voir structure objet filtres
        "operateur" => "ET",					// obligatoire, Opérateur entre les champs de recherche, 1 des valeurs de SEARCH_OPERATEUR_AVAILABLE_VALUES
        "pageNumber" => 1,						// obligatoire, Numéro de la page à consulter
        "pageSize" => 100,						// obligatoire, Nombre d'éléments par page (max=100)
        "sort" => "",							// optionnel, selon fonds, 1 des valeurs de SEARCH_SORT_AVAILABLE_VALUES, = "" si non précisé
        "typePagination" => "DEFAUT"			// optionnel, selon fonds, 1 des valeurs de SEARCH_TYPEPAGINATION_AVAILABLES_VALUES, = "DEFAUT" si non précisé
    ]
]
Structure objet champs :
[
    "champs" => [
        [
            "typeChamp" => "ALL",                         // obligatoire, selon fonds, type de champ, 1 des valeurs de SEARCH_TYPECHAMP_AVAILABLE_VALUES
            "operateur" => "ET",                          // obligatoire, opérateur entre les critères, 1 des valeurs de SEARCH_OPERATEUR_AVAILABLE_VALUES
            "criteres" => [
                [
                    "criteres" => [                       // optionnel, sous-critères
                        "..."
                    ],
                    "operateur" => "ET",                  // obligatoire, Opérateur entre les sous-critères de recherche
                    "proximite" => 1,                     // optionnel, Proximité maximum entre les mots du champ valeur.
                    "typeRecherche" => "UN_DES_MOTS",     // obligatoire, Type de recherche effectuée, 1 des valeurs de SEARCH_TYPERECHERCHE_AVAILABLE_VALUES
                    "valeur" => "VALEUR"                  // obligatoire, Mot(s)/expression recherchés
                ]
            ]
        ]
    ]
];

Structure objet filtres
[
    "filtres" => [
        [
            "facette" => "FACETTE1",        // obligatoire, selon fonds, 
            "valeurs" => [                  // Liste des valeurs du filtre dans le cas d'un filtre textuel ou d'un filtre via option textuelle
                "VAL1",
                "VAL2",
                "VAL3",
            ],
            "multiValeurs" => [             // Map des sous-valeurs d'une valeur de filtre dans le cas d'un filtre par option texte. La clé doit être la valeur correspondante au parent dans la liste 'valeurs'
                "VAL1" => [
                    "VAL1-1",
                    "VAL1-2",
                ],
                "VAL2" => [
                      "VAL2-1",
                ],
            ],
            "singleDate" => "AAAA-MM-JJ",   // Si filtre de type date unique
            "dates" => [                    // Si filtre de type période
                "start" => "AAAA-MM-JJ",
                "end" => "AAAA-MM-JJ"
            ]
        ],

    ],
];

*/
	
	public function add_search_query(
		$fond = self::SEARCH_FOND_DEFAULT,
		$champs = [],
		$filtres = [],
		$operateur = 'ET',
		$pageNumber = self::SEARCH_PAGENUMBER_DEFAULT,
		$pageSize = self::SEARCH_PAGESIZE_MAX,
		$sort = self::SEARCH_SORT_DEFAULT,
		$typePagination = self::SEARCH_TYPEPAGINATION_DEFAULT
	) {
		
		//test $fond
		if(!in_array($fond, self::SEARCH_FOND_AVAILABLES_VALUES)) {
			$this->error = true;
			$this->error_msg[] = __METHOD__." => Unknown fond : {$fond}";
			return false;
		}
		
		//test $champs
		$checked_champs = $this->check_search_champs($fond, $champs);
		if( empty($checked_champs) ) {
			return false;
		}
		//test $filtres
		$checked_filtres = $this->check_search_filtres($fond, $filtres);
		if( false === $checked_filtres ) {
			return false;
		}
		//test $operateur
		if(!in_array($operateur, self::SEARCH_OPERATEUR_AVAILABLE_VALUES)) {
			$operateur = legifrance_dila_client::SEARCH_OPERATEUR_DEFAULT;
		}
		
		//test $pageNumber
		$pageNumber = intval($pageNumber);
		if(!$pageNumber) {
			$pageNumber = self::SEARCH_PAGENUMBER_DEFAULT;
		}
		//test $pageSize
		$pageSize = intval($pageSize);
		if(self::SEARCH_PAGESIZE_MAX < $pageSize) {
			$pageSize = self::SEARCH_PAGESIZE_MAX;
		}
		
		//test $sort 
		if(!in_array($sort, self::SEARCH_SORT_AVAILABLE_VALUES[$fond])) {
			$sort = self::SEARCH_SORT_DEFAULT;
		}
		
		//test $typePagination
		if(!in_array($typePagination, self::SEARCH_TYPEPAGINATION_AVAILABLES_VALUES)) {
			$typePagination = self::SEARCH_TYPEPAGINATION_DEFAULT;
		}
		
		$raw_params = [
				"fond"						=> $fond,
				"recherche"		=> [
						"champs" 			=> $checked_champs,
						"filtres" 			=> $checked_filtres,
						"operateur" 		=> $operateur,
						"pageNumber" 		=> $pageNumber,
						"pageSize"			=> $pageSize,
						"sort" 				=> $sort,
						"typePagination" 	=> $typePagination,
				],
		];
		//print_r($raw_params);
		
		$json_request = json_encode(encoding_normalize::utf8_normalize($raw_params));
		//print $json_request;
		$path = self::SEARCH_PATH;
		$this->curl_requests[] = [
				'path' 			=> $path,
				'json_request'	=> $json_request
		];
		return true;
	}
	
	/**
	 * Vérification de la structure des champs de recherche
	 * 
	 * @param string $fond
	 * @param array $champs
	 * @return mixed : array or false on error
	 * 
		Structure objet champs :
		[
		    "champs" => [
		        [
		            "typeChamp" => "ALL",                         // obligatoire, selon fonds, type de champ, 1 des valeurs de SEARCH_TYPECHAMP_AVAILABLE_VALUES
		            "operateur" => "ET",                          // obligatoire, opérateur entre les critères, 1 des valeurs de SEARCH_OPERATEUR_AVAILABLE_VALUES
		            "criteres" => [
		                [
		                    "criteres" => [                       // optionnel, sous-critères, non géré
		                        "..."
		                    ],
		                    "operateur" => "ET",                  // obligatoire, Opérateur entre les critères de recherche
		                    "proximite" => 1,                     // optionnel, Proximité maximum entre les mots du champ valeur.
		                    "typeRecherche" => "UN_DES_MOTS",     // obligatoire, Type de recherche effectuée, 1 des valeurs de SEARCH_TYPERECHERCHE_AVAILABLE_VALUES
		                    "valeur" => "VALEUR"                  // obligatoire, Mot(s)/expression recherchés
		                ]
		            ]
		        ]
		    ]
		];
	 */
	protected function check_search_champs ($fond, $champs) {
		
		$checked_champs = [];
		foreach($champs as $kch=>$champ) {
			
			if( empty($champ['typeChamp']) || !in_array($champ['typeChamp'], self::SEARCH_TYPECHAMP_AVAILABLE_VALUES[$fond]) ) {
				$checked_champs[$kch]['typeChamp'] = self::SEARCH_TYPECHAMP_DEFAULT;
			} else {
				$checked_champs[$kch]['typeChamp'] = $champ['typeChamp'];
			}
			
			if( empty($champ['operateur']) || !in_array($champ['operateur'], self::SEARCH_OPERATEUR_AVAILABLE_VALUES) ) {
				$checked_champs[$kch]['operateur'] = self::SEARCH_OPERATEUR_DEFAULT;
			} else {
				$checked_champs[$kch]['operateur'] = $champ['operateur'];
			}
			if( empty($champ['criteres'])) {
				$this->error = true;
				$this->error_msg[] = __METHOD__." => empty criteres";
				return false;
			}
			foreach ($champ['criteres'] as $kcr=>$critere) {
				
				if( empty($critere['operateur']) || !in_array($critere['operateur'], self::SEARCH_OPERATEUR_AVAILABLE_VALUES) ) {
					$checked_champs[$kch]['criteres'][$kcr]['operateur'] = self::SEARCH_OPERATEUR_DEFAULT;
				} else {
					$checked_champs[$kch]['criteres'][$kcr]['operateur'] = $critere['operateur'];
				}
				
				if( !empty($critere['proximite'])) {
					$checked_champs[$kch]['criteres'][$kcr]['proximite'] = intval($critere['proximite']);
				}
				
				if( empty($critere['typeRecherche']) || !in_array($critere['typeRecherche'], self::SEARCH_TYPERECHERCHE_AVAILABLE_VALUES) ) {
					$checked_champs[$kch]['criteres'][$kcr]['typeRecherche'] = self::SEARCH_TYPERECHERCHE_DEFAULT;
				} else {
					$checked_champs[$kch]['criteres'][$kcr]['typeRecherche'] = $critere['typeRecherche'];
				}
				
				if( empty($critere['valeur'])) {
					$checked_champs[$kch]['criteres'][$kcr]['valeur'] = "";
				} else {
					$checked_champs[$kch]['criteres'][$kcr]['valeur'] = $critere['valeur'];
				}
			}
		}
		return $checked_champs;
	}
	
	
	/**
	 * 
	 * Vérification de la structure des filtres de recherche
	 * 
	 * @param string $fond
	 * @param array $filtres
	 * @return mixed : array or false on error
	 * 
		Structure objet filtres
		[
		    "filtres" => [
		        [
		            "facette" => "FACETTE1",        // obligatoire, selon fonds, 
		            "valeurs" => [                  // Liste des valeurs du filtre dans le cas d'un filtre textuel ou d'un filtre via option textuelle
		                "VAL1",
		                "VAL2",
		                "VAL3",
		            ],
		            "multiValeurs" => [             // Map des sous-valeurs d'une valeur de filtre dans le cas d'un filtre par option texte. La clé doit être la valeur correspondante au parent dans la liste 'valeurs'
		                "VAL1" => [
		                    "VAL1-1",
		                    "VAL1-2",
		                ],
		                "VAL2" => [
		                      "VAL2-1",
		                ],
		            ],
		            "singleDate" => "AAAA-MM-JJ",   // Si filtre de type date unique
		            "dates" => [                    // Si filtre de type période
		                "start" => "AAAA-MM-JJ",
		                "end" => "AAAA-MM-JJ"
		            ]
		        ],
		
		    ],
		];
	 * 
	 */
	protected function check_search_filtres ($fond, $filtres) {
		
		$checked_filtres = [];
		foreach($filtres as $kfi=>$filtre) {
			
			if( empty($filtre['facette']) ) {
				$this->error = true;
				$this->error_msg[] = __METHOD__." => empty facette";
				return false;
			}
			$facette = $filtre['facette'];
			if(  empty(self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]) ) {
				$this->error = true;
				$this->error_msg[] = __METHOD__." => facette '{$facette}' doesn't exists for fond '{$fond}'.";
				return false;
			}
			$type_facette = self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['type'];
			switch($type_facette) {
				
				case 'valeurs' :
					
					if( empty($filtre['valeurs'])) {
						break;
					}
					if( !is_array($filtre['valeurs'])) {
						break;
					}
					$tmp_valeurs = $filtre['valeurs'];
					if( !empty(self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['strict']) && !empty(self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['ref']) ) {
						$tmp_valeurs = array_intersect($tmp_valeurs, $this->config[self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['ref']]['valeurs']);
						if(empty($tmp_valeurs)) {
							$this->error = true;
							$this->error_msg[] = __METHOD__." => wrong valeurs for facette '{$type_facette}'.";
							return false;
						}
					}					
					if(!empty($tmp_valeurs)) {
						$checked_filtres[$kfi]['facette'] = $facette;
						$checked_filtres[$kfi]['valeurs'] = $tmp_valeurs;
					}
					
					break;
					
				case 'multiValeurs' : 
					
					if( empty($filtre['valeurs'])) {
						break;
					}
					if( !is_array($filtre['valeurs'])) {
						break;
					}
					$tmp_valeurs = $filtre['valeurs'];
					
					if( !empty(self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['strict']) && !empty(self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['ref']) ) {
						$tmp_valeurs = array_intersect($tmp_valeurs, $this->config[self::SEARCH_FACETTE_AVAILABLE_VALUES[$fond][$facette]['ref']]['valeurs']);
						if(empty($tmp_valeurs)) {
							$this->error = true;
							$this->error_msg[] = __METHOD__." => wrong valeurs for facette '{$type_facette}'.";
							return false;
						}
					}	
					
					if(!empty($tmp_valeurs)) {
						$checked_filtres[$kfi]['facette'] = $facette;
						$checked_filtres[$kfi]['valeurs'] = $tmp_valeurs;
					}
					
					if(empty($filtre['multiValeurs'])) {
						break;
					}
					if( !is_array($filtre['multiValeurs'])) {
						break;
					}
					$tmp_multiValeurs = $filtre['multiValeurs'];
					
					foreach($tmp_multiValeurs as $kmu=>$tmp_multiValeur) {
						if(in_array($kmu, $tmp_valeurs) && is_array($tmp_multiValeur) ) {
							$checked_filtres[$kfi]['multiValeurs'] = [$kmu=>$tmp_multiValeur];
						}
					}
						
					break;
					
				case 'date' :
					
					//TODO check format $filtre['singleDate']
					if( !empty($filtre['singleDate'])) {
						$checked_filtres[$kfi]['facette'] = $facette;
						$checked_filtres[$kfi]['singleDate'] = $filtre['singleDate'];
						break;
					}
					
					if(empty($filtre['dates'])) {
						break;
					}
					$dates_start = '';
					//TODO check format $filtre['dates']['start']
					if( !empty($filtre['dates']['start']) ) {
						$dates_start = $filtre['dates']['start'];
					}
					$dates_end = '';
					//TODO check format $filtre['dates']['end']
					if( !empty($filtre['dates']['end']) ) {
						$dates_end = $filtre['dates']['end'];
					}
					if($dates_start || $dates_end) {
						$checked_filtres[$kfi]['facette'] = $facette;
					
						if($dates_start) {
							$checked_filtres[$kfi]['facette']['dates']['start'] = $dates_start;
						}
						if($dates_end) {
							$checked_filtres[$kfi]['facette']['dates']['end'] = $dates_end;
						}
					}
					break;
			}
			
		}
		return $checked_filtres;
	}
	
	
	/**
	 * 
	 */
	public function add_consult_text_juri_query($textId) {
		
		$path = self::CONSULT_TEXT_JURI_PATH;
		$raw_params['textId'] = $textId;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
	
	/**
	 *
	 */
	public function add_consult_text_legi_query($textId, $date) {
		
		//TODO verifier format date (AAAA-MM-JJ ou date-time sur 13 car.)
		
		$path = self::CONSULT_TEXT_LEGI_PATH;
		$raw_params['textId'] = $textId;
		$raw_params['date'] = $date;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
	
	/**
	 *
	 */
	public function add_consult_text_jorf_query($textCid) {
				
		$path = self::CONSULT_TEXT_JORF_PATH;
		$raw_params['textCid'] = $textCid;
		
		return $this->add_consult_query($path, $raw_params);

	}
	
		
	/**
	 *
	 */
	public function add_consult_text_code_query($date, $textId, $sctCid = "") {
		
		//TODO verifier format date (AAAA-MM-JJ ou date-time sur 13 car.)
		
		$path = self::CONSULT_TEXT_CODE_PATH;
		$raw_params['date'] = $date;
		$raw_params['textId'] = $date;
		if( !empty($sctCid) && is_string($sctCid) ) {
			$raw_params['sctCid'] = $sctCid;
		}
		return $this->add_consult_query($path, $raw_params);
	}
	
		
	/**
	 *
	 */
	public function add_consult_text_kali_query($id) {
		
		$path = self::CONSULT_TEXT_KALI_PATH;
		$raw_params['id'] = $id;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
	
	/**
	 *
	 */
	public function add_consult_text_loda_query($date, $textId) {
		
		//TODO verifier format date (AAAA-MM-JJ ou date-time sur 13 car.)

		$path = self::CONSULT_TEXT_LODA_PATH;
		$raw_params['date'] = $date;
		$raw_params['textId'] = $date;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
	
	/**
	 *
	 */
	public function add_consult_text_acco_query($id) {
		
		$path = self::CONSULT_TEXT_ACCO_PATH;
		$raw_params['id'] = $id;
		
		return $this->add_consult_query($path, $raw_params);
	}
		
	
	/**
	 *
	 */
	public function add_consult_text_cnil_query($textId) {
		
		$path = self::CONSULT_TEXT_CNIL_PATH;
		$raw_params['textId'] = $textId;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
		
	/**
	 *
	 */
	public function add_consult_text_circ_query($id) {
			
		$path = self::CONSULT_TEXT_CIRC_PATH;
		$raw_params['id'] = $id;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
		
	/**
	 *
	 */
	public function add_consult_article_query($id) {
				
		$path = self::CONSULT_ARTICLE_PATH;
		$raw_params['id'] = $id;
		
		return $this->add_consult_query($path, $raw_params);
	}
	
	
	/**
	 * 
	 */
	protected function add_consult_query($path, $raw_params = []) {
		
		$json_request = json_encode(encoding_normalize::utf8_normalize($raw_params));
		
		$this->curl_requests[] = [
				'path'			=> $path, 
				'json_request'	=> $json_request,
				];
		return true;
	}
	
	
	/**
	 * Lancement requetes
	 * 
	 * @return boolean
	 */
	public function run_queries() {
		
		if(empty($this->curl_requests)) {
			return false;
		}
		$this->query_access_token();
		if(!$this->access_token) {
			return false;
		}
		
		$this->reset_result();
		$this->reset_errors();
		
		$this->curl_handler->reset();
		$this->curl_handler->set_mode(multicurl::MODE_MULTI);
		$curl_headers[CURLOPT_HTTPHEADER] = self::CURL_HTTPHEADERS;
		$curl_headers[CURLOPT_HTTPHEADER][] = self::AUTH_HEADER_KEY.$this->access_token;
		foreach($this->curl_requests as $curl_request) {
			$this->curl_handler->add_post(
					$this->ws_url.$curl_request['path'],
					$curl_request['json_request'],
					$curl_headers
					);
		}
		
		$this->curl_handler->run();
		$this->curl_responses = $this->curl_handler->get_responses();
				
		if(count($this->curl_responses)) {
			foreach($this->curl_responses as $curl_response) {
						
				if ($curl_response['headers']['Status-Code']!='200') {
					
					$this->result[$curl_response['id']]['Status'] = $curl_response['headers']['Status'];
					$this->result[$curl_response['id']]['Content'] = [];
					
				} else {
					
					$response = json_decode($curl_response['body'], true);
					
					$this->result[$curl_response['id']]['Status'] = '200';
					$this->result[$curl_response['id']]['Content'] = $response;
				}
			}
		}
		$this->curl_requests = [];
		return true;
	}
	
	
	/**
	 *
	 * @param string $client_id
	 */
	public function set_client_id($client_id) {
		$this->client_id = $client_id;
	}
	
	
	/**
	 *
	 * @param string $client_secret
	 */
	public function set_client_secret($client_secret) {
		$this->client_secret = $client_secret;
	}
		
	
	/**
	 * 
	 * @param string $ws_url
	 */
	public function set_ws_url($ws_url) {
		$this->ws_url = $ws_url;
	}
	
	
	/**
	 *
	 * @param string $oauth_token_endpoint
	 */
	public function set_oauth_token_endpoint($oauth_token_endpoint) {
		$this->oauth_token_endpoint = $oauth_token_endpoint;
	}
	
	
	/**
	 * Lecture token acces
	 *
	 * @return string access_token
	 */
	public function get_access_token(){
		return $this->access_token;
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

	public function get_config () {
		
		if(!empty($this->config)) {
			return $this->config;
		}
		$contents = '';
		$config_filename = $this->config_filename;
		$config_filename_subst = $this->config_filename_subst;
		
		if(is_readable($config_filename_subst)) {
			$contents = file_get_contents($config_filename_subst);
		}
		if(!$contents) {
			if(is_readable($config_filename)) {
				$contents = file_get_contents($config_filename);
			}
		}
		if(!$contents) {
			return $this->config;
		}
		$this->config = json_decode($contents, true);

		return $this->config;
	}
	
}