<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_param.class.php,v 1.9 2021/02/02 10:00:36 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path, $msg, $charset;
global $action, $pnb_param_form;

global $dilicom_url, $login, $password,
$ftp_login, $ftp_password, $ftp_server,
$webservice_url, $user_name, $user_password,
$alert_end_offers, $alert_staturation_offers, $alert_threshold_tokens,
$doctype_selector, $location_selector, $section_selector, $statut_selector, $codestat_selector, $owner_selector;

global $pmb_pnb_param_dilicom_url, $pmb_pnb_param_login, $pmb_pnb_param_password,
$pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server,
$opac_pnb_param_webservice_url, $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password,
$pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers, $pmb_pnb_alert_threshold_tokens,
$pmb_pnb_typedoc_id, $pmb_pnb_location_id, $pmb_pnb_section_id, $pmb_pnb_statut_id, $pmb_pnb_codestat_id, $pmb_pnb_owner_id;

require_once $include_path."/templates/pnb/pnb_param.tpl.php";

class pnb_param {
	
	
	private function __construct() {
	}
	
	
	public static function proceed() {
		global $action, $msg;
		
		switch ($action) {		
			case 'save':
				if(static::save()) {					
					print display_notification($msg['account_types_success_saved']);
				}
				print static::get_form();
				break;
			case 'edit':
			default:
				print static::get_form();
				break;
		}
	}
	
	
	private static function get_form() {
		
		global $pnb_param_form, $charset;
		global $pmb_pnb_param_login, $pmb_pnb_param_password, $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server;
		global $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password;
		global $pmb_pnb_param_dilicom_url, $opac_pnb_param_webservice_url;
		global $pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers, $pmb_pnb_codestat_id, $pmb_pnb_statut_id, $pmb_pnb_typedoc_id;
		global $pmb_pnb_alert_threshold_tokens;
		global $pmb_pnb_location_id, $pmb_pnb_section_id, $pmb_pnb_owner_id;
		
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
		$tpl = str_replace('!!alert_threshold_tokens!!', htmlentities($pmb_pnb_alert_threshold_tokens, ENT_QUOTES, $charset), $tpl);
		$tpl = str_replace('!!typedoc!!', do_selector('docs_type', 'doctype_selector', $pmb_pnb_typedoc_id), $tpl);	
		$tpl = str_replace('!!location!!', do_selector('docs_location', 'location_selector', $pmb_pnb_location_id), $tpl);	
		
		
		$section_rqt = "select idsection, section_libelle, num_location FROM docsloc_section join docs_section on idsection=num_section order by section_libelle";
		$section_result = pmb_mysql_query($section_rqt);
		$tmp_sections = [];
		if(pmb_mysql_num_rows($section_result)) {
			while($row = pmb_mysql_fetch_assoc($section_result)) {
				if( !isset($tmp_sections[$row['idsection']]) ) {
					$tmp_sections[$row['idsection']] = [
							'section_libelle' => $row['section_libelle'],
					];
				}
				$tmp_sections[$row['idsection']]['locations'][] = $row['num_location'];
			}
		}
		$section_selector = "<select id='section_selector' name='section_selector' >";
		foreach($tmp_sections as $k_section => $section) {
			$section_selector.= "<option value='".$k_section."' data-locations='".json_encode($section['locations'])."' ";
			if( !in_array($pmb_pnb_location_id, $section['locations']) ) {
							$section_selector.= "style='display:none;' ";
			}
			if( $pmb_pnb_section_id == $k_section ) {
				$section_selector.= "selected ";
			}
			$section_selector.= ">".htmlentities($section['section_libelle'], ENT_QUOTES, $charset)."</option>";
		}
 		$section_selector.= "</select>";
		$tpl = str_replace('!!section!!', $section_selector, $tpl);	
		$tpl = str_replace('!!statut!!', do_selector('docs_statut', 'statut_selector', $pmb_pnb_statut_id), $tpl);		
		$tpl = str_replace('!!codestat!!', do_selector('docs_codestat', 'codestat_selector', $pmb_pnb_codestat_id), $tpl);		
		$tpl = str_replace('!!owner!!', do_selector('lenders', 'owner_selector', $pmb_pnb_owner_id), $tpl);		
		
		return $tpl;		
	}
	
	
	private static function save() {
		
		global $dilicom_url, $login, $password,
	    $ftp_login, $ftp_password, $ftp_server, 
	    $webservice_url, $user_name, $user_password, 
	    $alert_end_offers, $alert_staturation_offers, $alert_threshold_tokens, 
	    $doctype_selector, $location_selector, $section_selector, $statut_selector, $codestat_selector, $owner_selector;
	    
	    global $pmb_pnb_param_dilicom_url, $pmb_pnb_param_login, $pmb_pnb_param_password, 
	    $pmb_pnb_param_ftp_login, $pmb_pnb_param_ftp_password, $pmb_pnb_param_ftp_server, 
	    $opac_pnb_param_webservice_url, $pmb_pnb_param_ws_user_name, $pmb_pnb_param_ws_user_password, 
	    $pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers, $pmb_pnb_alert_threshold_tokens, 
	    $pmb_pnb_typedoc_id, $pmb_pnb_location_id, $pmb_pnb_section_id, $pmb_pnb_statut_id, $pmb_pnb_codestat_id, $pmb_pnb_owner_id;
		
		$pmb_pnb_param_dilicom_url =  $dilicom_url;
		$pmb_pnb_param_login = $login;
		$pmb_pnb_param_password = $password;
		
		$pmb_pnb_param_ftp_login = $ftp_login;
		$pmb_pnb_param_ftp_password =  $ftp_password;
		$pmb_pnb_param_ftp_server =  $ftp_server;
		
		$opac_pnb_param_webservice_url =  $webservice_url;
		$pmb_pnb_param_ws_user_name =  $user_name;
		$pmb_pnb_param_ws_user_password =  $user_password;
		
		$pmb_pnb_alert_end_offers = intval($alert_end_offers);
		$pmb_pnb_alert_staturation_offers = intval($alert_staturation_offers);
		$pmb_pnb_alert_threshold_tokens = intval($alert_threshold_tokens);

		$pmb_pnb_typedoc_id = intval($doctype_selector);
		$pmb_pnb_location_id = intval($location_selector);
		$pmb_pnb_section_id = intval($section_selector);
		$pmb_pnb_statut_id = intval($statut_selector);
		$pmb_pnb_codestat_id = intval($codestat_selector);
		$pmb_pnb_owner_id = intval($owner_selector);
		
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_login)."' WHERE type_param='pmb' and sstype_param='pnb_param_login'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_password)."' WHERE type_param='pmb' and sstype_param='pnb_param_password'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_ftp_login)."' WHERE type_param='pmb' and sstype_param='pnb_param_ftp_login'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_ftp_password)."' WHERE type_param='pmb' and sstype_param='pnb_param_ftp_password'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_ftp_server)."' WHERE type_param='pmb' and sstype_param='pnb_param_ftp_server'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_ws_user_name)."' WHERE type_param='pmb' and sstype_param='pnb_param_ws_user_name'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_ws_user_password)."' WHERE type_param='pmb' and sstype_param='pnb_param_ws_user_password'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".addslashes($pmb_pnb_param_dilicom_url)."' WHERE type_param='pmb' and sstype_param='pnb_param_dilicom_url'";
		pmb_mysql_query($query);		
		$query = "UPDATE parametres set valeur_param = '".addslashes($opac_pnb_param_webservice_url)."' WHERE type_param='opac' and sstype_param='pnb_param_webservice_url'";
		pmb_mysql_query($query);			
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_alert_end_offers."' WHERE type_param='pmb' and sstype_param='pnb_alert_end_offers'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_alert_staturation_offers."' WHERE type_param='pmb' and sstype_param='pnb_alert_staturation_offers'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_codestat_id."' WHERE type_param='pmb' and sstype_param='pnb_codestat_id'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_statut_id."' WHERE type_param='pmb' and sstype_param='pnb_statut_id'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_typedoc_id."' WHERE type_param='pmb' and sstype_param='pnb_typedoc_id'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_alert_threshold_tokens."' WHERE type_param='pmb' and sstype_param='pnb_alert_threshold_tokens'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_location_id."' WHERE type_param='pmb' and sstype_param='pnb_location_id'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_section_id."' WHERE type_param='pmb' and sstype_param='pnb_section_id'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres set valeur_param = '".$pmb_pnb_owner_id."' WHERE type_param='pmb' and sstype_param='pnb_owner_id'";
		pmb_mysql_query($query);
		return true;
	}
}