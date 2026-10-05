<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4 2022/03/03 08:06:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub, $action, $id_bull;
global $object_type;

switch($sub) {
	case "circ_ask" :
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'serialcirc');
				break;
		}
		break;
	case "pointage" :
		switch($action) {
			case "nonrecevable":
				$id_bull = intval($id_bull);
				$requete="update abts_grille_abt set state='3' where id_bull= '$id_bull' ";
				pmb_mysql_query($requete);
				
				$abt_id = abts_pointage::get_num_abt_from_id_bull($id_bull);
				if($abt_id) {
					abts_pointage::delete_retard($abt_id);
				}
				print encoding_normalize::json_encode(
						array(
								'id_bull' => $id_bull,
								'status' => true,
						)
				);
				break;
			case "list":
				lists_controller::proceed_ajax($object_type);
				break;
		}
		break;
	default:
		break;
}