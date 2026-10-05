<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.php,v 1.41.8.1 2025/01/29 07:27:26 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";
$base_title = "";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");

global $class_path, $javascript_path, $include_path, $msg, $action;
global $idcaddie, $item, $what, $object_type;
global $list_ui_objects_type, $pager, $current_page_objects, $selected_objects;

if(!isset($idcaddie)) $idcaddie = 0;
if(!isset($item)) $item = 0;

switch ($object_type) {
	case "EXPL":
		$base_title = $msg['expl_carts'];
		break;
	case "EMPR":
	case "GROUP":
		$base_title = $msg['empr_carts'];
		break;
	case "BULL":
		$base_title = $msg['bull_carts'];
		break;
	case "NOTI":
		$base_title = $msg[396];
		break;
	default:
		if($object_type) { // Afin de contourner les appels en paniers de notices sans "object_type"
			$base_title = $msg['authorities_carts'];
		} else {
			$base_title = $msg[396];
		}
		break;
	}
	
// modules propres à cart.php ou à ses sous-modules
require_once($include_path."/cart.inc.php");
require_once($include_path."/templates/cart.tpl.php");
require_once($include_path."/isbn.inc.php");
require_once($include_path."/expl_info.inc.php");
require_once($include_path."/bull_info.inc.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once($class_path."/cart.class.php");
require_once($class_path."/caddie.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/emprunteur.class.php");
require_once($javascript_path."/misc.inc.php");
require_once($class_path."/empr_caddie.class.php");
require_once($class_path."/caddie_root.class.php");

require_once($class_path.'/interface/autorites/interface_autorites_form.class.php');
require_once($class_path.'/interface/catalog/interface_catalog_form.class.php');
require_once($class_path.'/interface/circ/interface_circ_form.class.php');

print window_title($base_title);

if (!$empr_show_caddie && $object_type=="EMPR") die();
print $expand_result;

print "<div id='contenu-frame'>";

// ne pas afficher les liens d'ajout aux caddies
$cart_link_non=1;

// afin de vérifier les droits sur le caddie :
$myCartTemp=new caddie($idcaddie) ;
if (!$myCartTemp->idcaddie) $idcaddie=0;

// gestion id de notice fille, concaténé avec l'id de la mère
if (($pos=strpos($item, "_p"))) {	
	$item=substr($item,0,$pos);   	 
}
// constante pour afficher le lien de suppr du panier
switch ($action) {
	case 'new_cart':
		$myCart = caddie_root::get_instance_from_object_type($object_type);	
		$myCart_url_base = "./cart.php?object_type=".$object_type."&item=".$item;
		$myCart_url_base .= (!empty($list_ui_objects_type) ? "&list_ui_objects_type=".$list_ui_objects_type : "").(!empty($what) ? "&what=".$what : '').(isset($current_print) ? "&current_print=".$current_print : "");
		$myCart_url_base .= (isset($pager) ? "&pager=".$pager : "") . (isset($include_child) ? "&include_child=".$include_child : "") . (isset($include_bulletin_notice) ? "&include_bulletin_notice=".$include_bulletin_notice : "") . (isset($include_analysis) ? "&include_analysis=".$include_analysis : ""); 
		$myCart_url_base .= (!empty($current_page_objects) ? "&current_page_objects=".$current_page_objects : "") . (!empty($selected_objects) ? "&selected_objects=".$selected_objects : "");
		$myCart->type = $object_type;
		print $myCart->get_form($myCart_url_base);
	break;
	case 'del_cart':
		$myCart = caddie_root::get_instance_from_object_type($object_type, $idcaddie);
		$myCart->delete();
	break;
	case 'valid_new_cart':
		$myCart = caddie_root::get_instance_from_object_type($object_type);
		$myCart->set_properties_from_form();
		$idcaddie_new = $myCart->create_cart();	
	break;
}

switch ($object_type) {
	case "EXPL":
		require_once ("carts/exemplaire.inc.php");
		break;
	case "EMPR":
	case "GROUP":
		require_once ("carts/empr.inc.php");
		break;
	case "BULL":
		require_once ("carts/bulletin.inc.php");
		break;
	case "NOTI":
		require_once ("carts/notice.inc.php");
		break;
	default:
		if($object_type) { // Afin de contourner les appels en paniers de notices sans "object_type"
			require_once ("carts/authority.inc.php");
		} else {
			require_once ("carts/notice.inc.php");
		}
		break;
}

print "<script>self.focus();</script>";

print $footer;

html_builder();

pmb_mysql_close();
