<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: applimobile.class.php,v 1.16.2.1 2024/05/16 14:39:52 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $lang, $opac_empr_password_salt, $opac_resa_dispo, $opac_default_sort, $opac_pret_prolongation;
global $charset, $lang, $pmb_url_base;

global $applimobile_operation_ws_url, $applimobile_operation_ws_user, $applimobile_operation_ws_password;
global $applimobile_library_name, $applimobile_library_logo;
global $applimobile_allow_booking, $applimobile_allow_password_change;
global $applimobile_contact_template;
global $applimobile_carousel_shelves, $applimobile_carousel_first_shelf_id;
global $applimobile_facets, $applimobile_sorts, $applimobile_simple_search_types, $applimobile_advanced_search_fields;

require_once $class_path."/connecteurs_out.class.php";
require_once $class_path."/connecteurs_out_sets.class.php";
require_once $class_path."/external_services_common.class.php";
require_once $class_path."/external_services_converters.class.php";
require_once $class_path."/etagere.class.php";
require_once $class_path."/facettes.class.php";

class applimobile extends connecteur_out {

	const CIPHER = "AES-256-GCM";
	const SECRET_LEN = 32;
	const KEY_LEN = 32;
	const TAG_LEN = 16;

	public function get_config_form() {
		return '';
	}

	public function update_config_from_form() {
		return;
	}

	public function instantiate_source_class($source_id) {
		return new applimobile_source($this, $source_id, $this->msg);
	}

	public function process($source_id, $pmb_user_id) {

		//Parametre GET
		global $action;

		if( !isset($action) || !in_array($action,  ['token', 'content']) ) {
			$action = 'content';
		}

		$source = new applimobile_source($this, $source_id, $this->msg);

		if(!$this->check_api_key($source)) {
			return;;
		}

		switch ($action) {
			case 'token' :
				$this->get_token($source);
				break;
			default :
			case 'content' :
				$this->get_content($source);
				break;
		}
		return;
	}

	protected function check_api_key($source) {

		if(!isset($_SERVER['HTTP_X_API_KEY'])) {
			header('Content-Type: Application/json;charset=utf-8');
			header('Status: 401 Unauthorized');
			return false;
		}
		$api_key = $_SERVER['HTTP_X_API_KEY'];
		$param = $source->config;
		if($api_key !== $param['keys']['api']) {
			header('Content-Type: Application/json;charset=utf-8');
			header('Status: 401 Unauthorized');
			return false;
		}
		return true;
	}


	protected function get_content($source) {

		//Parametres appli
	    global $opac_empr_password_salt, $opac_resa_dispo, $opac_default_sort, $opac_pret_prolongation;
	    global $opac_cart_allow;

 		$param = $source->config;
		if(false === $this->check_token($param)) {
			return;
		}
		$bin_key = base64_decode($param['keys']['api']);
		unset($param['keys']);

		//Encodage utilisateur et mot de passe applimobile_operation_ws_password

		$clear_user = $param['applimobile_operation_ws_user'];
		$iv_len = openssl_cipher_iv_length(applimobile::CIPHER);
		$bin_iv = openssl_random_pseudo_bytes($iv_len);

		$bin_tag = '';
		$bin_cipher_user = openssl_encrypt($clear_user, applimobile::CIPHER, $bin_key, OPENSSL_RAW_DATA, $bin_iv, $bin_tag);
		$param['applimobile_operation_ws_user'] = base64_encode($bin_iv.$bin_cipher_user.$bin_tag);

		$clear_password = $param['applimobile_operation_ws_password'];
		$bin_tag = '';
		$bin_cipher_password = openssl_encrypt($clear_password, applimobile::CIPHER, $bin_key, OPENSSL_RAW_DATA, $bin_iv, $bin_tag);

		$param['applimobile_operation_ws_password'] = base64_encode($bin_iv.$bin_cipher_password.$bin_tag);

		// le parametre $opac_empr_password_salt doit etre defini
		if('' == $opac_empr_password_salt) {
		    password::gen_salt_base();
		}
		$param['opac_empr_password_salt'] = base64_encode(hex2bin(substr($opac_empr_password_salt.$opac_empr_password_salt, 0, 64)));
		$param['opac_resa_dispo'] = $opac_resa_dispo;
		$param['opac_default_sort'] = $opac_default_sort;
		$param['opac_pret_prolongation'] = $opac_pret_prolongation;
		$param['opac_cart_allow'] = $opac_cart_allow;

		if(count($param['applimobile_carousel_shelves'])) {
			$shelf_list =  etagere::get_etagere_list(true);
			$tmp = [];
			foreach($shelf_list as $shelf) {
				if( in_array($shelf['idetagere'], $param['applimobile_carousel_shelves']) ) {
					$tmp[] = [
							'id'				=> $shelf['idetagere'],
							'label'				=> $shelf['name'],
					];
				}
			}
			$param['applimobile_carousel_shelves'] = $tmp;
		}

		if(count($param['applimobile_facets'])) {
			$facet_list = facettes::get_list();
			$tmp = [];
			foreach($facet_list as $facet) {
				if( in_array($facet['id_facette'], $param['applimobile_facets']) ) {
					$tmp[] = [
							'id'				=> $facet['id_facette'],
							'label'				=> $facet['facette_name'],
							'code_champ'		=> $facet['facette_critere'],
							'code_ss_champ'		=> $facet['facette_ss_critere'],
							'nb_result'         => $facet['facette_nb_result'],
							'limit_plus'		=> $facet['facette_limit_plus'],
							'type_sort'		    => $facet['facette_type_sort'],
							'order_sort'		=> $facet['facette_order_sort'],
							'datatype_sort'		=> $facet['facette_datatype_sort'],
					];
				}
			}
			$param['applimobile_facets'] = $tmp;
		}

		if(count($param['applimobile_sorts'])) {
			$sort_list = external_services_common::getRecordSortTypes();
			$tmp = [];
			foreach($sort_list as $sort) {
				if( in_array($sort['sort_name'], $param['applimobile_sorts']) ) {
					$tmp[] = [
							'name'				=> $sort['sort_name'],
							'label'				=> $sort['sort_caption'],
					];
				}
			}
			$param['applimobile_sorts'] = $tmp;
		}

		if(count($param['applimobile_simple_search_types'])) {
			$simple_search_types = external_services_common::SIMPLE_SEARCH_TYPES;
			$tmp = [];
			foreach($simple_search_types as $k=>$type) {
				if( in_array($type, $param['applimobile_simple_search_types']) ) {
					$tmp[$k] = [
							'type'				=> $type,
							'label'				=> $this->msg['applimobile_simple_search_type_'.$k],
					];
				}
			}
			$param['applimobile_simple_search_types'] = $tmp;
		}

		$utf8_param = encoding_normalize::utf8_normalize($param);

		header('Content-Type: Application/json;charset=utf-8');
		echo json_encode($utf8_param);
		return;
	}

	protected function check_token($param) {

		$json = json_decode(file_get_contents('php://input'));
		if(!isset($json->token)) {
			header('Content-Type: Application/json;charset=utf-8');
			header('Status: 401 Unauthorized');
			echo json_encode(['error'=>'no token']);
			return false;
		}

		$bin_key = base64_decode($param['keys']['key']);
		$bin_iv = base64_decode($param['keys']['iv']);

		$b64_token = $json->token;
		$bin_token = base64_decode($b64_token);
		$bin_tag = substr($bin_token, -applimobile::TAG_LEN);
		$bin_cipher_phrase = substr($bin_token, 0, (strlen($bin_token)-applimobile::TAG_LEN));

		$hex_phrase = openssl_decrypt($bin_cipher_phrase, applimobile::CIPHER, $bin_key, OPENSSL_RAW_DATA, $bin_iv, $bin_tag);

		$hex_secret = substr($hex_phrase, 0, applimobile::SECRET_LEN*2);
		$b64_secret = base64_encode(hex2bin($hex_secret));
		if($b64_secret !== $param['keys']['secret']) {
			header('Content-Type: Application/json;charset=utf-8');
			header('Status: 401 Unauthorized');
			echo json_encode(['error'=>'wrong token']);
			return false;
		}
		$hex_date = substr($hex_phrase, applimobile::SECRET_LEN*2);
		$current_date = date('YmdHis');
		if((intval($current_date) - intval($hex_date)) > 60) {
			header('Content-Type: Application/json;charset=utf-8');
			header('Status: 401 Unauthorized');
			echo json_encode(['error'=>'outdated token']);
			return false;
		}

		return true;
	}


	protected function get_token($source) {

		$param = $source->config;

		$bin_secret = base64_decode($param['keys']['secret']);
		$bin_key = base64_decode($param['keys']['key']);
		$bin_iv = base64_decode($param['keys']['iv']);

		$hex_secret = bin2hex($bin_secret);
		$hex_phrase = $hex_secret.date('YmdHis');

		$bin_tag = '';
		$bin_cipher_phrase = openssl_encrypt($hex_phrase, applimobile::CIPHER, $bin_key, OPENSSL_RAW_DATA, $bin_iv, $bin_tag);
		$b64_token = base64_encode($bin_cipher_phrase.$bin_tag);
		header('Content-Type: Application/json;charset=utf-8');
		header('Status: 200');
		echo json_encode(['token'=>$b64_token]);
		return;
	}


	public function get_running_pmb_userid($source_id) {

		if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			header('status: 200');
			exit();
		}
		$user_id = 0;
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			//Si on ne nous fourni pas de credentials, alors on teste l'utilisateur anonyme
			$user_id = connector_out_check_credentials('', '', $source_id);
			if ($user_id === false) {
				header('WWW-Authenticate: Basic realm="PMB Applimobile"');
				header('HTTP/1.0 401 Unauthorized');
				exit();
			}
		} else {
			//Sinon on teste les credentiels fournis
			$rawusername = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
			$user_id = connector_out_check_credentials($rawusername, $password, $source_id);
			if ($user_id === false) {
				header('WWW-Authenticate: Basic realm="PMB Applimobile"');
				header('HTTP/1.0 401 Unauthorized');
				exit();
			}
		}
		return $user_id;
	}

}

class applimobile_source extends connecteur_out_source {

    const APP_FONCTIONS = [
        "loans",
        "cart",
        "bookmarkedrecords",
        "simplesearch",
        "advancedsearch",
        "suggestionlist",
        "sections",
        "barcodescanner",
        "barcodeinfo",
        "library"
    ];
    public function get_app_fonctions(){
        $result = array();
        foreach(self::APP_FONCTIONS as $fonction){
            $result[$fonction] = $this->msg["applimobile_".$fonction] ?? $fonction;
        }
        return $result;
    }
	public function get_config_form() {

		global $charset, $lang, $pmb_url_base;

		$result = parent::get_config_form();
		if(!$this->id){
			$this->config['applimobile_operation_ws_url'] = '';
			$this->config['applimobile_operation_ws_user'] = '';
			$this->config['applimobile_operation_ws_password'] = '';
			$this->config['applimobile_library_name'] = '';
			$this->config['applimobile_library_logo'] = '';
			$this->config['applimobile_contact_template'] = '';
			$this->config['applimobile_carousel_shelves'] = [];
			$this->config['applimobile_carousel_first_shelf_id'] = 0;
			$this->config['applimobile_facets'] = [];
			$this->config['applimobile_sorts'] = [];
			$this->config['applimobile_simple_search_types'] = [];
			$this->config['applimobile_advanced_search_criteria'] = [];
			$this->config['applimobile_allow_booking'] = 0;
			$this->config['applimobile_allow_password_change'] = 0;
			$this->config['applimobile_keys'] = [];
			$this->config['applimobile_associated_accounts_activate'] = 0;
			$this->config['applimobile_rapid_search_localised'] = 0;
			$this->config['applimobile_func'] = [];
			$this->config['applimobile_opac_url'] = "";
			$this->config['applimobile_external_auth_active'] = "";
		}

		//Adresse du Web service de paramétrage
		$result .= "<div class='row'>
			<label class='etiquette' >".$this->msg['applimobile_configuration_ws_url']."</label><br />";
		if ($this->id) {
			$result .= "<a target='_blank' href='".$pmb_url_base."ws/connector_out.php?source_id=".$this->id."'>".$pmb_url_base."ws/connector_out.php?source_id=".$this->id."</a>";
		} else {
			$result .= $this->msg["applimobile_configuration_ws_unrecorded"];
		}
		$result .= "</div>";

		//Clé d'API
		if($this->id) {
			$result.= "<div class='row'>
            	<label class='etiquette' for='applimobile_api_key'>".$this->msg['applimobile_api_key']."</label><br />
            	<input type='text' class='saisie-80emr' id='applimobile_api_key' value='".htmlentities($this->config['keys']['api'],ENT_QUOTES,$charset)."' />
        	</div>";
		}

		//Renouvellement des clés accès
		if($this->id) {
			$result.= "<div class='row'>
				<input type='checkbox' id='applimobile_renew_keys' name='applimobile_renew_keys' value='1' />&nbsp;
				<label for='applimobile_renew_keys'>".$this->msg["applimobile_renew_keys"]."</label>
			</div>";
		}

		$result.= "<hr />";

		//Adresse du web service de fonctionnement
		$result .=
		"<div class='row'>
			<label class='etiquette' for='applimobile_operation_ws_url' >".$this->msg['applimobile_operation_ws_url']."</label><br />";

		//Connecteurs JSON-RPC disponibles
		$available_sources = [];
		$connecteurs = new connecteurs_out();
		foreach($connecteurs->connectors as $conn) {
			if( $conn->name == 'JSON-RPC') {
				$available_sources = $conn->sources;
			}
		}
		if(empty($available_sources)) {

			$result.= $this->msg['applimobile_operation_ws_no_jsonrpc_source'];

		} else {

			$result.= "<div class='row'>
				<input type='text' class='saisie-80em' id='applimobile_operation_ws_url' name='applimobile_operation_ws_url' value='".$this->config['applimobile_operation_ws_url']."' required />
			</div>
			<div class='row'>
				<label for='applimobile_operation_ws_user'>".$this->msg["applimobile_operation_ws_user"]."</label><br />
				<input type='text' name='applimobile_operation_ws_user' id='applimobile_operation_ws_user' value='".htmlentities($this->config['applimobile_operation_ws_user'], ENT_QUOTES, $charset)."' required/>
			</div>
			<div class='row'>
				<label for='applimobile_operation_ws_password'>".$this->msg["applimobile_operation_ws_password"]."</label><br />
				<input type='password' name='applimobile_operation_ws_password' id='applimobile_operation_ws_password' value='".htmlentities($this->config['applimobile_operation_ws_password'], ENT_QUOTES, $charset)."' required />
				<span class='fa fa-eye' onclick='toggle_password(this, \"applimobile_operation_ws_password\");'></span>
			</div>";
		}
		$result.= "<hr />";

		//Nom de la bibliothèque
		$result.= "<div class='row'>
			<label for='applimobile_library_name'>".$this->msg["applimobile_library_name"]."</label><br />
			<input type='text' class='saisie-80em' id='applimobile_library_name' name='applimobile_library_name' value='".htmlentities($this->config['applimobile_library_name'], ENT_QUOTES, $charset)."' required />
		</div>";

		//URL de l'icône de la bibliothèque
		$result.= "<div class='row'>
			<label for='applimobile_library_logo'>".$this->msg["applimobile_library_logo"]."</label><br />
			<input type='text' class='saisie-80em' id='applimobile_library_logo' name='applimobile_library_logo' value='".htmlentities($this->config['applimobile_library_logo'], ENT_QUOTES, $charset)."' />
		</div>
		<hr />";

		//Template page contact
		$result.= "<div class='row'>
			<label for='applimobile_contact_template'>".$this->msg["applimobile_contact_template"]."</label><br />
			<textarea id='applimobile_contact_template' name='applimobile_contact_template' class='saisie-80em' rows='8' cols='62' wrap='virtual'>".$this->config['applimobile_contact_template']."</textarea>
		<div>
		<hr />";

		//Autoriser les réservations
		$checked = ($this->config['applimobile_allow_booking']==1) ? 'checked' : '';
		$result.= "<div class='row'>
			<input type='checkbox' id='applimobile_allow_booking' name='applimobile_allow_booking' value='1' $checked />&nbsp;
			<label for='applimobile_allow_booking'>".$this->msg["applimobile_allow_booking"]."</label>
		</div>";

		//Autoriser le changement de mot de passe
		$checked = ($this->config['applimobile_allow_password_change']==1) ? 'checked' : '';
		$result.= "<div class='row'>
			<input type='checkbox' id='applimobile_allow_password_change' name='applimobile_allow_password_change' value='1' $checked />&nbsp;
			<label for='applimobile_allow_password_change'>".$this->msg["applimobile_allow_password_change"]."</label>
		</div><hr />";

		//Etageres a utiliser pour le carrousel en page d'accueil
		$shelf_list = etagere::get_etagere_list(true);
		$shelf_selector = "
		<select id='applimobile_carousel_shelves' name='applimobile_carousel_shelves[]' multiple size='5'>";
		$first_shelf_selector = "
		<select id='applimobile_carousel_first_shelf_id' name='applimobile_carousel_first_shelf_id'>";
		foreach($shelf_list as $shelf) {

			$selected = ( in_array($shelf['idetagere'], $this->config['applimobile_carousel_shelves']) ? 'selected' : '');
			$shelf_selector.= "
			<option value='".$shelf['idetagere']."' $selected >".htmlentities($shelf['name'], ENT_QUOTES, $charset)."</option>";

			$selected = ($shelf['idetagere'] == $this->config['applimobile_carousel_first_shelf_id'])? 'selected' : '';
			$first_shelf_selector.= "
			<option value='".$shelf['idetagere']."' $selected >".htmlentities($shelf['name'], ENT_QUOTES, $charset)."</option>";
		}
		$shelf_selector.= "</select>";
		$first_shelf_selector.= "</select>";
		$result.= "<div class='row'>
			<label >".$this->msg["applimobile_carousel_shelves"]."</label><br />
			$shelf_selector
			<br />
			<label >".$this->msg["applimobile_carousel_first_shelf"]."</label><br />
			$first_shelf_selector
		</div><hr />";

		//Facettes a afficher
		$facet_list = facettes::get_list();
		$facet_selector = $this->msg['applimobile_facet_none'];
		if(count($facet_list)) {
			$facet_selector = "<select id='applimobile_facets' name='applimobile_facets[]' multiple size='5'>";
			foreach($facet_list as $facet) {
				$selected = ( in_array($facet['id_facette'], $this->config['applimobile_facets']) ? 'selected' : '');
				$facet_selector.= "
				<option value='".$facet['id_facette']."' $selected >".htmlentities($facet['facette_name'], ENT_QUOTES, $charset)."</option>";
			}
			$facet_selector.= "</select>";
		}
		$result.= "<div class='row'>
			<label>".$this->msg["applimobile_facets"]."</label><br />
			$facet_selector
		</div><hr />";

		//Tris disponibles
		$sort_list = external_services_common::getRecordSortTypes();
		$sort_selector = "<select id='applimobile_sorts' name='applimobile_sorts[]' multiple size='10'>";
		foreach($sort_list as $sort) {
			$selected = ( in_array($sort['sort_name'], $this->config['applimobile_sorts']) ? 'selected' : '');
			$sort_selector.= "
			<option value='".$sort['sort_name']."' $selected >".htmlentities($sort['sort_caption'], ENT_QUOTES, $charset)."</option>";
		}
		$sort_selector.= "</select>";

		$result.= "<div class='row'>
			<label>".$this->msg["applimobile_sorts"]."</label><br />
			$sort_selector
		</div><hr />";

		//Types de recherche simple
		$simple_search_types = external_services_common::SIMPLE_SEARCH_TYPES;
		$type_selector = "";
		foreach($simple_search_types as $k=>$type) {
			$checked = ( in_array($type, $this->config['applimobile_simple_search_types']) ? 'checked' : '');
			$type_selector.= "
				<div class='row'>
					<input type='checkbox' id='applimobile_simple_search_types_".$type."' name='applimobile_simple_search_types[]' value='".$type."' ".$checked."/>&nbsp;
					<label for='applimobile_simple_search_types_".$type."' >".htmlentities($this->msg['applimobile_simple_search_type_'.$k], ENT_QUOTES, $charset)."<label>
				</div>";
		}
		$result.= "<div class='row'>
			<label >".$this->msg["applimobile_simple_search_types"]."</label><br />
			$type_selector
		</div><hr />";

		//Critères de recherche avancée
		$search_fields =  external_services_common::getAdvancedSearchFields('search_fields', $lang, false);
		$search_fields_selector = "<select id='applimobile_advanced_search_fields' name='applimobile_advanced_search_fields[]' multiple size='10'>";
		foreach($search_fields as $field) {
			$selected = ( in_array($field['id'], $this->config['applimobile_advanced_search_fields'] ?? []) ? 'selected' : '');
			$search_fields_selector.= "
			<option value='".$field['id']."' $selected >".htmlentities($field['label'], ENT_QUOTES, $charset)."</option>";
		}
		$search_fields_selector.= "</select>";
		$result.= "<div class='row'>
			<label >".$this->msg["applimobile_advanced_search_fields"]."</label><br />
			$search_fields_selector
		</div><hr />";
        //Paramétrage des comptes associés
        $result .= "<div class=\"row\">";
        $result .= "<label >".$this->msg["applimobile_associated_accounts_activate"]."</label><br />";
        $checked = $this->config['applimobile_associated_accounts_activate'] == '1' ? 'checked' : '';
        $result .="<input type='checkbox' id='applimobile_associated_accounts_activate' name='applimobile_associated_accounts_activate' value='1' ".$checked."/>&nbsp;";
        $result .="</div><hr />";
        //Paramétrage de la recherche rapide
        $result .= "<div class=\"row\">";
        $result .= "<label >".$this->msg["applimobile_rapid_search"]."</label><br />";
        $checked = $this->config['applimobile_rapid_search_localised'] == '1' ? 'checked' : '';
        $result .="<input type='checkbox' id='applimobile_rapid_search_localised' name='applimobile_rapid_search_localised' value='1' ".$checked."/>&nbsp;";
        $result .="</div><hr />";
        //Authentification externe
        $result .= "<div class=\"row\">";
        $result .= "<label >".$this->msg["applimobile_external_auth_active"]."</label><br />";
        $checked = $this->config['applimobile_external_auth_active'] == '1' ? 'checked' : '';
        $result .="<input type='checkbox' id='applimobile_external_auth_active' name='applimobile_external_auth_active' value='1' ".$checked."/>&nbsp;";
        $result .="</div><hr />";
        //Paramétrage des modules
        $fonctions = $this->get_app_fonctions();
        $result .= "<div class=\"row\">";
        $result .= "<label >".$this->msg["applimobile_disable_func"]."</label><br />";
		if(is_array($this->config['applimobile_func'])) {
			$fonctions_selector = "<select id='applimobile_func' name='applimobile_func[]' multiple size='5'>";
			foreach($fonctions as $name => $label) {
				$selected = ( in_array($name, $this->config['applimobile_func']) ? 'selected' : '');
				$fonctions_selector.= "
					<option value='".$name."' $selected >".htmlentities($label, ENT_QUOTES, $charset)."</option>";
			}
			$fonctions_selector.= "</select>";
			$result .= $fonctions_selector;

		}
        $result .="</div>";
		$result.= "<div class='row'>&nbsp;</div>";
		return $result;
	}


	public function update_config_from_form() {

		global $applimobile_operation_ws_url, $applimobile_renew_keys, $applimobile_operation_ws_user, $applimobile_operation_ws_password;
		global $applimobile_library_name, $applimobile_library_logo;
		global $applimobile_allow_booking, $applimobile_allow_password_change;
		global $applimobile_contact_template;
		global $applimobile_carousel_shelves, $applimobile_carousel_first_shelf_id;
		global $applimobile_facets, $applimobile_sorts, $applimobile_simple_search_types, $applimobile_advanced_search_fields;
		global $applimobile_associated_accounts_activate, $applimobile_rapid_search_localised;
		global $applimobile_func, $opac_url_base, $applimobile_external_auth_active;

		parent::update_config_from_form();

		$keys = [];
		$applimobile_renew_keys = intval($applimobile_renew_keys);
		if( !$applimobile_renew_keys && !empty($this->config['keys'])) {
			$keys = $this->config['keys'];
		}
		$this->config = [];

		$this->config['applimobile_operation_ws_url'] = stripslashes($applimobile_operation_ws_url);
		$this->config['applimobile_operation_ws_user'] = stripslashes($applimobile_operation_ws_user);
		$this->config['applimobile_operation_ws_password'] = stripslashes($applimobile_operation_ws_password);
		$this->config['applimobile_library_name'] = stripslashes($applimobile_library_name);
		$this->config['applimobile_library_logo'] = stripslashes($applimobile_library_logo);
		$this->config['applimobile_contact_template'] = stripslashes($applimobile_contact_template);
		if( !isset($applimobile_allow_booking) ) {
			$applimobile_allow_booking = 0;
		}
		$this->config['applimobile_allow_booking'] = intval($applimobile_allow_booking);
		if( !isset($applimobile_allow_password_change) ) {
			$applimobile_allow_password_change = 0;
		}
		$this->config['applimobile_allow_password_change'] = intval($applimobile_allow_password_change);
		$this->config['applimobile_carousel_shelves'] = [];
		if(is_array($applimobile_carousel_shelves) && count($applimobile_carousel_shelves)) {
			foreach($applimobile_carousel_shelves as $shelf_id) {
				$shelf_id = intval($shelf_id);
				if($shelf_id) {
					$this->config['applimobile_carousel_shelves'][] = $shelf_id;
				}
			}
		}
		if(!isset($applimobile_carousel_first_shelf_id)) {
			$applimobile_carousel_first_shelf_id = 0;
		}
		$this->config['applimobile_carousel_first_shelf_id'] = intval($applimobile_carousel_first_shelf_id);
		$this->config['applimobile_facets'] = [];
		if(is_array($applimobile_facets) && count($applimobile_facets)) {
			foreach($applimobile_facets as $facet_id) {
				$facet_id = intval($facet_id);
				if($facet_id) {
					$this->config['applimobile_facets'][] = $facet_id;
				}
			}
		}
		if(is_array($applimobile_func) && count($applimobile_func)) {
		    foreach($applimobile_func as $func) {
		        $this->config['applimobile_func'][] = $func;
			}
		}
		$this->config['applimobile_sorts'] = [];
		if(is_array($applimobile_sorts) && count($applimobile_sorts)) {
			foreach($applimobile_sorts as $sort_name) {
				$this->config['applimobile_sorts'][] = $sort_name;
			}
		}
		$this->config['applimobile_simple_search_types'] = [];
		if(is_array($applimobile_simple_search_types) && count($applimobile_simple_search_types)) {
			foreach($applimobile_simple_search_types as $type) {
				$this->config['applimobile_simple_search_types'][] = $type;
			}
		}
		$this->config['applimobile_advanced_search_fields'] = [];
		if(is_array($applimobile_advanced_search_fields) && count($applimobile_advanced_search_fields)) {
			foreach($applimobile_advanced_search_fields as $field) {
				$this->config['applimobile_advanced_search_fields'][] = $field;
			}
		}
		if(empty($keys)) {
			$this->config['keys'] = $this->generate_keys();
		} else {
			$this->config['keys'] = $keys;
		}
		$this->config['applimobile_associated_accounts_activate'] = isset($applimobile_associated_accounts_activate) ? '1' : '0';
		$this->config['applimobile_rapid_search_localised'] = isset($applimobile_rapid_search_localised) ? '1' : '0';
		$this->config['applimobile_external_auth_active'] = isset($applimobile_external_auth_active) ? '1' : '0';
		$this->config['applimobile_opac_url'] = $opac_url_base;

	}

	protected function generate_keys() {

		$b64_secret = base64_encode(openssl_random_pseudo_bytes(applimobile::SECRET_LEN));
		$b64_key = base64_encode(openssl_random_pseudo_bytes(applimobile::KEY_LEN));
		$iv_len = openssl_cipher_iv_length(applimobile::CIPHER);
		$b64_iv = base64_encode(openssl_random_pseudo_bytes($iv_len));
		$api_key = base64_encode(openssl_random_pseudo_bytes(applimobile::KEY_LEN));
		return ['secret'=>$b64_secret, 'key'=>$b64_key, 'iv'=>$b64_iv, 'api'=>$api_key];

	}

}
