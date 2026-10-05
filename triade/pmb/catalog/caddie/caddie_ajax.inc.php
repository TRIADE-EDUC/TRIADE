<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_ajax.inc.php,v 1.13 2020/10/21 11:14:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $sub, $idcaddie, $action, $id_item, $object_type, $caddie, $object, $object_id;

// functions particulières à ce module
require_once("./catalog/caddie/caddie_func.inc.php");
require_once("$include_path/templates/cart.tpl.php");
require_once("$include_path/expl_info.inc.php");
require_once("$class_path/caddie.class.php");
require_once("$class_path/serials.class.php");
require_once("$class_path/parameters.class.php") ;
require_once("$include_path/cart.inc.php");
require_once("$include_path/bull_info.inc.php");
require_once($class_path."/caddie/caddie_controller.class.php");

switch($sub) {
	case "pointage" :
		$idcaddie = caddie::check_rights($idcaddie) ;
		include('./catalog/caddie/pointage/main_ajax.inc.php');
		break;
	case "collecte" :
		$idcaddie = caddie::check_rights($idcaddie) ;
		include('./catalog/caddie/collecte/main_ajax.inc.php');
		break;
	case "list_from_item":
		$idcaddie = caddie::check_rights($idcaddie) ;
		if ($idcaddie) {
			$myCart = new caddie($idcaddie);
			switch($action) {
				case 'delete':
					$myCart->del_item($id_item);
					print caddie_controller::get_display_list_from_item('display', $object_type, $id_item);
					break;
				default:
					$myCart->add_item($id_item,$object_type);
					print caddie_controller::get_display_list_from_item('display', $object_type, $id_item);
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
				$idcaddie = caddie::check_rights($idcaddie) ;
				
				if ($idcaddie) {
					$myCart = new caddie($idcaddie);
					switch($action) {
						case 'delete':
							$myCart->del_item($object_id);
							break;
						default:
							$myCart->add_item($object_id,$object_type);
							break;
					}
					$myCart->compte_items();
				} else die("Failed: "."obj=".$object." caddie=".$caddie);
				print $myCart->nb_item;
				break;
		}
		break;
}