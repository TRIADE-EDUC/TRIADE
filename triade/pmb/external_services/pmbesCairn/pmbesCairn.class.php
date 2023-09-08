<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesCairn.class.php,v 1.3 2017-05-15 12:15:47 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/external_services_caches.class.php");
require_once($class_path."/sessions_tokens.class.php");
require_once($base_path."/admin/connecteurs/in/cairn/cairn.class.php");
require_once($class_path."/encoding_normalize.class.php");

class pmbesCairn extends external_services_api_class{
	public $error=false;		//Y-a-t-il eu une erreur
	public $error_message="";	//Message correspondant à l'erreur
	
	function form_general_config() {
		return false;
	}
	
	public function check_token($token) {
		/**
		 * On renvoie les infos emprunteur (id persistant, date d'expiration de la session, id empr, id cairn)
		 */
		$response = array(
				'SESSID' => '',
				'id_cairn' => '',
				'id_empr' => '',
				'expiration' => 0,
				'error' => false,
				'error_message' => ''
		);
		
		$sessions_token = new sessions_tokens('cairn');
		$sessions_token->set_token($token);
		
		if (!$sessions_token->is_valid()) {
			$this->error = true;
			$this->error_message = $this->msg['cairn_error_invalid_token'];
			$response['error'] = $this->error;
			$response['error_message'] = $this->error_message.' : '.$token;
			return encoding_normalize::utf8_normalize($response);
		}
		// La récupération du login suffit à valider le token et la session	
		$sessid =  md5($sessions_token->get_SESSID());
		$session_expiration = $sessions_token->get_expiration();
		$empr_login = $sessions_token->get_login();
		$cairn_connector = new cairn();
		$empr_id = $cairn_connector->get_empr_id($empr_login);	
		$id_cairn = $cairn_connector->get_cairn_id();

		$response['SESSID'] = $sessid;
		$response['id_cairn'] = $id_cairn;
		$response['id_empr'] = $empr_id;
		$response['expiration'] = $session_expiration;
		
		return encoding_normalize::utf8_normalize($response);
	}
}
