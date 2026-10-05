<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_func.inc.php,v 1.64.2.2 2024/12/02 15:20:24 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once("$class_path/quotas.class.php");
//require_once("$class_path/resa.class.php"); génère une fatal dans les inclusions

// permet de savoir si un CB expl est déjà affecté à une résa
function verif_cb_utilise ($cb) {
	$rqt = "select id_resa from resa where resa_cb='".addslashes($cb)."' ";
	$res = pmb_mysql_query($rqt) ;
	$nb=pmb_mysql_num_rows($res) ;
	if (!$nb) return 0 ;
	$obj=pmb_mysql_fetch_object($res) ;
	return $obj->id_resa ;
}

function affecte_cb ($cb) {
	// chercher s'il s'agit d'une notice ou d'un bulletin
	$rqt = "select expl_notice, expl_bulletin from exemplaires where expl_cb='".$cb."' ";
	$res = pmb_mysql_query($rqt) ;
	$nb=pmb_mysql_num_rows($res) ;
	if (!$nb) return 0 ;

	$obj=pmb_mysql_fetch_object($res) ;

	// chercher le premier (par ordre de rang, donc de date de début de résa, non validé
	$rqt = "select id_resa, resa_idempr from resa where resa_idnotice='".$obj->expl_notice."' and resa_idbulletin='".$obj->expl_bulletin."' and resa_cb='' and resa_date_fin='0000-00-00' order by resa_date ";
	$res = pmb_mysql_query($rqt) ;

	if (!pmb_mysql_num_rows($res)) return 0 ;

	$obj_resa=pmb_mysql_fetch_object($res) ;
	//MB 17/04/2015: Je ne peux pas valider une réservation à l'Opac car il n'y pas de mail envoyé au lecteur et les transferts ne sont pas gérés
	/*$nb_days = reservation::get_time($obj_resa->resa_idempr,$obj->expl_notice,$obj->expl_bulletin) ;

	// mettre resa_cb à jour pour cette resa
	$rqt = "update resa set resa_cb='".$cb."' " ;
	$rqt .= ", resa_date_debut=sysdate() " ;
	$rqt .= ", resa_date_fin=date_add(sysdate(), interval $nb_days DAY) " ;
	$rqt .= " where id_resa='".$obj_resa->id_resa."' ";
	$res = pmb_mysql_query($rqt);*/
	return $obj_resa->id_resa ;
}


function desaffecte_cb ($cb) {
	$rqt = "update resa set resa_cb='', resa_date_debut='0000-00-00', resa_date_fin='0000-00-00' where resa_cb='".$cb."' ";
	pmb_mysql_query($rqt) ;
	return pmb_mysql_affected_rows() ;
}

//   calcul du rang d'un emprunteur sur une réservation
function recupere_rang($id_empr, $id_notice, $id_bulletin) {
	$rank = 1;
	if (!$id_notice) $id_notice=0;
	if (!$id_bulletin) $id_bulletin=0 ;
	$query = "select resa_idempr from resa where resa_idnotice='".$id_notice."' and resa_idbulletin='".$id_bulletin."' order by resa_date";
	$result = pmb_mysql_query($query);
	while($resa=pmb_mysql_fetch_object($result)) {
		if($resa->resa_idempr == $id_empr) break;
		$rank++;
	}
	return $rank;
}

// retourne un tableau constitué des exemplaires disponibles pour une résa donnée
function expl_dispo ($no_notice=0, $no_bulletin=0) {
	// on récupère les données des exemplaires
	$requete = "SELECT expl_id, expl_cb, expl_cote, expl_notice, expl_bulletin, pret_retour, expl_location, location_libelle, expl_section, section_libelle, statut_libelle ";
	$requete .= " FROM docs_location, docs_section, docs_statut, exemplaires";
	$requete .= " LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl";
	$requete .= " WHERE expl_notice='$no_notice' and expl_bulletin='$no_bulletin' ";
	$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
	$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
	$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
	$requete .= " AND docs_statut.statut_allow_resa=1 and docs_statut.statut_visible_opac=1 and docs_location.location_visible_opac=1 and docs_section.section_visible_opac=1 ";
	$requete .= " order by location_libelle, section_libelle, expl_cote ";
	$result = pmb_mysql_query($requete);
	$tableau = array();
	while($expl = pmb_mysql_fetch_object($result)) {
		if(!$expl->pret_retour && !verif_cb_utilise($expl->expl_cb)) {
			$tableau[] = array (
				'expl_id' => $expl->expl_id,
				'expl_cb' => $expl->expl_cb,
				'expl_notice' => $expl->expl_notice,
				'expl_bulletin' => $expl->expl_bulletin,
				'expl_cote' => $expl->expl_cote,
			    'location' => translation::get_translated_text($expl->expl_location, "docs_location", "location_libelle", $expl->location_libelle),
			    'section' => translation::get_translated_text($expl->expl_section, "docs_section", "section_libelle", $expl->section_libelle),
				'statut' => $expl->statut_libelle ) ;
		}
	}
	return $tableau ;
}

function check_statut($id_notice=0, $id_bulletin=0) {
	global $opac_resa_dispo; 	// les résa de disponibles sont-elles autorisées ?
	global $opac_resa_planning;
	global $msg;
	global $message_resa,$empr_location,$pmb_location_reservation;

	// on checke s'il y a des exemplaires réservables et visibles
	if($id_notice) {
		$query = "select expl_id, expl_cb from exemplaires e, docs_statut s, docs_location l, docs_section se";
		$query .= " where (e.expl_notice='$id_notice'  ) and s.statut_allow_resa=1 and s.statut_visible_opac=1 and l.location_visible_opac=1 and se.section_visible_opac=1";
		$query .= " and s.idstatut=e.expl_statut";
		$query .= " and e.expl_location=l.idlocation";
		$query .= " and e.expl_section=se.idsection ";
	} elseif($id_bulletin) {
		$query = "select expl_id, expl_cb from exemplaires e, docs_statut s, docs_location l, docs_section se";
		$query .= " where (e.expl_bulletin='$id_bulletin' ) and s.statut_allow_resa=1 and s.statut_visible_opac=1 and l.location_visible_opac=1 and se.section_visible_opac=1" ;
		$query .= " and s.idstatut=e.expl_statut";
		$query .= " and e.expl_location=l.idlocation";
		$query .= " and e.expl_section=se.idsection ";
	} else {
		$message_resa.= "<strong>".$msg["resa_no_expl"]."</strong>";
		return 0;
	}
	if($pmb_location_reservation && !$empr_location) {
		$message_resa.= "<strong>".$msg["resa_no_expl"]."</strong>";
		return 0;
	}
	if($pmb_location_reservation) {
		$query.=" and e.expl_location in (select resa_loc from resa_loc where resa_emprloc=$empr_location) ";
	}
	$result = pmb_mysql_query($query);
	if(!pmb_mysql_num_rows($result)) {
		// aucun exemplaire n'est disponible pour le prêt
		$message_resa.= "<strong>".$msg["resa_no_expl"]."</strong>";
		return 0;
	}

	// Réservations possibles de documents sortis ?
	if($opac_resa_dispo=='2') {
		$nb_expl_available = count(expl_dispo($id_notice, $id_bulletin));
		if(!$nb_expl_available) return 0;
	}

	// on regarde si les résa de disponibles sont autorisées
	if ($opac_resa_dispo || $opac_resa_planning) return 1;

	// on checke si un exemplaire est disponible
	// aka. si un des exemplaires en circulation n'est pas mentionné dans la table des prêts,
	// c'est qu'il est disponible à la bibliothèque
	$list_dispo = '';

	while($reservable = pmb_mysql_fetch_object($result)) {
		$req2 = "select count(1) from pret where pret_idexpl=".$reservable->expl_id;
		$req2_result = pmb_mysql_query($req2);
		if(!pmb_mysql_result($req2_result, 0, 0)) {
			// l'exemplaire ne figure pas dans la table pret -> dispo
			// on récupère les données exemplaires pour constituer le message
			$req3 = "select p.expl_cb, p.expl_cote, p.expl_section, p.expl_location";
			$req3 .= " from exemplaires p";
			$req3 .= " where p.expl_id=".$reservable->expl_id." limit 1";
			$req3_result = pmb_mysql_query($req3);
			$req3_obj = pmb_mysql_fetch_object($req3_result);
			if($req3_obj->expl_cb) {
				// Si résa validé il n'est pas disponible en prêt
				$req4 = "select count(1) from resa where resa_cb='".$reservable->expl_cb."' and resa_confirmee='1'";
				$req4_result = pmb_mysql_query($req4);
				if(!pmb_mysql_result($req4_result, 0, 0)) {
				    $list_dispo .= '<br />'.translation::get_translated_text($req3_obj->expl_location, "docs_location", "location_libelle").'.';
				    $list_dispo .= translation::get_translated_text($req3_obj->expl_section, "docs_section", "section_libelle").' cote&nbsp;: '.$req3_obj->expl_cote;
				}
			}
		}
	}

	if($list_dispo) {
		$message_resa = "<b>{$msg["resa_doc_dispo"]}</b>";
		$message_resa .= $list_dispo;
		//signifie que : opac_resa_dispo == 0 && exemplaire(s) dispo(s)
		return 0;
// 		return 2;
	}

	// rien de spécial
	return  1;
}

function alert_mail_users_pmb($id_notice=0, $id_bulletin=0, $id_empr=0, $annul=0, $resa_planning=0, $id_resa = 0) {
    reservation::alert_mail_users_pmb($id_notice, $id_bulletin, $id_empr, $annul, $resa_planning, $id_resa);
}

function delete_cart_record($notice_id) {
	global $opac_cart_records_remove, $from_cart;

	if($opac_cart_records_remove && $from_cart) {
		$as=array_search($notice_id,$_SESSION["cart"]);
		if (($as!==null)&&($as!==false)) {
			unset($_SESSION["cart"][$as]);
		}
	}
}
