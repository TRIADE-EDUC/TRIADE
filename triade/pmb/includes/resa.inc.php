<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa.inc.php,v 1.62 2022/08/03 13:04:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path;
require_once($include_path."/mail.inc.php") ;
require_once($include_path."/sms.inc.php") ;

function alert_empr_resa($id_resa=0, $id_empr_concerne=0, $print_mode=0) {
	global $msg;
	global $pdflettreresa_priorite_email ;
	global $bouton_impr_conf, $pdflettreresa_priorite_email_manuel;
	global $empr_sms_msg_resa_dispo;
	global $class_path;
	
	global $use_opac_url_base; $use_opac_url_base=1; 
	
	// si c'est une impression à partir du bouton, on prend le paramètre ad hoc
	if ($bouton_impr_conf) $pdflettreresa_priorite_email = $pdflettreresa_priorite_email_manuel ;
    
	if ($pdflettreresa_priorite_email==3) return;
	$query = "select distinct "; 	
	$query .= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";  
	$query .= "date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date_resa, ";
	$query .= "date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, ";
	$query .= "date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, ";
	$query .= "id_empr, empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_sms, id_resa, ";
	$query .= "trim(concat(ifnull(notices_m.niveau_biblio,''), ifnull(notices_s.niveau_biblio,''))) as niveau_biblio, ";
	$query .= "trim(concat(ifnull(notices_m.notice_id,''), ifnull(notices_s.notice_id,''))) as id_notice ";
	$query .= "from (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), empr ";
	$query .= "where id_resa in (".$id_resa.") and resa_idempr=id_empr";
	if ($id_empr_concerne) $query .= " and id_empr=$id_empr_concerne ";

	$result = pmb_mysql_query($query);
	
	$tab_resa = array();
	while ($empr=pmb_mysql_fetch_object($result)) {
		$rqt_maj = "update resa set resa_confirmee=1 where id_resa in (".$id_resa.") AND resa_cb is not null and resa_cb!=''" ;
		if ($id_empr_concerne) $rqt_maj .= " and resa_idempr=$id_empr_concerne ";
		pmb_mysql_query($rqt_maj);
		
		require_once($class_path.'/event/events/event_resa.class.php');
		$evt = new event_resa('resa', 'alert_empr_resa');
		$evt->set_empr_id($empr->id_empr);
		$evth = events_handler::get_instance();
		$evth->send($evt);
		$lock_validation = false;
		if($evt->get_result()){
		    $lock_validation = $evt->get_result()["lock_validation"];
		}
		if ($lock_validation) continue;
		
		if (($pdflettreresa_priorite_email==1 || $pdflettreresa_priorite_email==2) && $empr->empr_mail) {
			//S'il n'y à pas de paramètre de $lock_validation pour bloquer l'envoi du mail
			if (!$lock_validation){
    			$to = $empr->empr_prenom." ".$empr->empr_nom." <".$empr->empr_mail.">";
    			$mail_reader_resa = new mail_reader_resa();
    			$mail_reader_resa->set_mail_to_id($empr->id_empr);
    			$mail_reader_resa->set_empr($empr);
    			$res_envoi = $mail_reader_resa->send_mail();  			
			}
			if (empty($res_envoi) || $pdflettreresa_priorite_email==2) {
			    if(is_resa_confirme($empr->id_resa)) {
			        $tab_resa[] = $empr->id_resa;
			    }
			}
		} elseif ($pdflettreresa_priorite_email!=3) {
		    if(is_resa_confirme($empr->id_resa)) {
		        $tab_resa[] = $empr->id_resa;
		    }
		}				
		if(is_resa_confirme($empr->id_resa) && $empr->empr_tel1 && $empr->empr_sms && $empr_sms_msg_resa_dispo && !$lock_validation){		
			$res_envoi_sms=send_sms(1, 0, $empr->empr_tel1,$empr_sms_msg_resa_dispo);
		}		
	} // end while
	if (empty($lock_validation) && !empty($tab_resa)){
        $valeur_tab = implode(',',$tab_resa);		
	    if($valeur_tab && !$print_mode) print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=lettre_resa&id_resa=$valeur_tab', 'lettre_confirm_resa".$id_resa."', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');</script>";
	}
}
	
//Fonction de test si la resa est valide ou non
function is_resa_confirme($id_resa=0){
	$rqt = "select * from resa where id_resa=$id_resa and resa_cb is not null and resa_cb!='' order by resa_idempr ";
	$res = pmb_mysql_query($rqt) ;
	
	while ($resa_lue = pmb_mysql_fetch_object($res)) {
		if ($resa_lue->resa_confirmee) {
			// archivage 
			$rqt_arch = "UPDATE resa_archive, resa, exemplaires SET
			resarc_confirmee = 1,
			resarc_loc_retrait = resa_loc_retrait,
			resarc_cb = resa_cb,
			resarc_debut = resa_date_debut,
			resarc_fin = resa_date_fin, 
			resarc_expl_typdoc = expl_typdoc,
			resarc_expl_cote = expl_cote,
			resarc_expl_statut = expl_statut,
			resarc_expl_location = expl_location,
			resarc_expl_codestat =expl_codestat,
			resarc_expl_owner = expl_owner,
			resarc_expl_section = expl_section			
			WHERE id_resa = $id_resa AND resa_arc = resarc_id AND  resa_cb = expl_cb "; 
			pmb_mysql_query($rqt_arch);
			
			return true;
		}
	} 
	return false;
	
}
