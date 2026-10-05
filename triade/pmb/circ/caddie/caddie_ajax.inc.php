<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_ajax.inc.php,v 1.11 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $sub, $idcaddie, $action, $id_item, $charset, $action, $caddie, $object, $object_type;

require_once("$class_path/empr_caddie.class.php");
require_once($include_path."/empr_cart.inc.php");
require_once($class_path."/caddie/empr_caddie_controller.class.php");

switch($sub) {
	case "pointage" :
		$idcaddie = empr_caddie::check_rights($idcaddie) ;
		if($idcaddie) {
			$myCart = new empr_caddie($idcaddie);
			switch ($action) {
				case 'add_item':
					if($id_item) {
						$res_pointage = $myCart->pointe_item($id_item);
					}
					break;
				case 'del_item':
					$res_pointage = $myCart->depointe_item($id_item);
					break;
				default:
					break;
			}
			$aff_cart_nb_items = $myCart->aff_cart_nb_items();
		}
		
		if(!$id_item) $id_item = 0;
		if(!$idcaddie) $idcaddie = 0;
		if(!$res_pointage) $res_pointage = 0;
		$result = array(
				'id'=>$id_item,
				'idcaddie'=>$idcaddie,
				'res_pointage'=>$res_pointage,
				'aff_cart_nb_items'=>($charset != "utf-8" ? encoding_normalize::utf8_normalize($aff_cart_nb_items) : $aff_cart_nb_items)
		);
		ajax_http_send_response($result);
		break;
	case "list_from_item":
		$idcaddie = empr_caddie::check_rights($idcaddie) ;
		if ($idcaddie) {
			$myCart = new empr_caddie($idcaddie);
			switch($action) {
				case 'delete':
					$myCart->del_item($id_item);
					print empr_caddie_controller::get_display_list_from_item('display', 'EMPR', $id_item);
					break;
				default:
					$myCart->add_item($id_item);
					print empr_caddie_controller::get_display_list_from_item('display', 'EMPR', $id_item);
					break;
			}
		}
		break;
	default:
	    switch($action) {
	        case "list":
	            require_once($class_path.'/caddie/caddie_root_lists_controller.class.php');
	            caddie_root_lists_controller::proceed_ajax($object_type, 'caddie_content');
	            break;
	        default:
        		$idcaddie=substr($caddie,5);
        		$object_type=substr($object,0,4);
        		$object_id=substr($object,10);
        		$idcaddie = empr_caddie::check_rights($idcaddie) ;
        		if ($idcaddie) {
        			$myCart = new empr_caddie($idcaddie);
        			switch($action) {
        				case 'delete':
        					$myCart->del_item($object_id);
        					break;
        				default:
        					$myCart->add_item($object_id);
        					break;
        			}
        			$myCart->compte_items();
        		} else die("Failed: "."obj=".$object." caddie=".$caddie);
        		print $myCart->nb_item;
        		break;
	    }
		break;
}


?>