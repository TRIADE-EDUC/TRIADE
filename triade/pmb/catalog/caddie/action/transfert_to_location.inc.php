<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transfert_to_location.inc.php,v 1.5 2022/02/07 17:04:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $action, $msg, $charset, $transferts_nb_jours_pret_defaut, $elt_flag, $elt_no_flag, $dest_id, $date_retour, $motif, $ask_date;
global $PMBuserid, $deflt_docs_location;

require_once("./classes/transfert.class.php");

if ($idcaddie) {
	$myCart = new caddie($idcaddie);
	print pmb_bidi($myCart->aff_cart_titre());
	switch ($action) {
		case 'choix_quoi':
			print pmb_bidi($myCart->aff_cart_nb_items());	

			$form = $myCart->get_choix_quoi_form("./catalog.php?categ=caddie&sub=action&quelle=transfert_to_location&action=transfert_to_location_suite&object_type=EXPL&idcaddie=".$idcaddie, "./catalog.php?categ=caddie&sub=action&quelle=transfert_to_location&idcaddie=0", $msg["caddie_menu_action_transfert_to_location"], $msg["caddie_menu_action_transfert_to_location"], "");
			
			$destination_form = "
			<div class='row'>&nbsp;</div>	
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_popup_motif"]."</label>
			</div>
			<div class='row'>
				<textarea name='motif' cols=40 rows=5></textarea>
			</div>
			<div class='row'>
				<label class='etiquette'>".$msg['caddie_menu_action_transfert_to_location_select']."</label>
			</div>
			<div class='row'>
				".gen_liste ("select distinct idlocation, location_libelle from docs_location order by location_libelle", "idlocation", "location_libelle", 'dest_id', "", $deflt_docs_location, "", "","","",0)."
			</div>	
			<div class='row'>
				<label class='etiquette'>".htmlentities($msg['transferts_popup_ask_date'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
                " . get_input_date('ask_date', 'ask_date', '', true) . "
			</div>			
			<div class='row'>
				<label class='etiquette'>".$msg["transferts_popup_date_retour"]."</label>			
			</div>
			<div class='row'>
                " . get_input_date('date_retour', 'date_retour', '', true) . "
			</div>";
			
			$form = str_replace('<!--suppr_link-->', $destination_form, $form);
			$date_pret = mktime(0, 0, 0, date("m"), date("d") + $transferts_nb_jours_pret_defaut, date("Y"));
			$form = str_replace("!!date_retour_mysql!!", date("Y-m-d", $date_pret), $form);
			print $form;
			break;
		case 'transfert_to_location_suite':

			print pmb_bidi($myCart->aff_cart_nb_items());
				
			$idcaddie = caddie::check_rights($idcaddie);
			if ($idcaddie) {
				$liste = array();
				if($elt_flag && $elt_no_flag)
					$liste = $myCart->get_cart("ALL");
				if($elt_flag && !$elt_no_flag)
					$liste = $myCart->get_cart("FLAG");
				if($elt_no_flag && !$elt_flag)
					$liste = $myCart->get_cart("NOFLAG");
				$liste = array_unique($liste);

				$trans = new transfert();
				$trans->validation_send_event = 1;
				// pour chaque exemplaire on genere les transferts
				$transferts_expl_is_here = array();
				$transferts_ok = array();
				$transferts_in_progress = array();
				$transferts_expl_in_loan = array();
				$transferts_nok = array();
				foreach ($liste as $id_expl) {	
					// L'exemplaire est déjà ici ? 
					$query = "SELECT expl_location FROM exemplaires WHERE expl_id=".$id_expl;
					$res = pmb_mysql_query( $query );
					$src_id = pmb_mysql_result($res, 0);
					if($src_id == $dest_id) {
						$transferts_expl_is_here[] = $id_expl;
						continue;						
					}		
					// L'exemplaire est en cours de transfert ? 			
					$query = "select id_transfert from transferts ,transferts_demande where num_transfert=id_transfert and num_expl=$id_expl and etat_transfert=0";
					$res = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($res)) {
						$transferts_in_progress[] = $id_expl;
						continue;
					}
					// L'exemplaire est en cours de prêt ?
					$query = "select pret_idexpl  from pret where pret_idexpl='".$id_expl."' ";
					$res = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($res)) {
						$transferts_expl_in_loan[] = $id_expl;
						continue;
					}
					
					$num = $trans->creer_transfert_catalogue($id_expl, $dest_id, $date_retour, stripslashes($motif), $ask_date);
					if ($num) {	
						// Le transfert est généré !	
						$query = 'update transferts set transfert_ask_user_num= "'.$PMBuserid.'" where id_transfert="'.$num.'" ';
						pmb_mysql_query($query);
						$transferts_ok[] = $id_expl;
					} else {
						// Erreur, Le transfert ne peut etre généré	
						$transferts_nok[] = $id_expl;
					}
				}
				print "	
					<hr>								
					<div class='row'>
						<div class='colonne3 align_left'>".$msg['caddie_menu_action_transfert_to_location_ok']."</div>
						<div class='colonne3'><b>".count($transferts_ok)."</b></div>
					</div>";	
				// <div class='colonne_suite'><b>".(count($transferts_ok)?"<a href='./circ.php?categ=trans&sub=departs'>".$msg['caddie_menu_action_transfert_to_location_see']."</a>":"")."</b></div>

				if (count($transferts_expl_is_here)) {
					print "
					<div class='row'>
						<div class='colonne3 align_left'>".$msg['caddie_menu_action_transfert_to_location_is_here']."</div>
						<div class='colonne3'><b>".count($transferts_expl_is_here)."</b></div>
					</div>";
				}				
				if (count($transferts_in_progress)) {											
					print "
					<div class='row'>
						<div class='colonne3 align_left'>".$msg['caddie_menu_action_transfert_to_location_in_progress']."</div>
						<div class='colonne3'><b>".count($transferts_in_progress)."</b></div>
					</div>";
				}
				if (count($transferts_expl_in_loan)) {
					print "
					<div class='row'>
						<div class='colonne3 align_left erreur'>".$msg['caddie_menu_action_transfert_to_location_in_loan']."</div>
						<div class='colonne3'><b>".count($transferts_expl_in_loan)."</b></div>
					</div>";
				}
				if (count($transferts_nok)) {						
					print "	
					<div class='row'>								
						<div class='colonne3 align_left erreur'>".$msg['caddie_menu_action_transfert_to_location_nok']."</div>
						<div class='colonne3'><b>".count($transferts_nok)."</b></div>		
					</div>";
				}
			}
			break;
		default:
			break;
	}
} else {	
	aff_paniers($idcaddie, "EXPL", "./catalog.php?categ=caddie&sub=action&quelle=transfert_to_location", "choix_quoi", $msg["caddie_menu_action_transfert_to_location"], "EXPL", 0, 0, 0);
}
