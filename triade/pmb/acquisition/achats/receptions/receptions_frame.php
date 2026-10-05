<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions_frame.php,v 1.24.4.1 2025/02/12 14:28:03 dgoron Exp $

// définition du minimum nécessaire

global $include_path, $class_path, $pmb_prefill_cote, $action, $msg, $charset;

$base_path="./../../..";
$base_auth = "ACQUISITION_AUTH";
$base_use_dojo=1;
$base_title = "\$msg[acquisition_menu_title]";
require_once ("$base_path/includes/init.inc.php");

require_once("$include_path/templates/receptions_frame.tpl.php");
require_once("$class_path/entites.class.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/liens_actes.class.php");
require_once("$class_path/lignes_actes_statuts.class.php");
require_once("$class_path/suggestions.class.php");
require_once("$class_path/suggestions_map.class.php");
require_once("$class_path/mono_display.class.php");
require_once("$class_path/serials.class.php");
require_once("$base_path/catalog/serials/bulletinage/bul_func.inc.php");
require_once("$include_path/bull_info.inc.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/explnum.class.php");
require_once("$class_path/expl.class.php");
require_once("$class_path/resa.class.php");
if ($pmb_prefill_cote) {
	require_once("$base_path/catalog/expl/$pmb_prefill_cote");
} else {
	require_once("$base_path/catalog/expl/custom_no_cote.inc.php");
}

print "<div id='att' style='z-Index:1000'></div>";

function show_delivery_form($msg_client='') {
	global $msg, $charset;
	global $no, $id_lig, $id_prod, $typ_lig;
	global $recept_deliv_form, $recept_deliv_form_suite;
	global $recept_form_qte_liv, $recept_bt_update, $recept_bt_undo, $previous, $recept_bt_next;
	global $applicants_tpl;
	$form = $recept_deliv_form;
	$lg = new lignes_actes($id_lig);
	$act = new actes($lg->num_acte);
	$fou = new entites($act->num_fournisseur);

	$nb_rec = $lg->getNbDelivered($id_lig);
	$nb_sol = $lg->nb-$nb_rec;

	if($nb_sol > 0) {
		$form = str_replace('<!-- bt_update -->', $recept_bt_update, $form);
		$form = str_replace('<!-- qte_liv -->', $recept_form_qte_liv, $form);
	} else {
		$form = str_replace('<!-- qte_liv -->', '', $form);
	}

	if ($previous) {
		$tp = unserialize(rawurldecode(stripslashes($previous)));
		if (is_array($tp) && count($tp)) {
			$form = str_replace('<!-- bt_undo -->', $recept_bt_undo, $form);
			$form = str_replace('!!previous!!', stripslashes($previous), $form);
		} else {
			$previous=0;
		}
	}
	if (!$previous) {
		$form = str_replace('<!-- bt_undo -->', '', $form);
		$form = str_replace('!!previous!!', 0, $form);
	}
	$form = str_replace('<!-- bt_next -->', $recept_bt_next, $form);
	$lgstat_form=lgstat::getHtmlSelect(array(0=>0), FALSE, array('id'=>'sel_lgstat_!!id_lig!!','onchange'=>'recept_upd_lgstat(this.getAttribute("id"));' ));

	$form=str_replace('!!lib_acte!!',
						htmlentities($msg['acquisition_recept_fou'], ENT_QUOTES, $charset)."&nbsp;".htmlentities($fou->raison_sociale,ENT_QUOTES,$charset).'&nbsp;'
						.htmlentities((($act->type_acte)?($msg['acquisition_act_num_dev']):($msg['acquisition_act_num_cde'])),ENT_QUOTES,$charset).htmlentities($act->numero,ENT_QUOTES,$charset),
						$form);

	if($act->type_acte == 0 && ($typ_lig == 1 ||$typ_lig == 2) && count($lg->getApplicants())){ //Actes étant des commandes, on prend en compte les demandeurs
		$applicants = $lg->getApplicants(); //On a les demandeurs de la commande; on les affiche pour que l'utilisateur sache qui bénéfieciera d'une réservation sur cet ouvrage
		$applicants_label = emprunteur::getName($applicants);
		$applicants_labels = '';
		foreach($applicants as $applicant){
			$applicants_labels.= '<label>'.htmlentities($applicants_label[$applicant],ENT_QUOTES,$charset).'</label><br/>';
		}
		$applicants_tpl = str_replace('!!std_applicants!!', $applicants_labels, $applicants_tpl);
		$form = str_replace('!!applicants_tr!!',$applicants_tpl,$form);
	}else{
		$form = str_replace('!!applicants_tr!!','',$form);
	}
	$form = str_replace('!!code!!',htmlentities($lg->code,ENT_QUOTES,$charset),$form);
	$form = str_replace('!!lib!!',nl2br(htmlentities($lg->libelle,ENT_QUOTES,$charset)),$form);
	$form = str_replace('!!qte_cde!!',$lg->nb,$form);
	$form = str_replace('!!qte_rec!!',$nb_rec,$form);
	$form = str_replace('!!qte_sol!!',$nb_sol,$form);
	$lgstat_form=str_replace("value='".$lg->statut."'","value='".$lg->statut."' selected='selected' ",$lgstat_form);
	$form = str_replace('!!lgstat!!',$lgstat_form,$form);
	$form = str_replace('!!comment_lg!!',nl2br(htmlentities($lg->commentaires_gestion,ENT_QUOTES,$charset)),$form);
	$form = str_replace('!!comment_lo!!',nl2br(htmlentities($lg->commentaires_opac,ENT_QUOTES,$charset)),$form);
	$form = str_replace('!!id_lig!!',$id_lig,$form);
	$form = str_replace('!!id_prod!!',$id_prod,$form);
	$form = str_replace('!!typ_lig!!',$typ_lig,$form);
	$form = str_replace('!!no!!',$no,$form);

	switch($typ_lig) {
		case '1': //notice
			$form.=do_notice_form($id_prod);
			$form.=do_explnum_form();
			$form.=do_expl_form();
			if ($lg->num_acquisition) $form.=do_sugg_form($lg->num_acquisition);
			break;
		case '2': //bulletin
			$form.=do_bull_form($id_prod);
			$form.=do_explnum_form();
			$form.=do_expl_form();
			//if ($lg->num_acquisition) $form.=do_sugg_form($lg->num_acquisition);
			break;
		case '3': //frais
			break;
		case '4': //abt
			break;
		case '5' : //article
			$form.=do_art_form($id_prod);
			$form.=do_explnum_form();
			if ($lg->num_acquisition) $form.=do_sugg_form($lg->num_acquisition);
			break;
		default : //non catalogué
			if ($lg->num_acquisition) $form.=do_sugg_form($lg->num_acquisition);
			break;
	}

	$form = str_replace('!!msg_client!!', $msg_client,$form);
	print $form.$recept_deliv_form_suite;
}

function do_notice_form($notice_id=0) {
	global $recept_deliv_form_notice;
	global $prefix_url_image;

	$prefix_url_image='./../../../';
	$form = $recept_deliv_form_notice;
	$md = new mono_display(	$notice_id, 	// $id = id de la notice à afficher
							6, 				// $level :
											//	0 : juste le header (titre  / auteur principal avec le lien si applicable)
											//	1 : ISBD seul, pas de note, bouton modif, expl, explnum et résas
											// 	6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
							'',				// $action = URL associée au header
							1, 				// $expl -> affiche ou non les exemplaires associés
							'',				// $expl_link
							'', 			// $lien_suppr_cart
							'', 			// $explnum_link
							0, 				// $show_resa
							2,				// $print
							1,				// $show_explnum
							1,				// $show_statut
							'',				// $anti_loop
							0,				// $draggable
							0,				// $no_link
							1,				// $show_opac_hidden_fields
							0				// $ajax_mode
							);

	$form = str_replace('!!notice!!',$md->result,$form);
	return $form;
}

function do_art_form($art_id) {
	global $recept_deliv_form_notice;
	global $prefix_url_image;

	$prefix_url_image='./../../../';
	$form = $recept_deliv_form_notice;
	$md = new serial_display (	$art_id,					// $id = id de la notice à afficher
								6,		 					// $level :
															// 0 : juste le header (titre  / auteur principal avec le lien si applicable)
															// 6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
								'',				 			// $action_serial = URL à atteindre si la notice est une notice chapeau
								'', 						// $action_analysis = URL à atteindre si la notice est un dépouillement
															// note dans ces deux variables, '!!id!!' sera remplacé par l'id de cette notice
															// les deux liens s'excluent mutuellement, bien sur.
								'', 						// $action_bulletin
								'', 						// $lien_suppr_cart = lien de suppression de la notice d'un caddie
								'', 						// $lien_explnum
								0,							// $bouton_explnum
								2,							// $print
								1, 							// $show_explnum
								0, 							// $show_statut=
								1, 							// $show_opac_hidden_fields=
								0,							// $draggable
								0, 							// $ajax_mode
								'',							// $anti_loop
								0							// $no_link
								);

	$form = str_replace('!!notice!!',$md->result,$form);
	return $form;
}

function do_bull_form($bull_id) {
	global $recept_deliv_form_bull;
	global $prefix_url_image;

	$prefix_url_image='./../../../';
	$form = $recept_deliv_form_bull;
	$md = show_bulletinage_info_catalogage($bull_id, true);
	$form = str_replace('!!bulletin!!',$md,$form);
	return $form;
}

function do_expl_form() {
	global $recept_deliv_form_expl, $expl_form;
	global $typ_lig, $id_prod;
	global $option_num_auto, $pmb_numero_exemplaire_auto, $pmb_numero_exemplaire_auto_script, $recept_deliv_form_expl_auto;
	global $pmb_droits_explr_localises, $explr_visible_mod;

	$form = '';
	$num_auto = 0;
	if (!isset($first)) $first=1;

	// visibilité des exemplaires
	// On ne vérifie que si l'utilisateur peut créer sur au moins une localisation.
	if (!$pmb_droits_explr_localises || $explr_visible_mod) {

		$id_notice = 0;
		$id_bulletin = 0;

		switch($typ_lig) {
			case '1': //notice
				$id_notice = $id_prod;
				break;
			case '2': //bulletin
				$id_bulletin = $id_prod;
				break;
			default : //non catalogué
				break;
		}

		if ($id_notice) {

			$expl_form = $recept_deliv_form_expl;
			if (  ($pmb_numero_exemplaire_auto=='1' || $pmb_numero_exemplaire_auto=='2') && $pmb_numero_exemplaire_auto_script ) {
				$num_auto = 1;
			}

			if ($num_auto==1 && (isset($option_num_auto)) ) {
				$recept_deliv_form_expl_auto = str_replace('!!checked!!', "checked='checked'",$recept_deliv_form_expl_auto);
				$expl_form = str_replace('<!-- option_num_auto -->', $recept_deliv_form_expl_auto, $expl_form);
			} elseif ($num_auto==1 && !isset($option_num_auto)) {
				$recept_deliv_form_expl_auto = str_replace('!!checked!!', '',$recept_deliv_form_expl_auto);
				$expl_form = str_replace('<!-- option_num_auto -->', $recept_deliv_form_expl_auto, $expl_form);
			}

			$nex = new exemplaire('', 0, $id_notice);
			$expl_form = $nex->expl_form('','');
			$form = $expl_form;

		} elseif ($id_bulletin) {

			$expl_form = $recept_deliv_form_expl;
			if ( ($pmb_numero_exemplaire_auto=='1' || $pmb_numero_exemplaire_auto=='3') && $pmb_numero_exemplaire_auto_script ) {
				$num_auto = 1 ;
			}

			if ($num_auto==1 && (isset($option_num_auto)) ) {
				$recept_deliv_form_expl_auto = str_replace('!!checked!!', "checked='checked'",$recept_deliv_form_expl_auto);
				$expl_form = str_replace('<!-- option_num_auto -->', $recept_deliv_form_expl_auto, $expl_form);
			} elseif ($num_auto==1 && !isset($option_num_auto)) {
				$recept_deliv_form_expl_auto = str_replace('!!checked!!', '',$recept_deliv_form_expl_auto);
				$expl_form = str_replace('<!-- option_num_auto -->', $recept_deliv_form_expl_auto, $expl_form);
			}

			$nex = new exemplaire('', 0, 0, $id_bulletin);
			$expl_form = $nex->expl_form('','');
			$form = $expl_form;
		}
	}
	return $form;
}

function add_expl() {
	global $typ_lig, $id_prod;
	global $pmb_droits_explr_localises, $explr_visible_mod;
	global $f_ex_cb, $f_ex_nbparts;

	// visibilité des exemplaires
	// On ne vérifie que si l'utilisateur peut créer sur au moins une localisation.
	if ($pmb_droits_explr_localises && !$explr_visible_mod) return false;

	$id_notice = 0;
	$id_bulletin = 0;

	switch($typ_lig) {
		case '1': //notice
			$id_notice = $id_prod;
			break;
		case '2': //bulletin
			$id_bulletin = $id_prod;
			break;
		default : //non catalogué
			break;
	}
	if (!$id_bulletin && !$id_notice) return false;

	//Vérification des champs personalisés
	$p_perso=new parametres_perso("expl");
	$nberrors=$p_perso->check_submited_fields();
	if ($nberrors) return false;

	if ($id_notice) {
		$nex = new exemplaire($f_ex_cb, 0, $id_notice, 0);
	} else {
		$nex = new exemplaire($f_ex_cb, 0, 0, $id_bulletin);
	}
	if ($nex->expl_id) {
	    return false;
	} else {
		//On passe le nombre de pièces à 1 si la valeur n'est pas transmise
		if(!is_numeric($f_ex_nbparts) || !$f_ex_nbparts) $f_ex_nbparts=1;

		$nex->set_properties_from_form();
		if (!$nex->save()) {
		    return false;
		}
	}
	return true;
}

function do_explnum_form() {
	global $recept_deliv_form_explnum;
	global $deflt_explnum_statut;
	$recept_deliv_form_explnum = str_replace('!!statut_list!!', gen_liste_multiple ("select id_explnum_statut, gestion_libelle from explnum_statut order by 2", "id_explnum_statut", "gestion_libelle", "id_explnum_statut", "f_explnum_statut", "", $deflt_explnum_statut, "", "","","",0), $recept_deliv_form_explnum);
	return $recept_deliv_form_explnum;
}

function do_sugg_form($id_suggestion) {
	global $charset;
	global $recept_deliv_form_sugg, $deflt3receptsugstat;

	$sug = new suggestions($id_suggestion);
	$tab_orig = $sug->getOrigines();
	$form = '';

	//Récupération des noms des créateurs des suggestions
	$list_orig='';
	if (count($tab_orig)) {
		$form = $recept_deliv_form_sugg;

		foreach($tab_orig as $orig) {
			switch($orig['type_origine']){
				default:
				case '0' :
				 	$q_user = "SELECT userid, nom, prenom FROM users where userid = '".$orig['origine']."'";
					$r_user = pmb_mysql_query($q_user);
					$row_user=pmb_mysql_fetch_row($r_user);
					$list_orig = htmlentities($row_user[1], ENT_QUOTES, $charset);
					if ($row_user[2]) $list_orig.= ", ".htmlentities($row_user[2], ENT_QUOTES, $charset);
					$list_orig .= "<br />";
					break;
				case '1' :
				 	$q_empr = "SELECT id_empr, empr_nom, empr_prenom FROM empr where id_empr = '".$orig['origine']."'";
					$r_empr = pmb_mysql_query($q_empr);
					$row_empr=pmb_mysql_fetch_row($r_empr);
					$list_orig.= htmlentities($row_empr[1], ENT_QUOTES, $charset);
					if ($row_empr[2]) $list_orig.= ", ".htmlentities($row_empr[2], ENT_QUOTES, $charset);
					$list_orig .= "<br />";
					break;
				case '2' :
					break;
			}
		}
		$form = str_replace('<!-- origines -->', $list_orig, $form);
		$form = str_replace('!!id_sug!!',$id_suggestion,$form);

		$sug_map= new suggestions_map();
		if($sug->statut==$sug_map->getState_ID('ORDERED')) {
			$sel_sugstat = $sug_map->getHtmlStateSelect('ORDERED', array(0=>$sug_map->getStateNameFromId($deflt3receptsugstat)), TRUE, array('name'=>'sel_sugstat'));
		} else {
			$sel_sugstat = $sug_map->getHtmlStateSelect('ORDERED', array(0=>$sug_map->getStateNameFromId($sug->statut)), TRUE, array('name'=>'sel_sugstat'));
		}
		$form = str_replace('<!-- sel_sugstat -->', $sel_sugstat, $form);
	}
	return $form;
}

function upload_file() {
	global $f_fichier, $id_lig, $typ_lig, $id_prod, $no, $base_path;
	global $id_rep, $path, $up_place, $f_fichier, $f_url, $f_explnum_statut, $deflt_upload_repertoire, $pmb_indexation_docnum, $pmb_indexation_docnum_default,$ck_index ;

	switch($typ_lig) {
		case '1': //notice
		case '5' : //article
			$f_notice = $id_prod;
			$f_bulletin = 0;
			break;
		case '2': //bulletin
			$f_notice = 0;
			$f_bulletin = $id_prod;
			break;
		default : //non catalogué
			break;
	}
	if (($f_notice || $f_bulletin) && $f_fichier) {
		$up_place=0;
		$id_rep=0;
		$path = '';
		$ck_index=0;
		if ($deflt_upload_repertoire) {
			$id_rep = $deflt_upload_repertoire;
			if($id_rep) {
				$r = new upload_folder($id_rep);
				$path = $r->repertoire_nom;
				$up_place = 1;
			}
		}
		if ($pmb_indexation_docnum && $pmb_indexation_docnum_default) $ck_index=1;
		$explnum = new explnum();
		$retour = $base_path.'/acquisition/achats/receptions/receptions_frame.php?action=delivery&no='.$no.'&id_lig='.$id_lig.'&typ_lig='.$typ_lig.'&id_prod='.$id_prod;
		$explnum->mise_a_jour($f_notice, $f_bulletin, '', $f_url, $retour,0,0, $f_explnum_statut);
	}
	return;
}

function update() {
	global $id_lig, $qte_liv, $previous, $sel_lgstat;
	global $pmb_resa_records_no_expl, $acquisition_sugg_to_cde_resa_auto;

	if (!$id_lig || $qte_liv<=0) return false;

	$lig_cde = new lignes_actes($id_lig);
	$id_cde = $lig_cde->num_acte;
	$cde = new actes($id_cde);

	$id_liv=0;
	$t_liv = liens_actes::getDeliveries($cde->id_acte, today());

	if (count($t_liv) && $t_liv[0]) {
		$id_liv = $t_liv[0];
		$liv = new actes($id_liv);
	} else {
		$liv = new actes();
		$liv->date_acte = today();
		$liv->type_acte = TYP_ACT_LIV;
		$liv->statut = STA_ACT_REC;
		$liv->num_entite = $cde->num_entite;
		$liv->num_fournisseur = $cde->num_fournisseur;
		$liv->num_contact_livr = $cde->num_contact_livr;
		$liv->num_contact_fact = $cde->num_contact_fact;
		$liv->num_exercice = $cde->num_exercice;
		$liv->commentaires = '';
		$liv->reference = '';
		$liv->calc(); //numero
		$liv->save();

		$id_liv = $liv->id_acte;
		//création des liens entre actes
		$la = new liens_actes($id_cde, $id_liv);

	}

	//Création de la ligne de livraison
	$lig_liv = new lignes_actes();
	$lig_liv->type_ligne = $lig_cde->type_ligne;
	$lig_liv->num_acte = $id_liv;
	$lig_liv->lig_ref = $lig_cde->id_ligne;
	$lig_liv->num_acquisition = $lig_cde->num_acquisition;
	$lig_liv->num_rubrique = $lig_cde->num_rubrique;
	$lig_liv->num_produit = $lig_cde->num_produit;
	$lig_liv->num_type = $lig_cde->num_type;
	$lig_liv->libelle = addslashes($lig_cde->libelle);
	$lig_liv->code = addslashes($lig_cde->code);
	$lig_liv->prix = $lig_cde->prix;
	$lig_liv->tva = $lig_cde->tva;
	$lig_liv->nb = $qte_liv;
	$lig_liv->date_cre = today();
	$lig_liv->statut = $sel_lgstat;
	$lig_liv->remise = $lig_cde->remise;
	$lig_liv->debit_tva = $lig_cde->debit_tva;
	$lig_liv->save();

	if(count($lig_cde->getApplicants()) && ($lig_cde->type_ligne == 1 || $lig_cde->type_ligne == 2)){
		/**
		 * Traitement des demandeurs et de leurs résas
		 */
		if($pmb_resa_records_no_expl && $acquisition_sugg_to_cde_resa_auto) {
			$applicants = $lig_cde->getApplicants();
			foreach($applicants as $applicant){
				if($lig_cde->type_ligne == 1){ //Notice
					$resa = new reservation($applicant, $lig_cde->num_produit, 0);
				}else if($lig_cde->type_ligne == 2){ //bulletin
					$resa = new reservation($applicant, 0, $lig_cde->num_produit);
				}
				if(!$resa->resa_exists()){
					$resa->add(0,1);
				}
				//TODO: check réservation existante.
			}
		}
	}

/*
	//Mise à jour de la suggestion
	$sug_map = new suggestions_map();
	if ( $lig_cde->num_acquisition != 0 ) {
		$sug = array();
		$sug[] = $lig_cde->num_acquisition;
		$sug_map->doTransition('RECEIVED', $sug);
	}
*/
	//La commande est-elle soldée
	$tab_cde = actes::getLignes($id_cde);
	$solde = true;
	while (($row_cde = pmb_mysql_fetch_object($tab_cde))) {

		if ($row_cde->type_ligne != 3) {	// Frais, non livrables
			$tab_liv = lignes_actes::getLivraisons($row_cde->id_ligne);
			$nb_rec = 0;
			while (($row_liv = pmb_mysql_fetch_object($tab_liv))) {
				$nb_rec = $nb_rec + $row_liv->nb;
			}
			if ($row_cde->nb > $nb_rec) {
				$solde = false;
				break;
			}
		}
	}
	if ($solde) {
		$cde->statut = ($cde->statut & (~STA_ACT_ENC) | STA_ACT_REC); // Cde soldée >> Statut commande = en cours->soldé
	}
	$cde->update_statut();

	$tp = array($lig_liv->id_ligne);
	if ($previous) {
		$tp = unserialize(rawurldecode(stripslashes($previous)));
	}
	$previous = addslashes(rawurlencode(serialize($tp)));

	return true;
}

function update_sug() {
	global $id_sug, $sel_sugstat;

	//Mise à jour de la suggestion
	$sug = array();
	$sug[0] = $id_sug;
	$sug_map = new suggestions_map();
	$sug_map->doTransition($sug_map->getStateNameFromId($sel_sugstat), $sug, TRUE);
	return true;
}

function undo() {
	global $id_lig, $previous;

	if(!$id_lig) return false;
	if(!$previous) return false;

	$tp = unserialize(rawurldecode(stripslashes($previous))) ;
	if (!is_array($tp) || !count($tp)) {
		$previous = 0;
		return false;
	}
	$id_liv = array_pop($tp);
	if (count($tp)) {
		$previous = addslashes(rawurlencode(serialize($tp)));
	} else {
		$previous=0;
	}

	$lg_liv = new lignes_actes($id_liv);
	if (!$lg_liv->id_ligne) return false;

	$liv = new actes($lg_liv->num_acte);
	if (!$liv->id_acte) return false;
	$id_cde = liens_actes::getOrder($id_liv);

	$lg_liv->delete();
	$r = actes::getLignes($lg_liv->num_acte);
	if (pmb_mysql_num_rows($r)==0) {
		actes::delete($lg_liv->num_acte);
	}

	$cde = new actes($id_cde);
	$cde->statut = ($cde->statut & !STA_ACT_AVA & !STA_ACT_REC & !STA_ACT_ARC | STA_ACT_ENC) ;
	$cde->update_statut();

	return true;
}

//Traitement des actions
$error_msg = '';

switch($action) {
	case 'upload_file' :
		upload_file();
		break;
	case 'add_expl' :
		if (!add_expl()) {
			$msg_client = htmlentities($msg['acquisition_recept_add_expl_err'], ENT_QUOTES, $charset);
		} else {
			$msg_client = htmlentities($msg['acquisition_recept_add_expl_ok'], ENT_QUOTES, $charset);
		}
		show_delivery_form($msg_client);
		break;
	case 'update' :
		if (!update()) {
			$msg_client = htmlentities($msg['acquisition_recept_deliv_err'], ENT_QUOTES, $charset);
		} else {
			$msg_client = htmlentities($msg['acquisition_recept_deliv_ok'], ENT_QUOTES, $charset);
		}
		show_delivery_form($msg_client);
		break;
	case 'update_sug' :
	    if (!update_sug()) {
		    $msg_client = htmlentities($msg['acquisition_recept_update_sug_err'], ENT_QUOTES, $charset);
		} else {
		    $msg_client = htmlentities($msg['acquisition_recept_update_sug_ok'], ENT_QUOTES, $charset);
		}
		show_delivery_form($msg_client);
		break;
	case 'undo' :
		if (!undo()) {
			$msg_client = htmlentities($msg['acquisition_recept_undo_err'], ENT_QUOTES, $charset);
		} else {
			$msg_client = htmlentities($msg['acquisition_recept_undo_ok'], ENT_QUOTES, $charset);
		}
		show_delivery_form($msg_client);
		break;
	case 'show' :
	default:
		show_delivery_form();
		break;
}
?>