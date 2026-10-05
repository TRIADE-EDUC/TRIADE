<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dilicom_apiapp.class.php,v 1.6 2023/05/25 12:37:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/connecteurs_out.class.php");
require_once($class_path."/connecteurs_out_sets.class.php");
require_once($class_path."/external_services_converters.class.php");
require_once($class_path."/encoding_normalize.class.php");
require_once __DIR__."/dilicom_apiapp_client.class.php";

class dilicom_apiapp extends connecteur_out {
    
    
    //délai d'expiration du jeton en secondes
    Const EXPIRATION_TOKEN_DELAY = "86400";
    
    Const TOKEN_TYPE = "Bearer";
    
    /**
     * JSON WEB TOKEN - Type
     * @var string
     */
    Const JWT_HEADER_TYPE = "JWT";
    
    /**
     * JSON WEB TOKEN - Algorithme
     * @var string
     */
    Const JWT_HEADER_ALG = "sha256";
    
    Const TOKEN_ERROR = [
        "error" => "invalid_grant",
        "error_description" => "Authentication failure"
    ];
    
    Const REFRESH_ERROR = [
        "error" => "invalid_request",
        "error_description" => "The access token expired"
    ];
    
    protected $dilicom_apiapp_client = null;
    protected $dilicom_apiapp_source = null;
	
	public function get_config_form() {
		$result = '';
		return $result;
	}
	
	public function update_config_from_form() {
		return;
	}
	
	public function instantiate_source_class($source_id) {
		return new dilicom_apiapp_source($this, $source_id, $this->msg);
	}
	
	public function instantiate_client_class($hmac_key) {
	    return new dilicom_apiapp_client($hmac_key, dilicom_apiapp_source::DEFAULT_APIAPP_WS_URL);
	}
	
	public function process($source_id, $pmb_user_id) {
	    $request = explode('/',$_SERVER["REQUEST_URI"]);
	    $method = $request[count($request)-1];
	    
	    $this->dilicom_apiapp_source = $this->instantiate_source_class($source_id);
	    $this->dilicom_apiapp_client = $this->instantiate_client_class($this->dilicom_apiapp_source->config["hmac_key"]);
	    
        //On recupere les parametres de la requete
        $bodyParams = [];
        $bodyRequestArgs = explode('&', file_get_contents('php://input'));
        foreach ($bodyRequestArgs as $arg){
           $argArray = explode('=',$arg);
           $bodyParams[$argArray[0]] = urldecode($argArray[1]);
        }
	    
        $response = '';
	    switch ($method) {
			case "discover" :
				$response = $this->discover();
	           	break;
	       	case "token" :
				$response = $this->token($bodyParams, $source_id);
	           	break;
	       	case "refresh" :
	       		$response = $this->refresh($bodyParams, $source_id);
	           	break;
	       	case "loans" :
				//on recupere le token present dans le header de la requete
	       		$token = explode(' ', $_SERVER["HTTP_AUTHORIZATION"])[1];
				$response = $this->loans($bodyParams, $source_id, $token);
	          	break;
	       default : 
	           break;
	    }
	    if('' !== $response) {
	    	$response = encoding_normalize::utf8_normalize($response);
	    	$response = json_encode($response, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	    }
	    header('Content-Type: text/html;charset=utf-8');
	    echo $response;
		return;
	}
	
	protected function discover(){
	    
	    $pmb_ws_url = $this->dilicom_apiapp_source->config['pmb_ws_url'];
	    if ($pmb_ws_url[strlen($pmb_ws_url)-1] !== "/" ){
	        $pmb_ws_url = $pmb_ws_url."/";
	    }
	    return array(
	        "infos" => [
	            "mail" =>  $this->dilicom_apiapp_source->config['email'],
	            "company" => $this->dilicom_apiapp_source->config['company']
	        ],
	        "authentication" => [
	            "get_token"=>$pmb_ws_url."token",
	            "refresh_token"=>$pmb_ws_url."refresh"
	        ],
	        "resources"=>[
	        		[
	            		"code"=>"loans",
	        			"endpoint"=>$pmb_ws_url."loans",
	            		"version"=>"1"
	        		]
	        ]
	    );
	}
		
	protected function token($bodyParams, $source_id){
	    
	    //on verifie le token cote Dilicom
	    $tokenIsOk = $this->dilicom_apiapp_client->check_token_to_dilicom($bodyParams['client_secret']);
	    if (!$tokenIsOk){
	        return static::format_auth_error(self::TOKEN_ERROR, 400);
	    }
	    
	    //on verifie l'emprunteur cote PMB
	    $emprIsOk = $this->empr_is_ok($bodyParams);
	    if (!$emprIsOk){
	        return static::format_auth_error(self::TOKEN_ERROR, 400);
	    }
	    
	    return $this->compute_token($bodyParams, $source_id);
	}
	
	protected function refresh(&$bodyParams, $source_id){
	    //verifier que le refresh_token est toujours valide
	    $response = $this->check_token($bodyParams['refresh_token']);
	    //Si non on retourne une erreur
	    if (!$response['success'] || empty($response['payload'])){
	        return static::format_auth_error(self::REFRESH_ERROR, 401);
	    }
	    $bodyParams['id_empr'] = $response['payload']->id_empr;
	    
	    return $this->compute_token($bodyParams, $source_id);
	}
	
	protected function loans(&$bodyParams, $source_id, $token){

	    $response = $this->check_token($token);

	    if (!$response['success']  || empty($response['payload'])) return false;
	    
	    $id_empr = $response['payload']->id_empr;
	    
	    //On recupere les prets PNB pour l'emprunteur
	    $loans = $this->get_loans($id_empr);
	    
	    //On récupère les données des emprunts
	    $loansFetch = $this->fetch_loans($loans);
	    
	    //On construit la réponse
	    return $this->format_loans($loansFetch);
	    
	}
	
	private function fetch_loans($loans){
	    $loans_fetch = [];
	    
	    //on agrège les données
	    foreach ($loans as $expl_id => $loan) {
	        $loans_fetch[$expl_id]['pnb_loan'] = $loan;
 	        $loans_fetch[$expl_id]['pret'] = new pret($loan["pnb_loan_num_loaner"], $expl_id);
 	        $loans_fetch[$expl_id]['notice'] = new notice($this->get_notice_id_from_expl_id($expl_id));
	        $loans_fetch[$expl_id]['offers_data'] = $this->get_offers_data($loan);
	    }
	    return $loans_fetch;
	}
	
	
	private function get_notice_id_from_expl_id($loans_expl_id){
	    $query = "SELECT expl_notice FROM exemplaires WHERE expl_id = '$loans_expl_id'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result) == 1){
	        return pmb_mysql_result($result, 0, "expl_notice");
	    }
	    return 0;
	}
	
	private function get_loans($id_empr){
	    $loans = [];
	    $query = "SELECT * FROM pnb_loans WHERE pnb_loan_num_loaner=$id_empr";
	    $result = pmb_mysql_query($query);
	    while ($r = pmb_mysql_fetch_assoc($result)){
	        $loans[$r['pnb_loan_num_expl']] = $r;
	    }
	    return $loans;
	}
	
	private function format_loans($loans){
	    global $pmb_pnb_param_login;
	    $loans_formated = [];

	    
	    foreach ($loans as $loan){
	        $contributors = $this->get_contributor($loan['notice']);
	        $categoryClil = $this->get_category_clil($loan['offers_data']);
    	   
	        $productFormDetails = $loan['offers_data']['Product']['DescriptiveDetail']['ProductFormDetail'];
	        $productFormDetails = is_array($productFormDetails) ? $productFormDetails : [$productFormDetails];

	        $loans_formated[] = [
    	        "loanId"                   => $loan['pnb_loan']["pnb_loan_loanid"],
	            "userId"                   => $loan['pnb_loan']["pnb_loan_num_loaner"],
	            "orderLineId"              => $loan['pnb_loan']["pnb_loan_order_line_id"],
	            "beginDate"                => $this->format_date_iso($loan['pret']->pret_date),
	            "endDate"                  => $this->format_date_iso($loan['pret']->pret_retour),
	            "loanhLink"                => $loan['pnb_loan']['pnb_loan_link'], 
	            "standardTitle"            => $loan['notice']->tit1,
	            "gtin13"                   => str_replace('-', '', $loan['notice']->code),
	            "imprintName"              => $loan['notice']->ed1 ?? "",
	            "collection"               => $loan['notice']->coll ?? "",
	            "language"                 => "", //facultatif
	            "categoryClil"             => $categoryClil,// obligatoire
	            "epubTechnicalProtection"  => $loan['pnb_loan']["pnb_loan_drm"],
	            "publicationDate"          => $this->format_date_fr($loan['notice']->date_parution) ?? "", //facultatif
	        	"description"              => str_replace(["\n","\r"],'', $loan['notice']->n_resume),
	            "frontCoverMedium"         => $loan['notice']->thumbnail_url,
	            "loanerGln"                => $pmb_pnb_param_login,
	            "contributors"             => $contributors, //facultatif
	            "productFormDetails"       => $productFormDetails, // obligatoire
            ];
	    }
	    return ["loans" => $loans_formated];
	}
	
	private function format_date_iso($date){
	    if (!$date){
	        return '';
	    }
	    
	    $dateTime = new \DateTime($date);
	    return $dateTime->format(DateTime::ISO8601);
	}
	
	private function format_date_fr($date){
	    if (!$date){
	        return '';
	    }
	    $dateTime = new \DateTime($date);
	    return $dateTime->format("d/m/Y");
	}
	
	private function check_token($token) {
	    /**
	     * @var array $json_web_token 
	     * $json_web_token[0] = $base64Header
         * $json_web_token[1] = $base64Payload
         * $json_web_token[2] = $signature
	     */
	    $json_web_token = explode('.', $token);
	    $signature = hash_hmac(self::JWT_HEADER_ALG, $json_web_token[0].'.'.$json_web_token[1], $this->dilicom_apiapp_source->config["hmac_key"], false);
	    if ($signature === $json_web_token[2]) {
	        return array(
	            "success" => true,
	            "payload" => json_decode(base64_decode($json_web_token[1]))
	        );
	    }
	    
	    return array(
	        "success" => false,
	        "payload" => new stdClass()
	    );
	}
	
	private function empr_is_ok(&$bodyParams){
    
	    $empr = emprunteur::check_login_and_password($bodyParams["username"], $bodyParams["password"]);
	    if (!$empr){
	        return false;
	    }
	    $bodyParams['id_empr'] = $empr;
	    
	    return true;
	}
	
	private function compute_token($bodyParams, $source_id) {

	    // JSON Web Token | calcul du token / refresh_token
	    $token = $this->generate_token($bodyParams['id_empr']);
	    $refresh_token = $this->generate_token($bodyParams['id_empr'], true);
	    
	    //on retourne les données
	    return [
	        "access_token" => $token,
	        "expires_in" => self::EXPIRATION_TOKEN_DELAY,
	        "token_type" => self::TOKEN_TYPE,
	        "refresh_token" => $refresh_token
	    ];
	}
	
	private function generate_token ($id_empr, $refresh_token = false) {
	    $header = new stdClass();
	    $header->typ = self::JWT_HEADER_TYPE;
	    $header->alg = self::JWT_HEADER_ALG;
	    
	    $payload = new stdClass();
	    $payload->id_empr = $id_empr;
	    $payload->expires_in = self::EXPIRATION_TOKEN_DELAY;
	    $payload->token_type = self::TOKEN_TYPE;
        $payload->refresh_token = $refresh_token;
        $payload->rand = bin2hex(random_bytes(16));
        
        $base64Header = base64_encode(json_encode($header));
	    $base64Payload = base64_encode(json_encode($payload));
	    $signature = hash_hmac(self::JWT_HEADER_ALG, $base64Header.'.'.$base64Payload, $this->dilicom_apiapp_source->config["hmac_key"], false);
	    
	    return $base64Header.'.'.$base64Payload.'.'.$signature;
	}
	
	private function get_offers_data($loan){
	    $offers_data = [];
	    
	    $query = "SELECT pnb_order_data FROM pnb_orders WHERE pnb_order_line_id = '".$loan['pnb_loan_order_line_id']."'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result) == 1){
	        $offers_data = json_decode(pmb_mysql_result($result, 0, "pnb_order_data"), true);
	    }
	    
	    return $offers_data;
	}
	
	private function get_contributor($notice){
	    $contributors = [];
	    
	    foreach ($notice->responsabilites as $contrib){
	        if ($contrib[0]['id']){
	            $author = new auteur($contrib[0]['id']);
	            global $fonction_auteur;
	            $contributors[] = [
	                "role"             => [
	                    $contrib[0]['fonction'] ? $fonction_auteur[$contrib[0]['fonction']] : ''
	                ],
	                "keyName"          => $author->name ?? '',
	                "nameBeforeKey"    => $author->rejete ?? ''
	            ];
	        }
	    }
	    return $contributors;
	}
	
	private function get_category_clil($offers_data){
	    $categoryClil = '';
	    $first = true;
	    
	    foreach ($offers_data['Product']['DescriptiveDetail']['Subject'] as $subject) {
	        if (!empty($subject['SubjectCode'] && !empty($subject['SubjectHeadingText'] && $subject['SubjectSchemeIdentifier'] == '29'))) {
	            if (!$first){
	                $categoryClil .= ', ' . $subject['SubjectHeadingText'];
	            } else {
	                $categoryClil .= $subject['SubjectHeadingText'];
	            }
	            $first = false;
	        }
	    }
	    return $categoryClil;
	}

	public static function format_error($message = '', $infos = ""){
	    return [
	        "status" => false,
	        "messages" => $message,
	        "infos" => $infos
	    ];
	}

	public static function format_auth_error($message, $error_code){
	    http_response_code($error_code);
	    return $message;
	}
	
}

class dilicom_apiapp_source extends connecteur_out_source {
	
	const DEFAULT_APIAPP_WS_URL = 'https://pnb-app.centprod.com/v1/pnb-app/json/';
	
	public function  __construct($connector, $id, $msg) {
		parent::__construct($connector, $id, $msg);
	}
	
	public function get_config_form() {		
		
		global $charset;
		$result = parent::get_config_form();
		
		//initialisation des parametres a la creation
		if(!$this->id) {
			$this->config['pmb_ws_url'] = '';
			$this->config['apiapp_ws_url'] = dilicom_apiapp_source::DEFAULT_APIAPP_WS_URL;
			$this->config['user_agent'] = '';
			$this->config['hmac_key'] = '';
			$this->config['company'] = '';
			$this->config['email'] = '';
		}
		
		//Adresse du Web service PMB
		$result.= "
			<div class='row'>
				<label class='etiquette' for='form_pmb_ws_url' >".$this->msg['pmb_ws_url'].'</label>
				<br />';
 		if ($this->id) {
 			$result.= "
 			<input type='text' class='saisie-80em' name='form_pmb_ws_url' id='form_pmb_ws_url' value='".$this->config['pmb_ws_url']."' />
			<strong>".$this->msg['pmb_ws_url_comment']."</strong>";
		} else {
			$result.= $this->msg['pmb_ws_url_unrecorded'];
		}
		$result .= "</div><hr />";

		//Adresse du Web service DILICOM API APP
		$result.= "
        <div class='row'>
            <label class='etiquette' for='form_apiapp_ws_url'>".$this->msg['apiapp_ws_url']."</label><br />
            <input type='text' class='saisie-80em' id='form_apiapp_ws_url' name='form_apiapp_ws_url' value='".$this->config['apiapp_ws_url']."' />
        </div>";
		
		//Identifiant utilisateur
		$result.= "
        <div class='row'>
            <label class='etiquette' for='form_user_agent'>".htmlentities($this->msg['user_agent'],ENT_QUOTES,$charset)."</label><br />
            <input type='text' class='saisie-20em' name='form_user_agent' id='form_user_agent' value='".htmlentities($this->config['user_agent'],ENT_QUOTES,$charset)."' />
        </div>";
		
		//Cle HMAC
		$result.= "
        <div class='row'>
            <label class='etiquette' for='form_hmac_key'>".htmlentities($this->msg['hmac_key'],ENT_QUOTES,$charset)."</label><br />
            <input type='password' class='saisie-80em' name='form_hmac_key' id='form_hmac_key' value='".htmlentities($this->config['hmac_key'],ENT_QUOTES,$charset)."' />
			<span class='fa fa-eye' onclick='toggle_password(this, \"form_hmac_key\");'></span>
        </div><hr />";
		
		//Nom etablissement
		$result.= "
		<div class='row'>
		<label class='etiquette' for='form_company'>".htmlentities($this->msg['company'],ENT_QUOTES,$charset)."</label><br />
		<input type='text' class='saisie-80em' name='form_company' id='form_company' value='".htmlentities($this->config['company'],ENT_QUOTES,$charset)."' />
		</div>";

		//Email Contact technique
		$result.= "
		<div class='row'>
		<label class='etiquette' for='form_email'>".htmlentities($this->msg['email'],ENT_QUOTES,$charset)."</label><br />
		<input type='email' class='saisie-80em' name='form_email' id='form_email' value='".htmlentities($this->config['email'],ENT_QUOTES,$charset)."' />
		</div>";

		//Input Hidden contenant l'id de la source
		$result.= "
		<input type='hidden' name='form_source_id' value='".htmlentities($this->id,ENT_QUOTES,$charset)."' />";
		
		$result.= "<div class='row'>&nbsp;</div>";
		
		return $result;
	}
	
	public function update_config_from_form() {
		
		global $pmb_url_base;
		global $form_pmb_ws_url, $form_apiapp_ws_url, $form_user_agent, $form_hmac_key, $form_company, $form_email;
		
		parent::update_config_from_form();
		if( !isset($form_pmb_ws_url) ) {
			$this->config['pmb_ws_url'] = $pmb_url_base.'ws/connector_out.php?source_id='.$this->id;
		} else {
			$this->config['pmb_ws_url'] = trim(stripslashes($form_pmb_ws_url));
		}
		$this->config['apiapp_ws_url'] = trim(stripslashes($form_apiapp_ws_url));
		$this->config['user_agent'] = trim(stripslashes($form_user_agent));
		$this->config['hmac_key'] = trim(stripslashes($form_hmac_key));
		$this->config['company'] = trim(stripslashes($form_company));
		$this->config['email'] = trim(stripslashes($form_email));
		
    }
}
