<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circ.tpl.php,v 1.55 2019-05-27 13:27:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// $circ_menu : menu page circulation

global $categ, $circ_menu, $pmb_pret_groupement, $empr_show_caddie, $msg, $pmb_rfid_activate, $pmb_rfid_serveur_url, $pmb_resa_planning, $pmb_gestion_financiere, $pmb_gestion_amende, $acquisition_active, $pmb_transferts_actif, $transferts_regroupement_depart, $transferts_validation_actif, $pmb_scan_request_activate, $empr_menu_panier_gestion, $empr_menu_panier_collecte, $empr_menu_panier_pointage, $empr_menu_panier_action, $circ_layout, $circ_layout_end;

if ((SESSrights & RESTRICTCIRC_AUTH) && ($categ!="pret") && ($categ!="pretrestrict") ) {
	$circ_menu = '';
} else {
	$menu_groupexpl="";
	if($pmb_pret_groupement)$menu_groupexpl="<li id='circ_menu_voir_document'><a href='./circ.php?categ=groupexpl'>".$msg['groupexpl_submenu_list_title']."</a></li>";
	$circ_menu = "
	<div id='menu'>
	<h3 onclick='menuHide(this,event)'>$msg[5]</h3>
	<ul>
		<li id='circ_menu_msg13'><a href='./circ.php?categ='>$msg[13]</a></li>
		<li id='circ_menu_msg14'><a href='./circ.php?categ=retour'>$msg[14]</a></li>
		<li id='circ_menu_msgcirc_doc_a_traiter'><a href='./circ.php?categ=ret_todo'>".$msg["circ_doc_a_traiter"]."</a></li>
		<li id='circ_menu_msg903'><a href='./circ.php?categ=groups'>$msg[903]</a></li>
		<li id='circ_menu_msg15'><a href='./circ.php?categ=empr_create'>$msg[15]</a></li>
		<li id='circ_menu_msg17'><a href='./circ.php?categ=serialcirc'>".$msg["serialcirc_circ_menu"]."</a></li>
		$menu_groupexpl
		<li id='circ_menu_search_perso'><a href='./circ.php?categ=search_perso'>".$msg["search_perso_menu"]."</a></li>
	</ul>";
	if ($empr_show_caddie) {
		$circ_menu.="<h3 onclick='menuHide(this,event)'>".$msg['empr_caddie_menu']."</h3>
			<ul>
				<li><a href='./circ.php?categ=caddie&sub=gestion&quoi=panier'>".$msg['empr_caddie_menu_gestion']."</a></li>
				<li><a href='./circ.php?categ=caddie&sub=gestion&quoi=barcode'>".$msg['empr_caddie_menu_collecte']."</a></li>
				<li><a href='./circ.php?categ=caddie&sub=gestion&quoi=pointagebarcode'>".$msg['empr_caddie_menu_pointage']."</a></li>
				<li><a href='./circ.php?categ=caddie&sub=action'>".$msg['empr_caddie_menu_action']."</a></li>
			</ul>
			";
		}
	$circ_menu.="
	<h3 onclick='menuHide(this,event)'>".$msg['show']."</h3>
	<ul>
		<li id='circ_menu_voir_exemplaire'><a href='./circ.php?categ=visu_ex'>".$msg["voir_exemplaire"]."</a></li>
		<li id='circ_menu_voir_document'><a href='./circ.php?categ=visu_rech'>".$msg['voir_document']."</a></li>
	</ul>";

	if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {
		$circ_menu.="
		<h3 onclick='menuHide(this,event)'>".$msg["circ_menu_rfid"]."</h3>
		<ul>
			<li id='circ_menu_rfid_programmer'><a href='./circ.php?categ=rfid_prog'>".$msg["circ_menu_rfid_programmer"]."</a></li>
			<li id='circ_menu_rfid_effacer'><a href='./circ.php?categ=rfid_del'>".$msg["circ_menu_rfid_effacer"]."</a></li>
			<li id='circ_menu_rfid_lire'><a href='./circ.php?categ=rfid_read'>".$msg["circ_menu_rfid_lire"]."</a></li>
		</ul>";
	}
	//		<li id='circ_menu_rfid_portique'><a href='./circ.php?categ=rfid_gates'>".$msg["circ_menu_rfid_portique"]."</a></li>
	//		<li id='circ_menu_rfid_lecteur'><a href='./circ.php?categ=rfid_readers'>".$msg["circ_menu_rfid_lecteur"]."</a></li>

	$circ_menu.="
	<h3 onclick='menuHide(this,event)'>$msg[resa_menu]</h3>
	<ul>
		<li id='circ_menu_resa_menu_liste_encours'><a href='./circ.php?categ=listeresa&sub=encours'>".$msg["resa_menu_liste_encours"]."</a></li>
		<li id='circ_menu_resa_menu_lsite_depassee'><a href='./circ.php?categ=listeresa&sub=depassee'>".$msg["resa_menu_liste_depassee"]."</a></li>
		<li id='circ_menu_resa_menu_liste_docranger'><a href='./circ.php?categ=listeresa&sub=docranger'>".$msg["resa_menu_liste_docranger"]."</a></li>";
	if ($pmb_resa_planning) {
		$circ_menu.= "<li id='circ_menu_resa_menu_planning'><a href='./circ.php?categ=resa_planning'>".$msg['resa_menu_planning']."</a></li>";
	}
	$circ_menu.= "
	</ul>";
	if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
		$circ_menu.="<h3 onclick='menuHide(this,event)'>$msg[relance_menu]</h3>
			<ul>
			<li id='circ_menu_relance_to_do'><a href='./circ.php?categ=relance&sub=todo'>".$msg["relance_to_do"]."</a></li>
			<li id='circ_menu_relance_recouvrement'><a href='./circ.php?categ=relance&sub=recouvr'>".$msg["relance_recouvrement"]."</a></li>
			</ul>";
		}
	if ($acquisition_active) {
		$circ_menu.= "<h3 onclick='menuHide(this,event)'>$msg[acquisition_menu_sug]</h3>
		<ul>
			<li><a href='./circ.php?categ=sug&action=modif&id_bibli=0'>$msg[acquisition_sug_do]</a></li>
		</ul>";
		}
	if ($pmb_transferts_actif && (SESSrights & TRANSFERTS_AUTH)) {
		$circ_menu .= "<h3 onclick='menuHide(this,event)'>$msg[transferts_circ_menu_titre]</h3>
				<ul>";
		if($transferts_regroupement_depart){
			$circ_menu .= "
					<li><a href='./circ.php?categ=trans&sub=recep'>".$msg['transferts_circ_menu_reception']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=departs'>".$msg['transferts_circ_menu_departs']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=refus'>".$msg['transferts_circ_menu_refuse']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=reset'>".$msg['transferts_circ_menu_reset']."</a></li>
				</ul>";
		}else{
			if ($transferts_validation_actif=="1")
				$circ_menu .= "
						<li><a href='./circ.php?categ=trans&sub=valid'>".$msg['transferts_circ_menu_validation']."</a></li>
						";

			$circ_menu .= "
				<li><a href='./circ.php?categ=trans&sub=envoi'>".$msg['transferts_circ_menu_envoi']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=recep'>".$msg['transferts_circ_menu_reception']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=retour'>".$msg['transferts_circ_menu_retour']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=refus'>".$msg['transferts_circ_menu_refuse']."</a></li>
					<li><a href='./circ.php?categ=trans&sub=reset'>".$msg['transferts_circ_menu_reset']."</a></li>
				</ul>";
		}
	}
	if($pmb_scan_request_activate){
		$circ_menu.='<h3 onclick="menuHide(this,event)">'.$msg['admin_menu_scan_request'].'</h3>
				 <ul>
				 	<li><a href="./circ.php?categ=scan_request&sub=request&action=edit">'.$msg['circ_scan_request_create_label'].'</a></li>
				 	<li><a href="./circ.php?categ=scan_request&sub=list&action=clean_filters">'.$msg['circ_scan_request_see_label'].'</a></li>
				 </ul>';
	}
	
	$plugins = plugins::get_instance();
	$circ_menu.= $plugins->get_menu('circ')."
	<div id='div_alert' class='erreur'></div>
	</div>";
}

// $empr_menu_panier_gestion : menu gestion des paniers
$empr_menu_panier_gestion = "
<h1>$msg[empr_caddie_menu] <span>> $msg[empr_caddie_menu_gestion] > !!sous_menu_choisi!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=panier").">
		<a title='$msg[empr_caddie_menu_gestion_panier]' href='./circ.php?categ=caddie&sub=gestion&quoi=panier'>$msg[empr_caddie_menu_gestion_panier]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=procs").">
		<a title='$msg[empr_caddie_menu_gestion_procs]' href='./circ.php?categ=caddie&sub=gestion&quoi=procs'>$msg[empr_caddie_menu_gestion_procs]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=remote_procs").">
		<a title='$msg[remote_procedures_circ_title]' href='./circ.php?categ=caddie&sub=gestion&quoi=remote_procs'>$msg[remote_procedures_circ_title]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=classementGen").">
		<a title='$msg[classementGen_list_libelle]' href='./circ.php?categ=caddie&sub=gestion&quoi=classementGen'>$msg[classementGen_list_libelle]</a>
	</span>
</div>";

// $empr_menu_panier_collecte : menu collecte
$empr_menu_panier_collecte = "
<h1>$msg[empr_caddie_menu] <span>> $msg[empr_caddie_menu_collecte] > !!sous_menu_choisi!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=barcode").">
		<a title='$msg[empr_caddie_menu_collecte_barcode]' href='./circ.php?categ=caddie&sub=gestion&quoi=barcode'>$msg[empr_caddie_menu_collecte_barcode]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=selection").">
		<a title='$msg[empr_caddie_menu_collecte_selection]' href='./circ.php?categ=caddie&sub=gestion&quoi=selection'>$msg[empr_caddie_menu_collecte_selection]</a>
	</span>
</div>";

// $empr_menu_panier_pointage : menu pointage des paniers
$empr_menu_panier_pointage = "
<h1>$msg[empr_caddie_menu] <span>> $msg[empr_caddie_menu_pointage] > !!sous_menu_choisi!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=pointagebarcode").">
		<a title='$msg[empr_caddie_menu_pointage_barcode]' href='./circ.php?categ=caddie&sub=gestion&quoi=pointagebarcode'>$msg[empr_caddie_menu_pointage_barcode]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=pointage").">
		<a title='$msg[empr_caddie_menu_pointage_selection]' href='./circ.php?categ=caddie&sub=gestion&quoi=pointage'>$msg[empr_caddie_menu_pointage_selection]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=pointagepanier").">
		<a title='$msg[empr_caddie_menu_pointage_panier]' href='./circ.php?categ=caddie&sub=gestion&quoi=pointagepanier'>$msg[empr_caddie_menu_pointage_panier]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=gestion&quoi=razpointage").">
		<a title='$msg[empr_caddie_menu_pointage_raz]' href='./circ.php?categ=caddie&sub=gestion&quoi=razpointage'>$msg[empr_caddie_menu_pointage_raz]</a>
	</span>
</div>";

// $empr_menu_panier_action : menu action des contenus de paniers
$empr_menu_panier_action = "
<h1>$msg[empr_caddie_menu] <span>> $msg[empr_caddie_menu_action] > !!sous_menu_choisi!!</span></h1>
<div class='hmenu'>
	<span".ongletSelect("categ=caddie&sub=action&quelle=supprpanier").">
		<a title='$msg[empr_caddie_menu_action_suppr_panier]' href='./circ.php?categ=caddie&sub=action&quelle=supprpanier'>$msg[empr_caddie_menu_action_suppr_panier]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=transfert").">
		<a title='$msg[empr_caddie_menu_action_transfert]' href='./circ.php?categ=caddie&sub=action&quelle=transfert'>$msg[empr_caddie_menu_action_transfert]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=edition").">
		<a title='$msg[empr_caddie_menu_action_edition]' href='./circ.php?categ=caddie&sub=action&quelle=edition'>$msg[empr_caddie_menu_action_edition]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=mailing").">
		<a title='$msg[empr_caddie_menu_action_mailing]' href='./circ.php?categ=caddie&sub=action&quelle=mailing'>$msg[empr_caddie_menu_action_mailing]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=selection").">
		<a title=\"".$msg['empr_caddie_menu_action_selection']."\" href='./circ.php?categ=caddie&sub=action&quelle=selection'>$msg[empr_caddie_menu_action_selection]</a>
	</span>
	<span".ongletSelect("categ=caddie&sub=action&quelle=supprbase").">
		<a title='$msg[empr_caddie_menu_action_suppr_base]' href='./circ.php?categ=caddie&sub=action&quelle=supprbase'>$msg[empr_caddie_menu_action_suppr_base]</a>
	</span>
</div>";

//	----------------------------------
// $circ_layout : layout page circulation

$circ_layout = "
<div id='conteneur' class='circ'>
$circ_menu
	<div id='contenu'>
";

//	----------------------------------
// $circ_layout_end : layout page circulation (fin)

$circ_layout_end = '
	</div>
</div>
';

