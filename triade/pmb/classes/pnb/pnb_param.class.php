<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_param.class.php,v 1.4 2018-06-28 12:34:27 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/pnb/pnb_param.tpl.php");

class pnb_param {
	
	public function __construct(){
				
	}
	
	public function proceed() {
		global $action, $msg;
		
		switch ($action) {		
			case 'save':
				if($this->save()) {					
					print display_notification($msg['account_types_success_saved']);
				}
				print $this->get_form();
				break;
			case 'edit':
			default:
				print $this->get_form();
				break;
		}
	}
	
	private function get_form() {
		global $pnb_param_form, $msg, $charset;
		global $pmb_pnb_param_login, $pmb_pnb_param_password, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;
		global $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;
		global $pmb_pnb_param_dilicom_url, $opac_pnb_param_webservice_url;
		global $pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers;
		
		$tpl = $pnb_param_form;
		$tpl = str_replace('!!login!!', htmlentities($pmb_pnb_param_login, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!password!!', htmlentities($pmb_pnb_param_password, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!ftp_login!!', htmlentities($pmb_pnb_param_ftp_login, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!ftp_password!!', htmlentities($pmb_pnb_param_ftp_password, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!ftp_server!!', htmlentities($pmb_pnb_param_ftp_server, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!user_name!!', htmlentities($pmb_pnb_param_ws_user_name, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!user_password!!', htmlentities($pmb_pnb_param_ws_user_password, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!dilicom_url!!', htmlentities($pmb_pnb_param_dilicom_url, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!webservice_url!!', htmlentities($opac_pnb_param_webservice_url, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!alert_end_offers!!', htmlentities($pmb_pnb_alert_end_offers, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!alert_staturation_offers!!', htmlentities($pmb_pnb_alert_staturation_offers, ENT_QUOTES, $charset), $tpl);
		return $tpl;		
	}
	
	private function save() {
		global $login, $password, $ftp_login, $ftp_password, $ftp_server, $dilicom_url, $webservice_url, $user_name, $user_password, $alert_end_offers, $alert_staturation_offers;
		global $pmb_pnb_param_login, $pmb_pnb_param_password, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;
		global $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;
		global $pmb_pnb_param_dilicom_url, $opac_pnb_param_webservice_url;
		global $pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers;		

		$pmb_pnb_param_login = $login;
		$pmb_pnb_param_password = $password;
		$pmb_pnb_param_ftp_login = $ftp_login;
		$pmb_pnb_param_ftp_password =  $ftp_password;
		$pmb_pnb_param_ftp_server =  $ftp_server;
		$pmb_pnb_param_ws_user_name =  $user_name;
		$pmb_pnb_param_ws_user_password =  $user_password;
		$pmb_pnb_param_dilicom_url =  $dilicom_url;
		$opac_pnb_param_webservice_url =  $webservice_url;
		$pmb_pnb_alert_end_offers = $alert_end_offers;
		$pmb_pnb_alert_staturation_offers = $alert_staturation_offers;
		
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_login."' WHERE type_param= 'pmb' and sstype_param='pnb_param_login'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_password."' WHERE type_param= 'pmb' and sstype_param='pnb_param_password'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_ftp_login."' WHERE type_param= 'pmb' and sstype_param='pnb_param_ftp_login'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_ftp_password."' WHERE type_param= 'pmb' and sstype_param='pnb_param_ftp_password'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_ftp_server."' WHERE type_param= 'pmb' and sstype_param='pnb_param_ftp_server'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_ws_user_name."' WHERE type_param= 'pmb' and sstype_param='pnb_param_ws_user_name'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_ws_user_password."' WHERE type_param= 'pmb' and sstype_param='pnb_param_ws_user_password'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_param_dilicom_url."' WHERE type_param= 'pmb' and sstype_param='pnb_param_dilicom_url'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".$opac_pnb_param_webservice_url."' WHERE type_param= 'opac' and sstype_param='pnb_param_webservice_url'";
		pmb_mysql_query($query);			
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_alert_end_offers."' WHERE type_param= 'pmb' and sstype_param='pnb_alert_end_offers'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_alert_staturation_offers."' WHERE type_param= 'pmb' and sstype_param='pnb_alert_staturation_offers'";
		pmb_mysql_query($query);
		return true;
	}
}