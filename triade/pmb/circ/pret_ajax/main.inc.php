<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2021/12/09 10:11:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $sub;
global $id_expl, $id_empr, $cb_doc, $cb_empr, $cb_list, $short_loan, $del_pret, $forcage, $force;

require_once($class_path."/ajax_pret.class.php");
require_once($class_path."/ajax_retour_class.php");
require_once("$include_path/ajax.inc.php");

//En fonction de $sub, il inclut les fichiers correspondants
switch($sub):
	case 'confirm_pret':
		$expl = new do_pret();
		if ( is_array($id_expl)) {
			foreach($id_expl as $id) {
				$status= $expl->confirm_pret($id_empr, $id, $short_loan, 'gestion_rfid');
			}
			ajax_http_send_response("$status","text/xml");
		} else {
			$status = $expl->confirm_pret($id_empr, $id_expl, $short_loan, 'gestion_rfid');
			ajax_http_send_response("$status","text/xml");
		}
		break;
	case 'get_info_expl':
		$info_expl = new do_pret();
		$status = $info_expl->get_info_expl($cb_doc);
		ajax_http_send_response("$status","text/xml");
		break;
	case 'add_cb':
		$pret = new do_pret();
		$status = $pret->check_pieges($cb_empr, $id_empr,$cb_doc, $id_expl,$forcage,$short_loan);
		ajax_http_send_response("$status","text/xml");
		break;
	case 'del_pret':
		$pret = new do_pret();
		$status = $pret->del_pret($id_expl);
		ajax_http_send_response("$status","text/xml");
		break;	
	case 'do_retour':
		$retour = new retour();
		$status = $retour->do_retour($cb_doc);
		ajax_http_send_response("$status","text/xml");
		break;	
	case 'add_cb_list':
		//input: cb_list, id_empr;
		if(empty($cb_empr)) $cb_empr = '';
		if(!empty($del_pret)){
			$query = "delete from pret where pret_idempr = '" . $id_empr . "' and pret_temp = '".$_SERVER['REMOTE_ADDR']."'";
			pmb_mysql_query($query);
		}
		$result=array();
		$erreur = 0;
		if(isset($cb_list) && is_array($cb_list) && count($cb_list)){
			foreach($cb_list as $cb_doc){
				// init de la class
				$info_1=array();
				$info_2=array();
				$pret = new do_pret();
				$info_1 = $pret->mode1_get_info_expl($cb_doc);
				if($info_1["error_message"]) $erreur=1;
				$id_expl=$info_1["expl_id"];
				$forcage = 0;
				if(!empty($force[$cb_doc])) {
					$forcage=$force[$cb_doc];
				}
				if(!$erreur) $info_2 = $pret->mode1_check_pieges($cb_empr, $id_empr,$cb_doc, $id_expl,$forcage);
				if($info_2["error_message"]) $erreur=1;
				$result[] = array_merge($info_1, $info_2);
			}
		}
		ajax_http_send_response(array2xml($result),"text/xml");
		break;
	default:
		ajax_http_send_error('400',"commande inconnue");
		break;		
endswitch;	
