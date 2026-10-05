<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mirabel_client.class.php,v 1.2 2022/09/20 12:43:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once $class_path."/curl.class.php";

class mirabel_client {
	
    const API_URL_DEFAULT = 'https://reseau-mirabel.info/api';
	
    const DEFAULT_HEADERS = [
        'Accept'		=> 'application/json',
    ];
    
	protected $api_url = '';
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
	 * @param string api_key : Identifiant partenaire (optionnel)
	 * @param string api_url : URL de l'API (optionnel)
	 * 
	 * @return void
	 */
	public function __construct($api_key = '', $api_url = '') 
	{
		$this->api_key = $api_key;
		if($api_url) {
		    $this->api_url = $api_url;
		} else {
		    $this->api_url = mirabel_client::API_URL_DEFAULT;
		}
		$this->curl_channel = new Curl();
	}
	
	/**
	 * 
	 * @param string $api_key
	 * 
	 * @return void
	 */
	public function setApiKey($api_key) 
	{
		$this->api_key = $api_key;
	}
	
	
	/**
	 * Liste des accès sur une revue
	 * 
	 * @param string $issn : issn
	 * @param integer $mirabel_revue_id : Identifiant de revue Mir@bel 
	 * @param string $sudoc_ppn_id : Identifiant ppn Sudoc
	 * @param integer $worldcat_id : Identifiant Worldcat
	 * @param boolean $use_api_key : Utiliser la clé d'api 
	 * 
	 * @return boolean
	 */
	public function getAccesRevue (
	    $issn = '',
	    $mirabel_revue_id = 0,
	    $sudoc_ppn_id = '',
	    $worldcat_id = 0,
	    $use_api_key = false
		) 
	{
	    $this->resetErrors();
		$this->resetResult();
        $this->curl_params = [];
			
        //issn
        $tmp = '';
        if( is_string($issn) ) {
            $tmp = trim($issn);
        } 
        if( !empty($tmp) ) {
		    $this->curl_params['issn'] = $tmp;
		}
			
		//revueid
		$tmp = intval($mirabel_revue_id);
		if( !empty($tmp) ) {
		    $this->curl_params['revueid'] = $tmp;
		}
			
		//sudoc
		$tmp = '';
		if( is_string($sudoc_ppn_id) ) {
		    $tmp = trim($sudoc_ppn_id);
		} 
		if( !empty($tmp) ) {
		    $this->curl_params['sudoc'] = $tmp;
		}
			
		//worldcat
		$tmp = intval($worldcat_id);
		if( !empty($tmp) ) {
		    $this->curl_params['worldcat'] = $tmp;
		}
			
		//partenaire
		if( !empty($this->api_key) && $use_api_key ) {
		    $this->curl_params['partenaire'] = $this->api_key;
		}
			
		$this->curl_url = $this->api_url."/acces/revue";
		$this->sendRequest();
		
		if($this->error) {
			return false;
		}
		
		$response_body = json_decode($this->curl_response->body, true);
		
		if( is_null($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'search => json response error';
			return false;
		}
		if( empty($response_body) ) {
			$this->error = true;
			$this->error_msg[] = 'search => no result provided';
			return false;
		}
		$this->result = $response_body;
		return true;
	}
	
	
	/**
	 * Liste des accès sur une sélection de titres
	 *
	 * @param [string] $issns : Tableau d'issns
	 * @param [integer] $mirabel_titre_ids : Tableau d'identifiants de titre Mir@bel
	 * @param [integer] $mirabel_revue_ids : Tableau d'identifiants de revue Mir@bel
	 * @param [string] $sudoc_ppn_ids : Tableau d'identifiants ppn Sudoc
	 * @param [integer] $worldcat_ids : Tableau d'identifiants Worldcat
	 * @param string $titre : Recherche sur titre 
	 *     "Titre%" permet de préciser : commence par "Titre"
	 *     "%Titre%" permet de préciser : contient "Titre"
	 * @param boolean $actif : Restriction aux titres actifs
	 * @param boolean $use_api_key : Utiliser la clé d'api
	 * @param boolean $abonnement : Restriction aux titres auxquels est abonné le partenaire (nécessite $with_api_key=true et clé d'api)
	 * @param boolean $possession : Restriction aux titres possédés par le partenaire (nécessite $with_api_key=true et clé d'api)
	 *
	 * @return boolean
	 */
	public function getAccesTitres (
	    $issns = [],
	    $mirabel_titre_ids = [],
	    $mirabel_revue_ids = [],
	    $sudoc_ppn_ids = [],
	    $worldcat_ids = [],
	    $titre = '',
	    $actif = false,
	    $use_api_key = false,
	    $abonnement = false,
	    $possession = false
	    ) 
	{
	        $this->resetErrors();
	        $this->resetResult();
	        $this->curl_params = [];
	        
	        //issn
	        $tmp = [];
	        if( is_array($issns) && !empty($issns) ) {
	            foreach($issns as $v) {
	                $v = trim($v);
	                if ($v) {
	                    $tmp[] = $v;
	                }
	            }
	        }
	        if( !empty($tmp) ) {
	            $this->curl_params['issn'] = implode(',', $tmp);
	        }
	        
	        //id
	        $tmp = [];
	        if( is_array($mirabel_titre_ids) && !empty($mirabel_titre_ids) ) {
	            foreach($mirabel_titre_ids as $v) {
	                $v = intval($v);
	                if ($v) {
	                    $tmp[] = $v;
	                }
	            }
	        }
	        if(!empty($tmp)) {
	            $this->curl_params['id'] = implode(',', $tmp);
	        }
	        
	        //revueid
	        $tmp = [];
	        if( is_array($mirabel_revue_ids) && !empty($mirabel_revue_ids) ) {
	            foreach($mirabel_revue_ids as $v) {
	                $v = intval($v);
	                if ($v) {
	                    $tmp[] = $v;
	                }
	            }
	        }
	        if(!empty($tmp)) {
	            $this->curl_params['revueid'] = implode(',', $tmp);
	        }
	        
	        //sudoc
	        $tmp = [];
	        if( is_array($sudoc_ppn_ids) && !empty($sudoc_ppn_ids) ) {
	            foreach($sudoc_ppn_ids as $v) {
	                $v = intval($v);
	                if ($v) {
	                    $tmp[] = $v;
	                }
	            }
	        }
	        if(!empty($tmp)) {
	            $this->curl_params['sudoc'] = implode(',', $tmp);
	        }
	        
	        //worldcat
	        $tmp = [];
	        if( is_array($worldcat_ids) && !empty($worldcat_ids) ) {
	            foreach($worldcat_ids as $v) {
	                $v = intval($v);
	                if ($v) {
	                    $tmp[] = $v;
	                }
	            }
	        }
	        if(!empty($tmp)) {
	            $this->curl_params['worldcat'] = implode(',', $tmp);
	        }
	        
	        //titre
	        $tmp = '';
	        if( is_string($titre)) {
	            $tmp = trim ($titre);
	        } 
	        if( !empty($tmp) ) {
	            $this->curl_params['titre'] = implode(',', $tmp);
	        }
	        
	        //actif
	        if($actif) {
	            $this->curl_params['actif'] = 1;
	        }
	        
	        //partenaire
	        if( !empty($this->api_key) && $use_api_key ) {
	            $this->curl_params['partenaire'] = $this->api_key;
	            
    	        //abonnement
    	        if($abonnement) {
    	            $this->curl_params['abonnement'] = 1;
    	        }
    	        
    	        //possession
    	        if($possession) {
    	            $this->curl_params['possession'] = 1;
    	        }
	
	        }
		        
	        $this->curl_url = $this->api_url."/acces/titres";
	        $this->sendRequest();
	        
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
	 * Détail d'un éditeur identifié par son ID Mir@bel
	 * 
	 * @param integer $id
	 * 
	 * @return boolean
	 */
	public function getEditeursById($mirabel_editeur_id)
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if( !intval($mirabel_editeur_id) ) {
	        return false;
	    }
	    $this->curl_url = $this->api_url."/editeurs/$mirabel_editeur_id";
	    $this->sendRequest();
	    
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
	 * Détail d'un éditeur cherché par son IdRef
	 *
	 * @param string $idref
	 *
	 * @return boolean
	 */
	public function getEditeursByIdref($idref)
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if ( !is_string($idref) ) {
	        return false;
	    }
	    $idref = trim($idref);
	    if(!$idref) {
	        return false;
	    }
	    
	    $this->curl_url = $this->api_url."/editeurs/idref/$idref";
	    $this->sendRequest();
	    
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
	 * Détail d'une revue
	 *
	 * @param integer $mirabel_revue_id
	 *
	 * @return boolean
	 */
	public function getRevuesById($mirabel_revue_id)
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if( !intval($mirabel_revue_id) ) {
	        return false;
	    }
	    $this->curl_url = $this->api_url."/revues/$mirabel_revue_id";
	    $this->sendRequest();
	    
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
	 * Détail d'un titre
	 *
	 * @param integer $mirabel_titre_id
	 *
	 * @return boolean
	 */
	public function getTitresById($mirabel_titre_id)
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if( !intval($mirabel_titre_id) ) {
	        return false;
	    }
	    	    
	    $this->curl_url = $this->api_url."/titres/$mirabel_titre_id";
	    $this->sendRequest();
	    
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
	 * Liste de titres recherchés par différents critères
	 *
	 * @param [string] $issns : Tableau d'issns
	 * @param [integer] $mirabel_titre_ids : Tableau d'identifiants de titre Mir@bel
	 * @param [integer] $mirabel_revue_ids : Tableau d'identifiants de revue Mir@bel
	 * @param [string] $sudoc_ppn_ids : Tableau d'identifiants ppn Sudoc
	 * @param [integer] $worldcat_ids : Tableau d'identifiants Worldcat
	 * @param string $titre : Recherche sur titre 
	 *     "Titre%" permet de préciser : commence par "Titre"
	 *     "%Titre%" permet de préciser : contient "Titre"
	 * @param boolean $actif : Restriction aux titres actifs
	 * @param integer $offset : Offset de début
	 *     Les réponses sont limitées à 1000 titres. Si cette limite est atteinte, ce paramètre permet de demander la suite.
	 *
	 * @return boolean
	 */
	public function getTitres(
	    $issns = [],
	    $mirabel_titre_ids = [],
	    $mirabel_revue_ids = [],
	    $sudoc_ppn_ids = [],
	    $worldcat_ids = [],
	    $titre = '',
	    $actif = false,
	    $offset = 0
	    )
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    //issn
	    $tmp = [];
	    if( is_array($issns) && !empty($issns) ) {
	        foreach($issns as $v) {
	            $v = trim($v);
	            if ($v) {
	                $tmp[] = $v;
	            }
	        }
	    }
	    if( !empty($tmp) ) {
	        $this->curl_params['issn'] = implode(',', $tmp);
	    }
	    
	    //id
	    $tmp = [];
	    if( is_array($mirabel_titre_ids) && !empty($mirabel_titre_ids) ) {
	        foreach($mirabel_titre_ids as $v) {
	            $v = intval($v);
	            if ($v) {
	                $tmp[] = $v;
	            }
	        }
	    }
	    if(!empty($tmp)) {
	        $this->curl_params['id'] = implode(',', $tmp);
	    }
	    
	    //revueid
	    $tmp = [];
	    if( is_array($mirabel_revue_ids) && !empty($mirabel_revue_ids) ) {
	        foreach($mirabel_revue_ids as $v) {
	            $v = intval($v);
	            if ($v) {
	                $tmp[] = $v;
	            }
	        }
	    }
	    if(!empty($tmp)) {
	        $this->curl_params['revueid'] = implode(',', $tmp);
	    }
	    
	    //sudoc
	    $tmp = [];
	    if( is_array($sudoc_ppn_ids) && !empty($sudoc_ppn_ids) ) {
	        foreach($sudoc_ppn_ids as $v) {
	            $v = intval($v);
	            if ($v) {
	                $tmp[] = $v;
	            }
	        }
	    }
	    if(!empty($tmp)) {
	        $this->curl_params['sudoc'] = implode(',', $tmp);
	    }
	    
	    //worldcat
	    $tmp = [];
	    if( is_array($worldcat_ids) && !empty($worldcat_ids) ) {
	        foreach($worldcat_ids as $v) {
	            $v = intval($v);
	            if ($v) {
	                $tmp[] = $v;
	            }
	        }
	    }
	    if(!empty($tmp)) {
	        $this->curl_params['worldcat'] = implode(',', $tmp);
	    }
	    
	    //titre
	    $tmp = '';
	    if( is_string($titre)) {
	        $tmp = trim ($titre);
	    }
	    if( !empty($tmp) ) {
	        $this->curl_params['titre'] = implode(',', $tmp);
	    }
	    
	    //actif
	    if($actif) {
	        $this->curl_params['actif'] = 1;
	    }
	    
	    $offset = intval($offset);
	    if($offset) {
	        $this->curl_params['offset'] = $offset;
	    }
	    
	    $this->curl_url = $this->api_url."/titres";
	    $this->sendRequest();
	    
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
	 * Pour un partenaire de Mir@bel, liste des accès sur ses revues
	 *
	 * @param boolean $abonnement : Restriction aux titres auxquels est abonné le partenaire (nécessite $with_api_key=true et clé d'api)
	 * @param boolean $possession : Restriction aux titres possédés par le partenaire (nécessite $with_api_key=true et clé d'api)
	 *
	 * @return boolean
	 */
	public function getMesAcces($abonnement = false, $possession = false)
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if( empty($this->api_key)) {
	        return false;
	    }

	    //partenaire
	    $this->curl_params['partenaire'] = $this->api_key;
	    
	    //abonnement
	    if($abonnement) {
	        $this->curl_params['abonnement'] = 1;
	    }
	    
	    //possession
	    if($possession) {
	        $this->curl_params['possession'] = 1;
	    }
	    
	    $this->curl_url = $this->api_url."/mes/acces";
	    $this->sendRequest();
	    
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
	 * Pour un partenaire de Mir@bel, liste des modifications d'accès sur ses revues
	 *
	 * @param string $depuis : Timestamp (en secondes) à partir duquel lister les changements. Une date textuelle ("2019-01-20 12:30:00" ou "-1 week") est aussi possible, mais plus ambiguë.
	 * @param boolean $abonnement : Restriction aux titres auxquels est abonné le partenaire (nécessite $with_api_key=true et clé d'api)
	 * @param boolean $possession : Restriction aux titres possédés par le partenaire (nécessite $with_api_key=true et clé d'api)
	 *
	 * @return boolean
	 */
	public function getMesAccesChange(
	    $depuis,
	    $abonnement = false, 
	    $possession = false
	    )
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if( empty($this->api_key)) {
	        return false;
	    }
	    
	    //depuis
	    if( is_string($depuis) ) {
	        $depuis = trim($depuis);
	    }
	    if( empty($depuis) ) {
	        return false;
	    }
	    
	    //partenaire
	    $this->curl_params['partenaire'] = $this->api_key;
	    
	    //abonnement
	    if($abonnement) {
	        $this->curl_params['abonnement'] = 1;
	    }
	    
	    //possession
	    if($possession) {
	        $this->curl_params['possession'] = 1;
	    }
	    
	    $this->curl_url = $this->api_url."/mes/acces/changes";
	    $this->sendRequest();
	    
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
	 * Pour un partenaire de Mir@bel, liste des titres, par possession ou abonnement
	 *
	 * @param boolean $abonnement : Restriction aux titres auxquels est abonné le partenaire (nécessite $with_api_key=true et clé d'api)
	 * @param boolean $possession : Restriction aux titres possédés par le partenaire (nécessite $with_api_key=true et clé d'api)
	 * @param integer $mirabel_ressource_id : Identifiant numérique d'une ressource de Mir@bel (par ex. 3 pour Cairn.info).
	 *
	 * @return boolean
	 */
	public function getMesTitres(
	    $abonnement = false,
	    $possession = false,
	    $mirabel_ressource_id = 0
	    )
	{
	    $this->resetErrors();
	    $this->resetResult();
	    $this->curl_params = [];
	    
	    if( empty($this->api_key)) {
	        return false;
	    }
	    
	    //partenaire
	    $this->curl_params['partenaire'] = $this->api_key;
	    
	    //abonnement
	    if($abonnement) {
	        $this->curl_params['abonnement'] = 1;
	    }
	    
	    //possession
	    if($possession) {
	        $this->curl_params['possession'] = 1;
	    }
	    
	    $mirabel_ressource_id = intval($mirabel_ressource_id);
	    if( !empty($mirabel_ressource_id)) {
	        $this->curl_params['ressourceid'] = $mirabel_ressource_id;
	    }
	    
	    $this->curl_url = $this->api_url."/mes/titres";
	    $this->sendRequest();
	    
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
	public function getErrors() 
	{
		return $this->error_msg;
	}
	

	/**
	 * RAZ messages d'erreur
	 *
	 * @return void
	 */
	public function resetErrors() 
	{
		
		$this->error = false;
		$this->error_msg = [];
	}
	
	/**
	 * Lecture resultat
	 *
	 * @return array
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * RAZ resultat
	 *
	 * @return void
	 */
	public function resetResult() {
		
		$this->result = '';
	}
	
	/**
	 * Envoi requete
	 * 
	 * @return bool
	 */
	protected function sendRequest() {
		
		$this->curl_response = '';
 		$this->curl_headers = mirabel_client::DEFAULT_HEADERS;
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
