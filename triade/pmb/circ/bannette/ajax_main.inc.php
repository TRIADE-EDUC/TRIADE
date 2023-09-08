<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2017-11-13 10:24:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/bannette_abon.class.php");
require_once($class_path."/emprunteur.class.php");

switch($sub){
	case 'get_form':
		$empr=new emprunteur($empr_id);
		ajax_http_send_response($form = $empr->get_bannette_form());
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
