<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: panier.inc.php,v 1.6 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $action, $idcaddie_selected, $msg, $elt_flag_inconnu, $elt_no_flag_inconnu, $cle, $object;

if($idcaddie) {
	$myCart = new caddie($idcaddie);
	switch ($action) {
		case 'choix_quoi':
			if ($idcaddie_selected) {
				$myCart_selected = new caddie($idcaddie_selected);
				print pmb_bidi($myCart_selected->aff_cart_titre());
				print $myCart_selected->aff_cart_nb_items() ;
				print $myCart->get_choix_quoi_form("./catalog.php?categ=caddie&sub=pointage&moyen=panier&action=pointe_item&idcaddie=$idcaddie&idcaddie_selected=$idcaddie_selected",
						"./catalog.php?categ=caddie&sub=pointage&moyen=panier&action=&object_type=NOTI&idcaddie=$idcaddie&item=0",
						$msg["caddie_choix_pointe_panier"],
						$msg["caddie_item_pointer"],
						"",false);
			}
			print pmb_bidi($myCart->aff_cart_titre());
			print $myCart->aff_cart_nb_items();
		break;
		case 'pointe_item':
			print pmb_bidi($myCart->aff_cart_titre());
			print $myCart->aff_cart_nb_items();
			if ($idcaddie_selected) {
				$myCart_selected = new caddie($idcaddie_selected);
				$liste_0=$liste_1= array();
				if ($elt_flag) {
					$liste_0 = $myCart_selected->get_cart("FLAG", $elt_flag_inconnu) ;
				}	
				if ($elt_no_flag) {
					$liste_1= $myCart_selected->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
				}	
				$liste= array_merge($liste_0,$liste_1);
				if($liste) {
				    foreach ($liste as $cle => $object) {
						$myCart->pointe_item($object,$myCart_selected->type);	
					}
				}	
			}
			print "<h3>".$msg["caddie_menu_pointage_apres_pointage"]."</h3>";
			print pmb_bidi($myCart->aff_cart_nb_items()) ;
			aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=panier", "choix_quoi", $msg["caddie_select_pointe_panier"], "", 0, 0, 0, true,1);
			break;
		default:
			print pmb_bidi($myCart->aff_cart_titre());
			print pmb_bidi($myCart->aff_cart_nb_items());
			aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=panier", "choix_quoi", $msg["caddie_select_pointe_panier"], "", 0, 0, 0, true, 1);
			break;
	}
} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=panier", "", $msg["caddie_select_pointe"], "", 0, 0, 0);