<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.inc.php,v 1.60.4.2 2025/03/13 16:40:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $class_path, $include_path, $cart_click_bull, $cart_click_expl, $action, $item, $form_action, $form_cancel, $msg, $page, $lien;

if(!isset($idcaddie)) $idcaddie = 0;

// inclusions principales
require_once("$class_path/caddie.class.php");
require_once("$class_path/serials.class.php");
require_once("$class_path/emprunteur.class.php") ;
require_once("$class_path/sort.class.php");
require_once("$include_path/cart.inc.php");
require_once("$include_path/templates/cart.tpl.php");
require_once("$include_path/expl_info.inc.php");
require_once("$include_path/bull_info.inc.php");
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');

$cart_click_bull = "onClick=\"openPopUp('./print_cart.php?action=print_prepare&object_type=BULL&item=!!item!!', 'print_cart')\"";
$cart_click_expl = "onClick=\"openPopUp('./print_cart.php?action=print_prepare&object_type=EXPL&item=!!item!!', 'print_cart')\"";

switch ($action) {
	case 'new_cart':
		$myCart= new caddie();
		print $myCart->get_form("./catalog.php?categ=search&mode=3&item=".$item);
		break;
	case 'del_cart':
		$myCart= new caddie($idcaddie);
		$myCart->delete();
		aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=search&mode=3", "add_item", $msg['caddie_select_afficher'], "", 0, 1, 1);
		break;
	case 'del_item':
		$myCart= new caddie($idcaddie);
		if ($page=="" || $page==0) {
			$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["NOLINK"]=true;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$myCart->name;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=sprintf($msg["histo_cart"],$myCart->type);
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
			$_POST["page"]=1;
			$page=1;
		}
		if ($_SESSION["CURRENT"]!==false) {
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["URI"]="catalog.php?categ=search&mode=3&action=add_item&object_type=NOTI&idcaddie=".$idcaddie."&item=";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["PAGE"]=$page;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$msg["histo_cart_alone"]." : ".$myCart->name;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["SEARCH_TYPE"]="cart";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["NOPRINT"]=true;
		}
		$myCart->del_item($item);
		print "<div class=\"row\"><b>Panier&nbsp;: ".$myCart->name.' ('.$myCart->type.')</b></div>';
		//aff_cart_notices($myCart->get_cart(), $myCart->type, $idcaddie);
		$myCart->aff_cart_objects("./catalog.php?categ=search&mode=3&idcaddie=$idcaddie", false, true, false);
		break;
	case 'valid_new_cart':
		$myCart = new caddie(0);
		$myCart->set_properties_from_form();
		$myCart->create_cart();
		aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=search&mode=3", "add_item", $msg['caddie_select_afficher'], "", 0, 1, 1);
		break;
	default:
		if($idcaddie) {
			//Historique
			$myCart = new caddie($idcaddie);
			if ($page=="") {
			    if (!isset($_SESSION["session_history"]) || !is_array($_SESSION["session_history"])) {
			        $_SESSION["session_history"] = [];
			    }
				$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["NOLINK"]=true;
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$myCart->name;
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=sprintf($msg["histo_cart"],$myCart->type);
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
				$_POST["page"]=1;
				$page=1;
			}
			if ($_SESSION["CURRENT"]!==false) {
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["URI"]="catalog.php?categ=search&mode=3&action=add_item&object_type=NOTI&idcaddie=".$idcaddie."&item=";
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["GET"]=$_GET;
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["POST"]=$_POST;
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["PAGE"]=$page;
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$msg["histo_cart_alone"]." : ".$myCart->name;
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["SEARCH_TYPE"]="cart";
				$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["NOPRINT"]=true;
			}
			session_write_close();//On libère la session car il n'y a pas d'écriture ensuite et cela évite les verrous.
			$lien = "./catalog.php?categ=caddie&sub=gestion&quoi=panier&action=&object_type=".$myCart->type."&idcaddie=".$myCart->idcaddie."&item=0";
			print pmb_bidi("<div class=\"row\"><b>".$msg['caddie_intro']." <a href='".$lien."'>".$myCart->name.'</a> ('.$myCart->type.')</b></div>');
			//aff_cart_notices($myCart->get_cart(), $myCart->type, $idcaddie);
			$myCart->aff_cart_objects("./catalog.php?categ=search&mode=3&idcaddie=$idcaddie", false, true, false);
		} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=search&mode=3", "add_item", $msg["caddie_select_afficher"], "", 0, 1, 1, false, 1);
}

// affichage du contenu du caddie à partir de $liste qui contient les object_id
function aff_cart_notices($liste, $caddie_type, $idcaddie=0) {
	global $msg, $charset;
	global $begin_result_liste;
	global $end_result_liste;
	global $page, $nbr_lignes, $nb_per_page;
	global $form_cb_expl;

	//Calcul des variables pour la suppression d'items
	if($nb_per_page){
		$modulo = $nbr_lignes%$nb_per_page;
		if($modulo == 1){
			$page_suppr = (!$page ? 1 : $page-1);
		} else {
			$page_suppr = $page;
		}
		$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);
	}

	if (empty($liste) || !is_array($liste)) {
		print $msg[399];
		return;
	} else {
		// en fonction du type de caddie on affiche ce qu'il faut
		if ($caddie_type=="NOTI") {
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print $begin_result_liste;

			$elements_records_list_ui = new elements_records_list_ui($liste, count($liste), false);
			$lien_suppr_cart = "<a href='./catalog.php?categ=search&mode=3&action=del_item&object_type=NOTI&idcaddie=$idcaddie&item=!!notice_id!!&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title='".$msg['caddie_icone_suppr_elt']."' /></a>";
			elements_records_list_ui::set_link_delete_cart($lien_suppr_cart);
			$elements_records_list_ui->set_draggable(0);
			$elements_records_list_ui->set_ajax_mode(0);
			$elements_records_list_ui->set_button_explnum(1);
			print $elements_records_list_ui->get_elements_list();

			print $end_result_liste;
		} // fin si NOTI
		// si EXPL
		if ($caddie_type=="EXPL") {
			// boucle de parcours des exemplaires trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print $begin_result_liste;
			foreach ($liste as $expl) {
				if($stuff = get_expl_info($expl)) {
					$stuff->lien_suppr_cart = "<a href='./catalog.php?categ=search&mode=3&action=del_item&object_type=EXPL&idcaddie=$idcaddie&item=$expl&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title='".$msg['caddie_icone_suppr_elt']."' /></a>";
					$stuff = check_pret($stuff);
					print pmb_bidi(print_info($stuff,0,1));
				} else {
						print "<strong>$form_cb_expl&nbsp;: {$msg[395]}</strong>";
				}
			} // fin de liste
			print $end_result_liste;
		} // fin si EXPL
		if ($caddie_type=="BULL") {
			// boucle de parcours des bulletins trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print $begin_result_liste;
			foreach ($liste as $expl) {
				if($bull_aff = show_bulletinage_info($expl)) {
					$javascript_template ="
						<div id=\"el!!id!!Parent\" class=\"notice-parent\">
                            ".get_expandBase_button('el!!id!!')."
    						<span class=\"notice-heada\">!!heada!!</span>
    						<br />
						</div>
						<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
        				   		!!CONTENU!!
 						</div>";
					$lien_suppr_cart = "<a href='./catalog.php?categ=search&mode=3&action=del_item&object_type=EXPL&idcaddie=$idcaddie&item=$expl&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title='".$msg['caddie_icone_suppr_elt']."' /></a>";
					$aff = str_replace('!!id!!', $expl, $javascript_template);
					$aff = str_replace('!!unique!!', md5(microtime()), $aff);
					$aff = str_replace('!!heada!!', $lien_suppr_cart.$bull_aff->header, $aff);
					$aff = str_replace('!!CONTENU!!', $bull_aff->display, $aff);
					print pmb_bidi($aff);
				} else {
					print "<strong>$form_cb_expl&nbsp;: {$msg[395]}</strong>";
				}
			} // fin de liste
			print $end_result_liste;
		} // fin si BULL
	}
}
