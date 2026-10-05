<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relance_export.php,v 1.10 2022/09/30 13:59:42 dgoron Exp $

//Affichage des recouvrements pour un lecteur, format Excel HTML

// définition du minimum nécéssaire 
$base_path="../..";                            
$base_auth = "CIRCULATION_AUTH";  
$base_noheader=1;
$base_nosession=1;
//$base_nocheck = 1 ;
require_once ("$base_path/includes/init.inc.php");  

global $class_path, $msg, $charset, $pmb_lecteurs_localises, $empr_export;

header("Content-Type: application/download\n");
header("Content-Disposition: atachement; filename=\"tableau.xls\"");

require_once($class_path."/emprunteur.class.php");
require_once($class_path."/comptes.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/amende.class.php");

$export_relance_tpl="<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head>
<body>
	<table>
		<tr>
			<th>".$msg["relance_export_empr_cb"]."</th>
			<th>".$msg["relance_export_empr_name"]."</th>
			<th>".$msg["relance_export_empr_surname"]."</th>
			<th>".$msg["relance_export_empr_adr1"]."</th>
			<th>".$msg["relance_export_empr_adr2"]."</th>
			<th>".$msg["relance_export_empr_cp"]."</th>
			<th>".$msg["relance_export_empr_ville"]."</th>
			<th>".$msg["relance_export_empr_mail"]."</th>
			<th>".$msg["relance_export_empr_tel1"]."</th>
			<th>".$msg["relance_export_empr_tel2"]."</th>
			".($pmb_lecteurs_localises ? "<th>".$msg["relance_export_empr_location"]."</th>" : "")."
			<th>".$msg["relance_export_empr_categ"]."</th>
			<th>".$msg["relance_export_empr_codestat"]."</th>
			<th>".$msg["relance_export_empr_statut"]."</th>
			<th>".$msg["relance_export_empr_groupes"]."</th>
			<th>".$msg["relance_export_empr_niveau_suppose"]."</th>
			<th>".$msg["relance_export_empr_total_amende"]."</th>
			<th>".$msg["relance_export_empr_frais_relance"]."</th>
			<th>".$msg["relance_export_expl_titre"]."</th>
			<th>".$msg["relance_export_expl_cb"]."</th>
			<th>".$msg["relance_export_expl_cote"]."</th>
			<th>".$msg["relance_export_expl_codestat"]."</th>
			<th>".$msg["relance_export_expl_section"]."</th>
			<th>".$msg["relance_export_expl_pret_date"]."</th>
			<th>".$msg["relance_export_expl_pret_retour"]."</th>
			<th>".$msg["relance_export_expl_niveau_relance"]."</th>
			<th>".$msg["relance_export_expl_date_relance"]."</th>
			<th>".$msg["relance_export_expl_mail"]."</th>
			<th>".$msg["relance_export_expl_printed"]."</th>
			<th>".$msg["relance_export_expl_amende"]."</th>
		</tr>
		!!relance_liste!!
	</table>
</body>
</html>";

$relance_liste = "";
$req ="select id_empr  from empr, pret, exemplaires, empr_categ where 1 ";
$req.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ ";
if(isset($empr_export) && is_array($empr_export)) {
    $req.= "and id_empr in (".implode(",",$empr_export).") ";
}
$req.= "group by id_empr";
$res=pmb_mysql_query($req);
while ($r=pmb_mysql_fetch_object($res)) {
	$relance_liste.=get_relance($r->id_empr);
}

print str_replace("!!relance_liste!!",$relance_liste,$export_relance_tpl);

function get_relance($id_empr){
	global $charset, $msg, $pmb_gestion_financiere, $pmb_gestion_amende;
	global $pmb_lecteurs_localises;

	$info = "";
	// liste des relances
	if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
		$amende=new amende($id_empr);
		$level=$amende->get_max_level();
		$niveau_min=$level["level_min"];
		$id_expl=$level["level_min_id_expl"];
		$total_amende = $amende->get_total_amendes();
	}
	
	$niveau_suppose = $level["level_normal"];
	$cpt_id=comptes::get_compte_id_from_empr($id_empr,2);
	$cpt=new comptes($cpt_id);

	$frais_relance=$cpt->summarize_transactions("","",0,-1);
	if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
	
	$empr=new emprunteur($id_empr,'', FALSE, 0);	
	
	//Groupes
	if (count($empr->groupes)) {
		$lib_groupes=strip_tags(implode(" / ",$empr->groupes));
	} else {
		$lib_groupes = "";
	}
	$info_empr="
	<td>".$empr->cb."</td>
	<td>".$empr->nom."</td>
	<td>".$empr->prenom."</td>
	<td>".$empr->adr1."</td>
	<td>".$empr->adr2."</td>
	<td>".$empr->cp."</td>
	<td>".$empr->ville."</td>
	<td>".$empr->mail."</td>
	<td>".$empr->tel1."</td>
	<td>".$empr->tel2."</td>
	".($pmb_lecteurs_localises ? "<td>".$empr->empr_location_l."</td>" : "")."
	<td>".$empr->cat_l."</td>
	<td>".$empr->cstat_l."</td>
	<td>".$empr->empr_statut_libelle."</td>
	<td>".$lib_groupes."</td>
	<td>".$niveau_suppose."</td>
	<td>".$total_amende."</td>
	<td>".$frais_relance."</td>
	";
	
	$reqexpl = "select pret_idexpl as expl from pret where pret_retour<CURDATE() and pret_idempr=$id_empr";
	
	$resexple=pmb_mysql_query($reqexpl);
	while(($liste = pmb_mysql_fetch_object($resexple))){			
		$dates_resa_sql = " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour " ;
		
		$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, pret_idempr, expl_id, expl_cb,expl_cote, pret_date, pret_retour,
		niveau_relance,
		date_relance,
		printed,		
		tdoc_libelle, section_libelle, location_libelle, codestat_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date!='', concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql.", " ;
		$requete.= " notices_m.tparent_id, notices_m.tnvol " ; 
		$requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, docs_codestat, pret ";
		$requete.= " WHERE expl_id='".$liste->expl."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and expl_codestat = idcode and pret_idexpl = expl_id  ";
		$res_det_expl = pmb_mysql_query($requete) ;
		$expl = pmb_mysql_fetch_object($res_det_expl);
				
		$amd = $amende->get_amende($liste->expl);
		
		$reqlog = "select sum(log.log_printed) as printed, sum(log.log_mail) as mail
			from log_retard as log, log_expl_retard  as expl where log.idempr=$id_empr and  log.niveau_reel='".$expl->niveau_relance."'
		 	and expl.num_log_retard=log.id_log and expl_id='".$liste->expl."' ";
		
		$reslog=pmb_mysql_query($reqlog);
		if($log=pmb_mysql_fetch_object($reslog)) {
			$printed=$log->printed;
			$mail=$log->mail;
		} else {
			$printed=0;
			$mail=0;
		}
		
		$info.="
		<tr>
			$info_empr
			<td>".htmlentities($expl->tit,ENT_QUOTES,$charset)."</td>
			<td>".htmlentities($expl->expl_cb,ENT_QUOTES,$charset)."</td>
			<td>".htmlentities($expl->expl_cote,ENT_QUOTES,$charset)."</td>
			<td>".htmlentities($expl->codestat_libelle,ENT_QUOTES,$charset)."</td>
			<td>".htmlentities($expl->section_libelle,ENT_QUOTES,$charset)."</td>
			<td>".format_date($expl->pret_date)."</td>
			<td>".format_date($expl->pret_retour)."</td>
			<td>".$expl->niveau_relance."</td>
			<td>".format_date($expl->date_relance)."</td>
			<td>".htmlentities($mail,ENT_QUOTES,$charset)."</td>
			<td>".htmlentities($printed,ENT_QUOTES,$charset)."</td>
			<td>".htmlentities($amd["valeur"],ENT_QUOTES,$charset)."</td>
		</tr>
		";

	}		
	return $info;
}

?>