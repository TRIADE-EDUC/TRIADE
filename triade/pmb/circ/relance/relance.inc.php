<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relance.inc.php,v 1.115 2022/12/06 15:29:39 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $msg;
global $act, $empr, $id, $progress_bar;
global $pmb_lecteurs_localises, $pmb_utiliser_calendrier;
global $empr_sort_rows, $empr_show_rows, $empr_filter_rows;
global $deflt2docs_location, $relance_solo;
global $readers_relances_ui_selected_objects;

require_once $include_path."/mail.inc.php" ;
require_once $include_path."/mailing.inc.php";
require_once "$include_path/notice_authors.inc.php";
require_once "$include_path/sms.inc.php";

//Gestion des relances
require_once($class_path."/relance.class.php");
require_once($class_path."/readers/readers_relances_controller.class.php");

function send_mail($id_empr, $relance) {
	mail_reader_loans_late_relance::set_niveau_relance($relance);
	$mail_reader_loans_late_relance = mail_reader_loans_late_relance::get_instance();
	$mail_reader_loans_late_relance->set_mail_to_id($id_empr);
	$mail_reader_loans_late_relance->send_mail();
	return true;
}

function print_relance($id_empr,$mail=true) {
	global $mailretard_priorite_email, $mailretard_priorite_email_2, $mailretard_priorite_email_3;
	global $msg, $pmb_gestion_financiere, $pmb_gestion_amende;
	global $mail_sended;
	
	$mail_sended=0;
	$not_mail=0;
	if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
		$req="delete from cache_amendes where id_empr=".$id_empr;
		pmb_mysql_query($req);
		$amende=new amende($id_empr);
		$level=$amende->get_max_level();
		$niveau_min=$level["level_min"];
		$id_expl=$level["level_min_id_expl"];
		$total_amende = $amende->get_total_amendes();
	}
	
	//Si mail de rappel affecté au groupe, on envoie au responsable
	$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=$id_empr and resp_groupe and mail_rappel limit 1";
	$res=pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($res) > 0) {
		$requete="select empr_mail, empr_tel1, empr_sms from empr where id_empr='".pmb_mysql_result($res,0,1)."'";
		$res=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($res)) {
		    list($empr_mail, $empr_tel1, $empr_sms)=pmb_mysql_fetch_row($res);
		}
	} else {
		$requete="select empr_mail, empr_tel1, empr_sms from empr where id_empr=$id_empr";
		$resultat=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($resultat)) {
			list($empr_mail, $empr_tel1, $empr_sms)=pmb_mysql_fetch_row($resultat);
		}
	}
	
	if ($niveau_min) {
		//Par mail
		//JP 05/06/2017 : je passe par un flag car l'imbrication de conditions se complique...
		$flag_print=false;
		if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($empr_mail))&&( ($niveau_min<3)||($mailretard_priorite_email_3) )&&($mail)) {
			$flag_print=true;
			if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
				//On force en lettre
				$flag_print=false;
			}
		}
		
		if ($flag_print) {
			if (send_mail($id_empr,$niveau_min)) {
				$requete="update pret set printed=1 where pret_idexpl=".$id_expl;
				pmb_mysql_query($requete);
				$mail_sended=1;
			}
			//3ème niveau de relance par mail et par lettre
			if (($niveau_min==3) && ($mailretard_priorite_email_3==2)) {
				$requete="update pret set printed=2 where pret_idexpl=".$id_expl;
				pmb_mysql_query($requete);
				$not_mail=1;
			}
		} else {
			$requete="update pret set printed=2 where pret_idexpl=".$id_expl;
			pmb_mysql_query($requete);
			$not_mail=1;
		}
		
		//Par sms
		global $empr_sms_msg_retard;
		if($empr_tel1 && $empr_sms && $empr_sms_msg_retard) {
		    send_sms(0, $niveau_min, $empr_tel1, $empr_sms_msg_retard);
		}
		
	}
	$req="delete from cache_amendes where id_empr=".$id_empr;
	pmb_mysql_query($req);
	//On loggue les infos de la lettre
	$niveau_courant = $niveau_min;
	
	if($niveau_courant){
		
		$niveau_suppose = $level["level_normal"];
		$cpt_id=comptes::get_compte_id_from_empr($id_empr,2);
		$cpt=new comptes($cpt_id);
		$solde=$cpt->update_solde();
		$frais_relance=$cpt->summarize_transactions("","",0,$realisee=-1);
		if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
		
		$req="insert into log_retard (niveau_reel,niveau_suppose,amende_totale,frais,idempr,log_printed,log_mail) values('".$niveau_courant."','".$niveau_suppose."','".$total_amende."','".$frais_relance."','".$id_empr."', '".$not_mail."', '".$mail_sended."')";
		pmb_mysql_query($req);
		$id_log_ret = pmb_mysql_insert_id();
		
		$reqexpl = "select pret_idexpl as expl from pret where pret_retour<	CURDATE() and pret_idempr=$id_empr";
		$resexple=pmb_mysql_query($reqexpl);
		while(($liste = pmb_mysql_fetch_object($resexple))){
			$dates_resa_sql = " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour " ;
			$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, pret_idempr, expl_id, expl_cb,expl_cote, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date!='', concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql.", " ;
			$requete.= " notices_m.tparent_id, notices_m.tnvol " ;
			$requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
			$requete.= " WHERE expl_id='".$liste->expl."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
			$res_det_expl = pmb_mysql_query($requete) ;
			$expl = pmb_mysql_fetch_object($res_det_expl);
			if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
				$amd = $amende->get_amende($liste->expl);
			}
			$req_ins="insert into log_expl_retard (titre,expl_id,expl_cb,date_pret,date_retour,amende,num_log_retard) values('".addslashes($expl->tit)."','".$expl->expl_id."','".$expl->expl_cb."','".$expl->pret_date."','".$expl->pret_retour."','".$amd["valeur"]."','".$id_log_ret."')";
			pmb_mysql_query($req_ins);
		}
	}
	return $not_mail;
}


// Pour localiser les relances : $deflt2docs_location, $pmb_lecteurs_localises, $empr_location_id ;
$loc_filter = "";
if ($pmb_lecteurs_localises) {
	$loc_filter = "and empr_location = '".$deflt2docs_location."' ";
}

//Traitement avant affichage
if(!empty($relance_solo)) {
	readers_relances_controller::set_id_empr($relance_solo);
}
if(empty($empr) && !empty($readers_relances_ui_selected_objects)) {
	$empr = $readers_relances_ui_selected_objects;
}
if(!empty($empr)) {
	readers_relances_controller::set_empr($empr);
}
//switch $act
if(!empty($act)) {
	readers_relances_controller::proceed();
	unset($act);
}

// Juste pour la progress bar , on execute ceci:
$req ="select id_empr  from empr, pret, exemplaires, empr_categ where 1 ";
$req.= $loc_filter;
$req.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr";
$res=pmb_mysql_query($req);

$nb=pmb_mysql_num_rows($res);
if($nb>2){
	$progress_bar=new progress_bar($msg["relance_progress_bar"],$nb,3);
}

// Calendrier activé : Est-il bien paramétré sur le site de gestion par défaut des lecteurs ?
if ($pmb_utiliser_calendrier) {
	$req_date_calendrier = "select count(num_location) as nb from ouvertures where date_ouverture >=curdate() and ouvert=1 and num_location=".$deflt2docs_location;
	$res_date_calendrier = pmb_mysql_query($req_date_calendrier);
	if ($res_date_calendrier) {
		if (!pmb_mysql_result($res_date_calendrier, 0, "nb")) {
			warning("", "<span class='erreur'>".$msg["calendrier_active_and_empty"]."</span>");
		}
	}
}

if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
	$filter_list = relance::get_instance_filter_list();
	list_readers_relances_ui::set_used_filter_list_mode(true);
	list_readers_relances_ui::set_filter_list($filter_list);
}

//switch $action / Affichage de la liste ou de la personnalisation de jeux de données
readers_relances_controller::proceed($id);
if($progress_bar)$progress_bar->hide();

?>