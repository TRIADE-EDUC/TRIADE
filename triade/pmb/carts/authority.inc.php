<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority.inc.php,v 1.7 2019/05/29 12:42:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $item, $msg, $action, $idcaddie, $object_type, $current_print;

require_once($class_path."/caddie/authorities_caddie_controller.class.php");
if($item) {
	print "<h1>".$msg["400"]."</h1>";
	$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $item);
	print $authority->get_object_instance()->get_header();
}
switch($action) {
	case 'add_item':
		// cas du click sur le lien du panier
		if($idcaddie)$caddie[0]=$idcaddie;
		// Pour tous les paniers cochés
		foreach($caddie  as $idcaddie) {
			$myCart = new authorities_caddie($idcaddie);
			$myCart->add_item($item,$object_type);
			$myCart->compte_items();
		}
		print "<script type='text/javascript'>window.close();</script>"; 
		break;
	case 'new_cart':
		break;
	case 'del_cart':
	case 'valid_new_cart':		
	default:
		if(isset($current_print) && $current_print) {
			$authorities_caddie = 1;
			$action="print_prepare";			
			require_once("./print_cart.php");			
		} else {
			authorities_caddie_controller::get_aff_paniers_in_cart($object_type, $item);
		}	
		break;
}
/**
 * TODO: something else
 */