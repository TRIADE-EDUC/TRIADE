<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa.inc.php,v 1.36 2019-05-29 12:12:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $aff_alerte, $msg;

$temp_aff = resa_a_traiter() . resa_a_ranger() . resa_depassees_a_traiter(). resa_planning_a_traiter();

if ($temp_aff) $aff_alerte .= "<ul>".$msg['resa_menu_alert'].$temp_aff."</ul>" ;

function resa_a_traiter() {
	global $msg;
	global $pmb_transferts_actif,$transferts_choix_lieu_opac,$deflt_docs_location, $pmb_location_reservation,$transferts_site_fixe,$pmb_lecteurs_localises;
	
	$sql="SELECT resa_idnotice, resa_idbulletin FROM resa, exemplaires, docs_statut  WHERE (resa_cb is null OR resa_cb='') 
	and resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin 
	and expl_statut=idstatut AND pret_flag=1	
	limit 1";
	
	if($pmb_lecteurs_localises && $deflt_docs_location){
		$sql="SELECT resa_idnotice, resa_idbulletin FROM resa, exemplaires, docs_statut  WHERE (resa_cb is null OR resa_cb='') 
		and resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin  
		and expl_location='".$deflt_docs_location."'
		and expl_statut=idstatut AND pret_flag=1	
		limit 1";		
	}	
	// respecter les droits de réservation du lecteur 
	if($pmb_location_reservation)
		$sql="SELECT resa_idnotice, resa_idbulletin FROM resa, empr, resa_loc, exemplaires , docs_statut WHERE 
		resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin 
		and expl_location='".$deflt_docs_location."' 
		and	expl_statut=idstatut AND pret_flag=1 
		and	resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') 
		and empr_location=resa_emprloc and resa_loc='$deflt_docs_location' 
		limit 1";
	
	if ($pmb_transferts_actif=="1") {
		switch ($transferts_choix_lieu_opac) {
			case "1":
				//retrait de la resa sur lieu choisi par le lecteur
				$sql="SELECT resa_idnotice, resa_idbulletin FROM resa, empr WHERE resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') AND resa_loc_retrait='".$deflt_docs_location."' limit 1";
			break;		
			case "2":
				//retrait de la resa sur lieu fixé
				if ($deflt_docs_location==$transferts_site_fixe)
					$sql="SELECT resa_idnotice, resa_idbulletin FROM resa WHERE (resa_cb is null OR resa_cb='') limit 1";
				else return "";	
				 	
			break;		
			case "3":
				//retrait de la resa sur lieu exemplaire
				// respecter les droits de réservation du lecteur 
				if($pmb_location_reservation)
					$sql = "select resa_idnotice, resa_idbulletin from resa, exemplaires,empr, resa_loc where resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') and empr_location=resa_emprloc and resa_loc='$deflt_docs_location' and 
							resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin and expl_location=resa_loc limit 1";
				else 
					$sql = "select resa_idnotice, resa_idbulletin from resa, exemplaires,empr where resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') and 
							resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin and expl_location='".$deflt_docs_location."' limit 1";
			break;		
			default:
				//retrait de la resa sur lieu lecteur
				$sql="SELECT resa_idnotice, resa_idbulletin FROM resa, empr WHERE resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') AND empr_location='".$deflt_docs_location."' limit 1";
			break;			
		}
	
	}
	$result = pmb_mysql_query($sql); 
	if($result && pmb_mysql_num_rows($result)) {
		return "<li><a href='./circ.php?categ=listeresa&sub=encours' target='_parent'>".$msg['resa_menu_a_traiter']."</a></li>";
	} else {
		return "";
	}
}

function resa_a_ranger() {
	global $msg,$deflt_docs_location;
	
	$sql="SELECT count(1) from resa_ranger,exemplaires where resa_cb=expl_cb and expl_location='$deflt_docs_location' limit 1 ";
	$result = pmb_mysql_query($sql) ;
	if ($result && pmb_mysql_result($result, 0, 0)) {
		return "<li><a href='./circ.php?categ=listeresa&sub=docranger' target='_parent'>".$msg['resa_menu_a_ranger']."</a></li>" ;
	} else {
		return "";
	}
}

function resa_depassees_a_traiter() {
	global $msg,$pmb_transferts_actif, $deflt_docs_location,$transferts_choix_lieu_opac;
		
	$sql="SELECT 1 FROM resa, empr WHERE resa_idempr = id_empr AND resa_date_fin < CURDATE() and resa_date_fin <>  '0000-00-00' ";
	if ($pmb_transferts_actif=="1") {
		switch ($transferts_choix_lieu_opac) {
			case "1":
				//retrait de la resa sur lieu choisi par le lecteur
				$sql .= " AND resa_loc_retrait='".$deflt_docs_location."' ";
			break;		
			case "2":
				//retrait de la resa sur lieu fixé
			break;		
			case "3":
				//retrait de la resa sur lieu exemplaire
			break;	
			default:
				//retrait de la resa sur lieu lecteur
				$sql .= " AND empr_location='".$deflt_docs_location."' ";
			break;					
		}
	
	}
	
	// comptage des résas dépassées 
	//$sql="SELECT 1 FROM resa where resa_date_fin < CURDATE() and resa_date_fin <> '0000-00-00' limit 1 ";
	$result = pmb_mysql_query($sql); 
	if (!$result || !pmb_mysql_num_rows($result)) {
		return "" ;
	}
	return "<li><a href='./circ.php?categ=listeresa&sub=depassee' target='_parent'>".$msg['resa_menu_a_depassees']."</a></li>" ;

}

function resa_planning_a_traiter() {
	global $msg, $charset;
	global $pmb_resa_planning, $pmb_resa_planning_toresa, $deflt_resas_location, $deflt_docs_location;

	$ret='';
	if($pmb_resa_planning) {
		$pmb_resa_planning_toresa+=0;
		if ($deflt_resas_location) {
			$expl_loc = $deflt_resas_location;
		} else {
			$expl_loc = $deflt_docs_location;
		}
		$query = "SELECT count(*) ";
		$query.= "FROM resa_planning ";
		$query.= "WHERE resa_remaining_qty!=0 ";
		$query.= "and resa_validee=0 ";
		$query.= "and resa_loc_retrait = $expl_loc ";
		$query.= "and datediff(resa_date_debut, curdate()) <= ".$pmb_resa_planning_toresa;
		
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_result($result,0,0)) {
			$ret .= "<li><a href='./circ.php?categ=resa_planning&sub=all&montrerquoi=invalidees' target='_parent'>".$msg['resa_planning_to_validate']."</a></li>" ;
		}
		$query = "SELECT count(*) ";
		$query.= "FROM resa_planning ";
		$query.= "WHERE resa_remaining_qty!=0 ";
		$query.= "and resa_validee=1 ";
		$query.= "and resa_loc_retrait = $expl_loc ";
		$query.= "and datediff(resa_date_debut, curdate()) <= ".$pmb_resa_planning_toresa;

		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_result($result,0,0)) {
			$ret .= "<li><a href='./circ.php?categ=resa_planning&sub=all' target='_parent'>".$msg['resa_planning_todo']."</a></li>" ;
		}
	}
	return $ret;
}