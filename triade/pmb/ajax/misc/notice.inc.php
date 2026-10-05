<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.inc.php,v 1.13 2023/07/26 15:07:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $id, $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid, $charset, $msg, $current, $show_expl, $show_explnum, $show_map;
global $pmb_book_pics_show, $pmb_book_pics_url;

require_once("$class_path/serial_display.class.php");
require_once("$class_path/mono_display.class.php");
require_once("$include_path/avis_notice.inc.php");

if ($id) {
	//droits d'acces utilisateur/notice (lecture)
	$acces_l=1;
	if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
		require_once("$class_path/acces.class.php");
		$ac= new acces();
		$dom_1= $ac->setDomain(1);
		$acces_l = $dom_1->getRights($PMBuserid,$id,4);	//lecture
	}
	
	if ($acces_l==0) {
		error_message('', htmlentities($dom_1->getComment('view_noti_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		$display = '';
		$requete = "SELECT * FROM notices WHERE notice_id=$id LIMIT 1";
		$resultat = pmb_mysql_query($requete);
		if ($resultat) {
			if(pmb_mysql_num_rows($resultat)) {
				$notice = pmb_mysql_fetch_object($resultat);
				$cart_click_isbd = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=$id', 'cart')\"";
				$cart_click_isbd = "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click_isbd>" ;
				if ($current!==false) {
					$print_action = "&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&notice_id=".$id."&action_print=print_prepare','print'); w.focus(); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				}
				if ($notice->niveau_biblio == 'b') {
					// notice de bulletin
					$isbd = new mono_display($notice, 6, '', $show_expl, '', '', '', 0, 0, $show_explnum, 0, '', 0, false, true, 0, 0, $show_map);
				} elseif($notice->niveau_biblio != 's' && $notice->niveau_biblio != 'a') {
					// notice de monographie
					$isbd = new mono_display($notice, 6, '', $show_expl, '', '', '', 0, 0, $show_explnum, 0, '', 0, false, true, 0, 0, $show_map);
					
				} else {
					// notice de périodique
					$isbd = new serial_display($notice, 5, '', '', '', '', '', 0, 0, $show_explnum, 0, true, 0, 0, '', false, $show_map);
				}
				
				// header
				$display .= "
						<div class='row' style='padding-top: 8px;'>
							".$isbd->aff_statut.$cart_click_isbd.$print_action."<h1 style='display: inline;'>".$isbd->header."</h1>
							 </div>";
				
				// isbd + exemplaires existants
				$display .= "
						<div class='row'>
						$isbd->isbd
						</div>";
			}
		}
		ajax_http_send_response($display);
	}
}