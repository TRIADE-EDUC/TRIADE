<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.inc.php,v 1.118.2.1.2.1 2025/04/30 07:18:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/notice.class.php");
require_once ($class_path."/elements_list/elements_records_caddie_list_ui.class.php");

function aff_paniers($item=0, $object_type="NOTI", $lien_origine="./cart.php?", $action_click = "add_item", $titre="Cliquez sur le nom d'un panier pour y déposer la notice", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1, $nocheck=false, $lien_pointage=0) {
	global $msg;
	global $action;

	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	print "<script type='text/javascript' src='./javascript/ajax.js'></script>";
	if(($item)&&($nocheck)) {
		print "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&item=$item' method='post'>";
		print "<input type='hidden' id='idcaddie' name='idcaddie' >";
		if ($lien_pointage) {
			print "<input type='hidden' id='idcaddie_selected' name='idcaddie_selected' >";
		}
	}
	if(($item)&&(!$nocheck)) {
		print "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&item=$item' method='post'>";
		if($action!="save_cart") {
		    if($object_type == 'NOTI') {
		        print "<input type='checkbox' name='include_child' >&nbsp;".$msg["cart_include_child"];
		    }
		    if($object_type == 'NOTI' && notice::get_niveau_biblio($item) == 's') {
		        print "<br /><input type='checkbox' name='include_bulletin_notice' >&nbsp;".$msg["cart_include_bulletin_notice"];
		    }
		    if(($object_type == 'NOTI' && notice::get_niveau_biblio($item) == 's') || ($object_type == 'BULL')) {
		        print "<br /><input type='checkbox' name='include_analysis' >&nbsp;".$msg["cart_include_analysis"];
		    }
		}
	}
	print "<hr />";
	$boutons_select='';
	if ($lien_creation) {
		print "<div class='row'>
		$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
		</div><br>";
	}

	list_caddies_ui::set_lien_creation($lien_creation);
	list_caddies_ui::set_lien_edition($lien_edition);
	$list_caddies_ui = new list_caddies_ui(array('type' => $restriction_panier));
	$list_caddies_ui->set_caddie_object_type($object_type);
	$list_caddies_ui->set_item($item);
	$list_caddies_ui->set_lien_origine($lien_origine);
	$list_caddies_ui->set_action_click($action_click);
	$list_caddies_ui->set_expandable_title($titre);
	$list_caddies_ui->set_nocheck($nocheck);
	$list_caddies_ui->set_lien_pointage($lien_pointage);
	print confirmation_delete("$lien_origine&action=del_cart&object_type=".$object_type."&item=$item&idcaddie=");
	print $list_caddies_ui->get_display_list();
	$script_submit = $list_caddies_ui->get_script_submit();
	if ($lien_creation) {
		print "<script type='text/javascript'>
            pmb_include('./javascript/classementGen.js');
        </script>";
	}

	if (!$nocheck) {
		if($item && $action!="save_cart") {
			$boutons_select="<input type='submit' value='".$msg["print_cart_add"]."' class='bouton'/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>&nbsp;";
		}
		if ($lien_creation) {
			print "<div class='row'><hr />
				$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
				</div>";
		} else {
			print "<div class='row'><hr />
				$boutons_select
				</div>";
		}
	} else 	if ($lien_creation) {
		print "<div class='row'><hr />
			$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
			</div>";
	}
	//if(($item)&&(!$nocheck)) print"</form>";
	if(($item)) print"</form>";
	print $script_submit;
}

// affichage d'un unique objet de caddie
function aff_cart_unique_object ($item, $caddie_type, $url_base="./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=0" ) {
	global $msg;
	global $begin_result_liste;
	global $end_result_list;
	global $page, $nbr_lignes, $nb_per_page, $nb_per_page_search;
	global $form_cb_expl;

	// nombre de références par pages
	if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
	else $nb_per_page = 10;

	$cb_display = "
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			";

	$liste=array();
	$liste[] = array('object_id' => $item, 'content' => "", 'blob_type' => "", 'flag' => "") ;

	$aff_retour = "" ;

	//Calcul des variables pour la suppression d'items
	$modulo = $nbr_lignes%$nb_per_page;
	if($modulo == 1){
		$page_suppr = (!$page ? 1 : $page-1);
	} else {
		$page_suppr = $page;
	}
	$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);

	if (!is_array($liste) || empty($liste)) {
		return $msg[399];
	} else {
		// en fonction du type de caddie on affiche ce qu'il faut
		if ($caddie_type=="NOTI") {
			$elements_records_caddie_list_ui = new elements_records_caddie_list_ui($liste, count($liste), false);
			$elements_records_caddie_list_ui->set_show_resa(0);
			$elements_records_caddie_list_ui->set_show_resa_planning(0);
			$elements_records_caddie_list_ui->set_draggable(0);
			elements_records_caddie_list_ui::set_url_base($url_base);
			$aff_retour .= $elements_records_caddie_list_ui->get_elements_list();

			$aff_retour .= $end_result_list;
		} // fin si NOTI
		// si EXPL
		if ($caddie_type=="EXPL") {
			// boucle de parcours des exemplaires trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
		    foreach ($liste as $expl) {
				if (!$expl['content'])
					if($stuff = get_expl_info($expl['object_id'])) {
						$stuff->lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=EXPL&item=".$expl['object_id']."&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a>";
						$stuff = check_pret($stuff);
						$aff_retour .= print_info($stuff,0,1);
					} else {
						$aff_retour .= "<div class='row'><strong>ID : ".$expl['object_id']."&nbsp;: {$msg[395]}</strong></div>";
					}
				else {
					$cb_display = "
						<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				    		<span class=\"notice-heada\"><strong>Code-barre : $expl[content]&nbsp;: {$msg[395]}</strong></span>
				    		<br />
						</div>
						";
					$aff_retour .= $cb_display;
				}
			} // fin de liste
			$aff_retour .= $end_result_list;
		} // fin si EXPL
		if ($caddie_type=="BULL") {
			// boucle de parcours des bulletins trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
		    foreach ($liste as $expl) {
				global $url_base_suppr_cart;
				$url_base_suppr_cart = $url_base ;
				if ($bull_aff = show_bulletinage_info($expl["object_id"],0,1)) {
					$aff_retour .= $bull_aff;
				} else {
					$aff_retour .= "<strong>$form_cb_expl&nbsp;: {$msg[395]}</strong><br />";
				}
			} // fin de liste
			$aff_retour .= $end_result_list;
		} // fin si BULL
	}
	return $aff_retour ;
}
