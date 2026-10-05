<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.3 2021/12/09 09:01:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $msg, $empr_id, $bannette_abon;

require_once($class_path."/bannette_abon.class.php");
require_once($class_path."/emprunteur.class.php");

switch($sub){
	case 'get_form':
		$empr=new emprunteur($empr_id);
		ajax_http_send_response($empr->get_bannette_form());
		break;
	case 'save_abon':
		$instance_bannette_abon = new bannette_abon(0, $empr_id);
		ajax_http_send_response($instance_bannette_abon->save_bannette_abon($bannette_abon));
		break;
	case 'delete_abon':
		$instance_bannette_abon = new bannette_abon(0, $empr_id);
		ajax_http_send_response($instance_bannette_abon->delete_bannette_abon($bannette_abon));
		break;
	default:
		ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
		break;
}	
