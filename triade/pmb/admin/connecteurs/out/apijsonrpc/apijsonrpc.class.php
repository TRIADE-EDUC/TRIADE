<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: apijsonrpc.class.php,v 1.18 2024/02/27 11:13:08 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
global $charset;
global $auth_user;
global $auth_pw;
global $auth_connexion_phrase;
global $api_exported_functions, $authentication_type, $authorized_groups;

require_once $class_path."/connecteurs_out.class.php";
require_once $class_path."/external_services.class.php";
require_once $class_path."/external_services_esusers.class.php";
require_once $base_path."/admin/connecteurs/out/apijsonrpc/apijsonrpc_jsonrpcserver.class.php";

class apijsonrpc extends connecteur_out {
	public $json_input = array();

	public function get_config_form() {
	    $result = $this->msg["apijsonrpc_no_configuration_required"];
		return $result;
	}

	public function update_config_from_form() {
		return;
	}

	public function instantiate_source_class($source_id) {
		return new apijsonrpc_source($this, $source_id, $this->msg);
	}

	//On chargera nous même les messages si on en a besoin
	public function need_global_messages() {
		return false;
	}

	public function process($source_id, $pmb_user_id) {

		$apijsonrpc_jsonrpcserver = new apijsonrpc_jsonrpcserver($this);
		$apijsonrpc_jsonrpcserver->process($source_id, $pmb_user_id, $this->json_input);

		//Rien
		return;
	}

	public function return_json_error($message, $request) {
		$response = array (
			'id' => $request['id'],
			'result' => NULL,
			'error' => $message
		);
		// output the response
		if (!empty($request['id'])) {
			header('content-type: application/json;charset=utf-8');
			echo json_encode($response);
		}
		die();
	}

	public function get_running_pmb_userid($source_id) {
	    global $auth_user;
	    global $auth_pw;

		$user_id = 1;
		$this->json_input = json_decode(file_get_contents('php://input'),true);
		if (!$this->json_input)
			return 1;

		$sc = $this->instantiate_source_class($source_id);
		// Ajout pour Bibloto
		if (!empty($auth_user) && !empty($auth_pw)) {
		    $this->json_input["auth_user"] = $auth_user;
		    $this->json_input["auth_pw"] = md5($auth_user . $auth_pw . $sc->config['auth_connexion_phrase'] . $this->json_input["id"] . $this->json_input["method"]);
		}

		$credentials_user = '';
		$credentials_password = '';

		if (isset($this->json_input["auth_user"])) {
			$credentials_user = $this->json_input["auth_user"];
			if (isset($this->json_input["auth_pw"])) {
				//Vérification du hash salé double
				$requete="select esuser_password from es_esusers where esuser_username='".addslashes($credentials_user)."'";
				$resultat=pmb_mysql_query($requete);
				if ($resultat) {
					$pwd=pmb_mysql_result($resultat,0,0);

					$salt=md5($credentials_user.md5($pwd).$sc->config['auth_connexion_phrase'].$this->json_input["id"].$this->json_input["method"]);
					if ($salt==$this->json_input["auth_pw"]) $credentials_password=$pwd;
				}
			}
		} else if (isset($_SERVER['PHP_AUTH_USER'])) {
			$credentials_user = $_SERVER['PHP_AUTH_USER'];
			$credentials_password = $_SERVER['PHP_AUTH_PW'];
		}

		if (!$credentials_user) {
			//Si on ne nous fourni pas de credentials, alors on teste l'utilisateur anonyme
			$user_id = connector_out_check_credentials('', '', $source_id);
			if ($user_id === false) {
			    $this->return_json_error('Access with no credentials is forbidden.', $this->json_input);
			}
		} else {
			$user_id = connector_out_check_credentials($credentials_user, $credentials_password, $source_id);
			if ($user_id === false) {
			    $this->return_json_error('Bad credentials.', $this->json_input);
			}
		}

		return $user_id;
	}
}

class apijsonrpc_source extends connecteur_out_source {

	public function get_config_form() {
		global $charset;

		if(!isset($this->config['auth_connexion_phrase'])){
		    $this->config['auth_connexion_phrase'] = "";
		}

		$result = parent::get_config_form();
		$result .= "
            <div class='row'>
                <label for='auth_connexion_phrase'>".htmlentities($this->msg['apijsonrpc_auth_connexion_phrase'],ENT_QUOTES,$charset)."</label>
            </div>
            <div class='row'>
                <input type='text' name='auth_connexion_phrase' class='saisie-80em' value='".htmlentities($this->config['auth_connexion_phrase'],ENT_QUOTES,$charset)."'/>
            </div>";

		$api_catalog = es_catalog::get_instance();
		$api_functions = array();
		foreach ($api_catalog->groups as $agroup) {
			foreach ($agroup->methods as $amethod) {
				$api_functions[$agroup->name][] = $amethod->name;
			}
		}

		if (!isset($this->config["exported_functions"])) {
			$this->config["exported_functions"] = array();
		}
		$selected_functions = array();
		foreach ($this->config["exported_functions"] as $afunction) {
			$selected_functions[] = $afunction["group"]."|_|".$afunction["name"];
		}

		//Adresse d'utilisation
		$result .= '<div class=row><label class="etiquette" for="api_exported_functions">'.$this->msg["apijsonrpc_service_endpoint"].'</label><br />';
		if ($this->id) {
			$result .= '<a target="_blank" href="ws/connector_out.php?source_id='.$this->id.'">ws/connector_out.php?source_id='.$this->id.'</a>';
		} else {
			$result .= $this->msg["apijsonrpc_service_endpoint_unrecorded"];
		}
		$result .= "</div>";

		//Fonction exportées
		$result  .= '<div class=row><label class="etiquette" for="api_exported_functions">'.$this->msg["apijsonrpc_exported_functions"].'</label><br />';
		$api_select = '<select MULTIPLE name="api_exported_functions[]" size="20">';
		foreach ($api_functions as $agroup_name => $agroup) {
			$api_select .= '<optgroup label="'.htmlentities($agroup_name ,ENT_QUOTES, $charset).'">';
			foreach ($agroup as $amethodname) {
				$davalue = $agroup_name."|_|".$amethodname;
				$api_select .= '<option '.(in_array($davalue, $selected_functions) ? 'selected' : "").' value="'.htmlentities($davalue ,ENT_QUOTES, $charset).'">'.htmlentities($amethodname ,ENT_QUOTES, $charset).'</option>';
			}
			$api_select .= '</optgroup>';
		}
		$api_select .= '</select>';
		$result .= $api_select;
		$result .= "</div>";

		return $result;
	}

	public function update_config_from_form() {
		parent::update_config_from_form();
		global $auth_connexion_phrase;
		global $api_exported_functions, $authentication_type, $authorized_groups;

		$this->config['auth_connexion_phrase']= stripslashes($auth_connexion_phrase);

		if (!$api_exported_functions) {
			$api_exported_functions = array();
		}
		if (!isset($authentication_type) || !$authentication_type) {
			$authentication_type = 'none';
		}
		if (!isset($authorized_groups) || !$authorized_groups) {
			$authorized_groups = array();
		}
		//Récupérons la liste des fonctions pour virer de l'entrée les noms de fonctions qui n'existent pas
		$api_catalog = es_catalog::get_instance();
		$api_functions = array();
		foreach ($api_catalog->groups as $agroup) {
			foreach ($agroup->methods as $amethod) {
				$api_functions[] = $agroup->name."|_|".$amethod->name;
			}
		}
		$api_exported_functions = array_intersect($api_exported_functions, $api_functions);

		//Enregistrons
		$config_exported = array();
		foreach ($api_exported_functions as $afunction) {
			$dafunction = explode("|_|", $afunction);
			$config_exported[] = array("group" => $dafunction[0], "name" => $dafunction[1]);
		}
		$this->config["exported_functions"] = $config_exported;

		return;
	}

}
