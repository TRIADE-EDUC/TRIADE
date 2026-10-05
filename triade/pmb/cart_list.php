<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart_list.php,v 1.20 2023/12/08 08:53:19 dgoron Exp $

$base_path=".";
$base_noheader=1;
$base_nobody=1;

$base_auth = "CATALOGAGE_AUTH";
require_once("includes/init.inc.php");

global $class_path, $msg, $charset;
global $id_object, $type_object;

require_once("$class_path/caddie.class.php");
require_once("$class_path/empr_caddie.class.php");
require_once($class_path.'/event/events/event_caddie.class.php');
require_once($class_path.'/caddie/caddie_controller.class.php');
require_once($class_path.'/caddie/empr_caddie_controller.class.php');
require_once($class_path.'/caddie/authorities_caddie_controller.class.php');

header("Content-Type: text/html; charset=$charset");

//si id_object est présent, il s'agit de l'accès rapide aux paniers (div apparaissant sur mouseOver icone panier)
//sinon, il s'agit de la liste des paniers pour le drag and drop

$id_object = intval($id_object);
if ($id_object) {
	if(!isset($type_object) || !$type_object) $type_object = 'NOTI';
	switch ($type_object) {
		case 'EMPR' :
			empr_caddie_controller::proceed_quick_access($id_object, $type_object);
			break;
		case 'EXPL' :
		case 'BULL' :
		case 'NOTI' :
			caddie_controller::proceed_quick_access($id_object, $type_object);
			break;
		default :
			authorities_caddie_controller::proceed_quick_access($id_object, $type_object);
			break;
	}
} else {
	$list_noti=caddie::get_cart_list("NOTI",0);
	$list_expl=caddie::get_cart_list("EXPL",0);
	$list_bull=caddie::get_cart_list("BULL",0);
	$list_empr=array();
	
	$is_cart=0;
	$link="<a href='#' id='close_cart_pannel' ><img style='border:0px' class='align_middle' src='".get_url_icon('close.gif')."'/></a>";
	
	print "
    <div>
        <table style='width:100%' role='presentation'>
            <tr>
                <td class='align_left' style='width:90%'></td>
                <td class='align_right'>$link</td>
            </tr>
        </table>
    </div>";
	
	if (count($list_noti)) {
		print "<h3>$msg[396]</h3><br />";
		for ($i=0; $i<count($list_noti); $i++) {
			$cart_link= "catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=NOTI&idcaddie=".$list_noti[$i]["idcaddie"]."&item=0";
			$pannel_cart_see = "";
			$pannel_cart_link = $cart_link;
			print "<div id=\"NOTI_".$list_noti[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='".get_url_icon('basket_20x20.gif')."'/>&nbsp;<a href=\"".$pannel_cart_link."\">".htmlentities($list_noti[$i]["name"],ENT_QUOTES,$charset)."<span id=\"NOTI_nbitem_".$list_noti[$i]["idcaddie"]."\"> (".$list_noti[$i]["nb_item"].")</span></a>".$pannel_cart_see."</div>";
		}
		$is_cart++;
	}
	if (count($list_expl)) {
		print "<br />";
		print "<h3>$msg[expl_carts]</h3><br />";
		for ($i=0; $i<count($list_expl); $i++) {
			$cart_link = "catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=EXPL&idcaddie=".$list_expl[$i]["idcaddie"]."&item=0";
			$pannel_cart_see = "";
			$pannel_cart_link = $cart_link;
			print "<div id=\"EXPL_".$list_expl[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='".get_url_icon('basket_20x20.gif')."'/>&nbsp;<a href=\"".$pannel_cart_link."\">".htmlentities($list_expl[$i]["name"],ENT_QUOTES,$charset)."<span id='EXPL_nbitem_".$list_expl[$i]["idcaddie"]."'> (".$list_expl[$i]["nb_item"].")</span></a>".$pannel_cart_see."</div>";
		}
		$is_cart++;
	}
	if (count($list_bull)) {
		print "<br />";
		print "<h3>$msg[bull_carts]</h3><br />";
		for ($i=0; $i<count($list_bull); $i++) {
			$cart_link = "catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=BULL&idcaddie=".$list_bull[$i]["idcaddie"]."&item=0";
			$pannel_cart_see = "";
			$pannel_cart_link = $cart_link;
			print "<div id=\"BULL_".$list_bull[$i]["idcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='".get_url_icon('basket_20x20.gif')."'/>&nbsp;<a href=\"".$pannel_cart_link."\">".htmlentities($list_bull[$i]["name"],ENT_QUOTES,$charset)."<span id='BULL_nbitem_".$list_bull[$i]["idcaddie"]."'> (".$list_bull[$i]["nb_item"].")</span></a>".$pannel_cart_see."</div>";
		}
		$is_cart++;
	}
	if (count($list_empr)) {
		print "<br />";
		print "<h3>".$msg['empr_carts']."</h3><br />";
		for ($i=0; $i<count($list_empr); $i++) {
			$cart_link = "circ.php?categ=caddie&sub=gestion&quoi=panier&action=&idemprcaddie=".$list_empr[$i]["idemprcaddie"]."&item=0";
			$pannel_cart_see = "";
			$pannel_cart_link = $cart_link;
			print "<div id=\"EMPR_".$list_empr[$i]["idemprcaddie"]."\" recept=\"yes\" recepttype=\"caddie\" downlight=\"cart_downlight\" highlight=\"cart_highlight\"><img src='".get_url_icon('basket_empr.gif')."'/>&nbsp;<a href=\"".$pannel_cart_link."\">".htmlentities($list_empr[$i]["name"],ENT_QUOTES,$charset)."<span id='EMPR_nbitem_".$list_empr[$i]["idemprcaddie"]."'> (".$list_empr[$i]["nb_item"].")</span></a>".$pannel_cart_see."</div>";
		}
		$is_cart++;
	}
	if (!$is_cart){
		print "<h3>$msg[398]</h3>";
	}
}

/*
					'idcaddie' => $temp->idcaddie,
					'name' => $temp->name,
					'type' => $temp->type,
					'comment' => $temp->comment,
					'autorisations' => $temp->autorisations,
					'nb_item' => $nb_item,
					'nb_item_pointe' => $nb_item_pointe,
					'nb_item_base' => $nb_item_base,
					'nb_item_base_pointe' => $nb_item_base_pointe,
					'nb_item_blob' => $nb_item_blob,
					'nb_item_blob_pointe' => $nb_item_blob_pointe
				*/
