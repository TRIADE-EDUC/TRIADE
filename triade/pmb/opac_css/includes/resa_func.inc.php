<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_func.inc.php,v 1.51 2018-11-20 15:44:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/quotas.class.php");

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
	/*$nb_days = get_time($obj_resa->resa_idempr,$obj->expl_notice,$obj->expl_bulletin) ;

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
	$res = pmb_mysql_query($rqt) ;
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

//Récupération de la durée de réservation pour une notice ou un bulletin et un emprunteur
function get_time($id_empr,$id_notice,$id_bulletin) {
	global $pmb_quotas_avances;

	//Si les quotas avancés sont actifs
	if ($pmb_quotas_avances) {
		$struct=array();
		if ($id_notice) {
			$struct["NOTI"]=$id_notice;
			$quota_type="BOOK_TIME_QUOTA";
		} else {
			$struct["BULL"]=$id_bulletin;
			$quota_type="BOOK_TIME_SERIAL_QUOTA";
		}
		$struct["READER"]=$id_empr;
		$qt=new quota($quota_type);
		$t=$qt->get_quota_value($struct);
		if ($t==-1) $t=0;
	} else {
		//Sinon je regarde la durée de réservation la plus défavorable par type de document
		if ($id_notice)
			$requete="select min(duree_resa) from docs_type, exemplaires where expl_notice='$id_notice' and expl_typdoc=idtyp_doc";
		else
			$requete="select min(duree_resa) from docs_type, exemplaires where expl_bulletin='$id_bulletin' and expl_typdoc=idtyp_doc";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) $t=pmb_mysql_result($resultat,0,0); else $t=0;
	}
	return $t;
}

// retourne un tableau constitué des exemplaires disponibles pour une résa donnée
function expl_dispo ($no_notice=0, $no_bulletin=0) {
	// on récupère les données des exemplaires
	$requete = "SELECT expl_id, expl_cb, expl_cote, expl_notice, expl_bulletin, pret_retour, location_libelle, section_libelle, statut_libelle ";
	$requete .= " FROM exemplaires, docs_location, docs_section, docs_statut";
	$requete .= " LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl";
	$requete .= " WHERE expl_notice='$no_notice' and expl_bulletin='$no_bulletin' ";
	$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
	$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
	$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
	$requete .= " order by location_libelle, section_libelle, expl_cote ";
	$result = pmb_mysql_query($requete);
	while($expl = pmb_mysql_fetch_object($result)) {
		if(!$expl->pret_retour && !verif_cb_utilise($expl->expl_cb))
			$tableau[] = array (
				'expl_id' => $expl->expl_id,
				'expl_cb' => $expl->expl_cb,
				'expl_notice' => $expl->expl_notice,
				'expl_bulletin' => $expl->expl_bulletin,
				'expl_cote' => $expl->expl_cote,
				'location' => $expl->location_libelle,
				'section' => $expl->section_libelle,
				'statut' => $expl->statut_libelle ) ;
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

	// on regarde si les résa de disponibles sont autorisées
	if ($opac_resa_dispo=='1' || $opac_resa_planning=='1') return 1;

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
			$req3 = "select p.expl_cb, p.expl_cote, s.section_libelle, l.location_libelle ";
			$req3 .= " from exemplaires p, docs_section s, docs_location l";
			$req3 .= " where p.expl_id=".$reservable->expl_id;
			$req3 .= " and s.idsection=p.expl_section";
			$req3 .= " and l.idlocation=p.expl_location limit 1";
			$req3_result = pmb_mysql_query($req3);
			$req3_obj = pmb_mysql_fetch_object($req3_result);
			if($req3_obj->expl_cb) {
				// Si résa validé il n'est pas disponible en prêt
				$req4 = "select count(1) from resa where resa_cb='".$reservable->expl_cb."' and resa_confirmee='1'";
				$req4_result = pmb_mysql_query($req4);
				if(!pmb_mysql_result($req4_result, 0, 0)) {
					$list_dispo .= '<br />'.$req3_obj->location_libelle.'.';
					$list_dispo .= $req3_obj->section_libelle.' cote&nbsp;: '.$req3_obj->expl_cote;
				}
			}
		}
	}

	if($list_dispo) {
		$message_resa = "<b>$msg[resa_doc_dispo]</b>";
		$message_resa .= $list_dispo;
		//signifie que : opac_resa_dispo == 0 && exemplaire(s) dispo(s)
		return 0;
// 		return 2;
	}

	// rien de spécial
	return  1;
}

function alert_mail_users_pmb($id_notice=0, $id_bulletin=0, $id_empr, $annul=0, $resa_planning=0) {
	global $msg, $charset;
	global $opac_biblio_name, $opac_biblio_email ;
	global $opac_url_base ;
	global $pmb_location_reservation,$pmb_resa_alert_localized;
	global $pmb_transferts_actif, $transferts_choix_lieu_opac, $idloc;
	global $use_opac_url_base;
	
	//Pas très propre mais pas mieux pour le moment / Réaffectation à la fin de la méthode
	$temp_use_opac_url_base = $use_opac_url_base;
	$use_opac_url_base=1;
	
	// paramétrage OPAC: choix du nom de la bibliothèque comme expéditeur
	$requete = "select location_libelle, email, empr_location from empr, docs_location where empr_location=idlocation and id_empr='$id_empr' ";
	$res = pmb_mysql_query($requete);
	$loc=pmb_mysql_fetch_object($res) ;
	$PMBusernom = $loc->location_libelle ;
	$PMBuserprenom = '' ;
	$PMBuseremail = $loc->email ;
	if ($PMBuseremail) {
		$query = "select distinct empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_tel2, empr_ville, location_libelle, nom, prenom, user_email, date_format(sysdate(), '".$msg["format_date_heure"]."') as aff_quand, deflt2docs_location  from empr, docs_location, users where id_empr='$id_empr' and empr_location=idlocation and user_email like('%@%') and user_alert_resamail=1";
		$result = @pmb_mysql_query($query);
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=".$charset."\n";
		$output_final='';
		while ($empr=@pmb_mysql_fetch_object($result)) {
			if ($pmb_location_reservation && $pmb_resa_alert_localized) {
				if ($loc->empr_location!=$empr->deflt2docs_location) {
					continue;
				}
			}
			if (!$output_final) {
				$output_final = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>" ;
				if ($annul==1) {
					if ($resa_planning) {
						$sujet = $msg["mail_obj_resa_planning_canceled"] ;
					} else {
						$sujet = $msg["mail_obj_resa_canceled"] ;
					}
					$output_final .= "<a href='".$opac_url_base."'><span style='color:red'><strong>".$sujet ;					
				} elseif ($annul==2) {
					if ($resa_planning) {
						$sujet = $msg["mail_obj_resa_planning_reaffected"] ;
					} else {
						$sujet = $msg["mail_obj_resa_reaffected"] ;
					}
					$output_final .= "<a href='".$opac_url_base."'><span style='color:blue'><strong>".$sujet ;					
				} else {
					if ($resa_planning) {
						$sujet = $msg["mail_obj_resa_planning_added"] ;
					} else {
						$sujet = $msg["mail_obj_resa_added"] ;
					}
					$output_final .= "<a href='".$opac_url_base."'><span style='color:green'><strong>".$sujet ;					
				}
				$output_final .= "</strong></span></a> ".$empr->aff_quand."
									<br /><strong>".$empr->empr_prenom." ".$empr->empr_nom."</strong>
									<br /><i>".$empr->empr_mail." / ".$empr->empr_tel1." / ".$empr->empr_tel2."</i>";
				if ($empr->empr_cp || $empr->empr_ville) $output_final .= "<br /><u>".$empr->empr_cp." ".$empr->empr_ville."</u>";
				$output_final .= "<hr />".$msg['resa_empr_location'].": ".$empr->location_libelle;
				if (($pmb_transferts_actif=="1")&&($transferts_choix_lieu_opac=="1")) {
					$docs_location = new docs_location($idloc);
					$output_final .= "<br />".$msg['resa_loc_retrait'].": ".$docs_location->libelle;
				}
				$output_final .= "<hr />";
				if ($id_notice) {
					record_display::init_record_datas($id_notice);
					$current = new notice_affichage($id_notice,array(),0,1);
					$current->do_header();
					$current->do_isbd(1,1);
					$output_final .= "<h3>".$current->notice_header."</h3>";
					$output_final .= $current->notice_isbd;
					$output_final .= $current->affichage_expl ;
				} else {
					$output_final .= bulletin_affichage_reduit($id_bulletin) ;
				}
				$output_final .= "<hr /></body></html> ";
			}
			$res_envoi=mailpmb($empr->nom." ".$empr->prenom, $empr->user_email,$sujet." ".$empr->aff_quand,$output_final,$PMBusernom, $PMBuseremail, $headers, "", "", 1);
		}
	}
	$use_opac_url_base = $temp_use_opac_url_base;
}
