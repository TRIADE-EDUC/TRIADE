<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_date_tri.inc.php,v 1.6 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/classes/notice.class.php');

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (!isset($start)) {
	$start=0;
}

$v_state=urldecode($v_state);

if (!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
	$count = pmb_mysql_result($notices, 0, 0);
}
	
print "<br /><br /><h2 class='center'>".htmlentities($msg["gen_date_tri_msg"], ENT_QUOTES, $charset)."</h2>";


$query = pmb_mysql_query("select notice_id, year, niveau_biblio, niveau_hierar from notices order by notice_id LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
	
	while($mesNotices = pmb_mysql_fetch_assoc($query)) {
		
		switch($mesNotices['niveau_biblio'].$mesNotices['niveau_hierar']){
			case 'a2': 
				//Si c'est un article, on récupère la date du bulletin associé
				$reqAnneeArticle = "SELECT date_date FROM bulletins, analysis WHERE analysis_bulletin=bulletin_id AND analysis_notice='".$mesNotices['notice_id']."'";
				$queryArt=pmb_mysql_query($reqAnneeArticle,$dbh);
				
				if(!pmb_mysql_num_rows($queryArt)) $dateArt = "";
				else $dateArt=pmb_mysql_result($queryArt,0,0);
							
				if($dateArt == '0000-00-00' || !isset($dateArt) || $dateArt == "") $annee_art_tmp = "";
					else $annee_art_tmp = substr($dateArt,0,4);

				//On met à jour, les notices avec la date de parution et l'année
				$reqMajArt = "UPDATE notices SET date_parution='".$dateArt."', year='".$annee_art_tmp."'
							WHERE notice_id='".$mesNotices['notice_id']."'";
		        pmb_mysql_query($reqMajArt, $dbh);
			    break;	
				
			case 'b2': 
				//Si c'est une notice de bulletin, on récupère la date pour connaitre l'année						
				$reqAnneeBulletin = "SELECT date_date FROM bulletins WHERE num_notice='".$mesNotices['notice_id']."'";
				$queryAnnee=pmb_mysql_query($reqAnneeBulletin,$dbh);
				
				if(!pmb_mysql_num_rows($queryAnnee)) $dateBulletin="";
				else $dateBulletin = pmb_mysql_result($queryAnnee,0,0);
				
				if($dateBulletin == '0000-00-00' || !isset($dateBulletin) || $dateBulletin == "") $annee_tmp = "";
				else $annee_tmp = substr($dateBulletin,0,4);
				
				//On met à jour date de parution et année
				$reqMajBull = "UPDATE notices SET date_parution='".$dateBulletin."', year='".$annee_tmp."'
						WHERE notice_id='".$mesNotices['notice_id']."'";
	    		pmb_mysql_query($reqMajBull, $dbh);
				
				break;
				
			default:
				// Mise à jour du champ date_parution des notices (monographie et pério)
				$date_parution = notice::get_date_parution($mesNotices['year']);
		    	$reqMaj = "UPDATE notices SET date_parution='".$date_parution."' WHERE notice_id='".$mesNotices['notice_id']."'";
		    	pmb_mysql_query($reqMaj, $dbh);
		    	break;
		}    	           		   	
	}
	pmb_mysql_free_result($query);

	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - GEN_DATE_TRI;
	$not = pmb_mysql_query("SELECT count(1) FROM notices", $dbh);
	$compte = pmb_mysql_result($not, 0, 0);
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg['gen_date_tri_msg'], ENT_QUOTES, $charset)." : ";
	$v_state .= $compte." ".htmlentities($msg['gen_date_tri_msg'], ENT_QUOTES, $charset);
	print netbase::get_process_state_form($v_state, $spec);
}