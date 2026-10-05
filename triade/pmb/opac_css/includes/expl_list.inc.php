<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_list.inc.php,v 1.14 2024/01/12 16:00:45 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Localisation des exemplaires

function expl_list($type,$id) {	
	global $dbh;
	global $msg;
	
	$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*";
	$requete .= " FROM exemplaires, docs_location, docs_section, docs_statut";
	$requete .= " LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl";
	// selon le type de données d'origine : ouvrage ou bulletin d'un périodique
	// on adapte la requête
	switch ($type){
		case 'b' :
			$requete .= " WHERE expl_bulletin='$id'";
			break;
		case 'm' :
		default:
			$requete .= " WHERE expl_notice='$id'";
			break;
	}
	$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
	$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
	$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
	$requete .= " AND section_visible_opac = 1 ";
	$requete .= " AND statut_visible_opac = 1 ";
	
	// récupération du nombre d'exemplaires
	$res = pmb_mysql_query($requete, $dbh);
	$compteur=0;
	while($expl = pmb_mysql_fetch_object($res)) {
		$compteur = $compteur+1;
		$expl_liste .= "<tr><td>";
		$expl_liste .= $expl->expl_cb."&nbsp;";
		$expl_liste .= "</td><td><strong>";
		$expl_liste .= $expl->expl_cote."&nbsp;";
		$expl_liste .= "</strong></td><td>";
		$expl_liste .= translation::get_translated_text($expl->idlocation, "docs_location", "location_libelle", $expl->location_libelle)."&nbsp;";
		$expl_liste .= "</td><td>";
		if($expl->section_libelle_opac) {
			$expl_liste .= $expl->section_libelle_opac."&nbsp;";
		} else {
			$expl_liste .= $expl->section_libelle."&nbsp;";
		}
		$situation = "";
		if ($expl->statut_libelle_opac !="") $situation .= $expl->statut_libelle_opac."<br />";
		if ($expl->pret_flag) {
			if($expl->pret_retour)
				// exemplaire sorti
				$situation .= "<strong>".str_replace('!!date!!', formatdate($expl->pret_retour), $msg['out_until'] )."</strong>";								
			else
				// exemplaire disponible
				$situation .= "<strong>$msg[available]</strong>&nbsp;";				
		} else {
			$situation .= "<strong>$msg[exclu]</strong>";
		}
		$expl_liste .= "</td><td>$situation &nbsp;</td>";
		$expl_liste .="</tr>";
	}
	
	// affichage de la liste d'exemplaires calculées ci-dessus
	if ($compteur==0){
		$expl_liste="<tr class='even'><td colspan=5>".$msg["no_expl"]."</td></tr>";
	}
	print pmb_bidi($expl_liste);
}
?>