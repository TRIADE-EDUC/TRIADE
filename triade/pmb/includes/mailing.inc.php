<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing.inc.php,v 1.10 2019/11/28 14:22:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/amende.class.php");
require_once($class_path."/comptes.class.php");
require_once ("$include_path/notice_authors.inc.php");  
require_once($class_path."/serie.class.php");
require_once ("$class_path/author.class.php");  

// génère un pavé d'adresse de la bibliothèque, séparé par des \n faire nl2br pour mettre cela en HTML 
function m_biblio_info($short=0) {
	
	global $biblio_name, $biblio_logo, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website ;
	global $txt_biblio_info ;

	if ($short==1) {
		return $biblio_name;
	} else { 
		// afin de ne générer qu'une fois l'adr et compagnie 
		if (!$txt_biblio_info) {
			if ($biblio_adr1 != "") $biblio_name = $biblio_name."\n";
			if ($biblio_adr2 != "") $biblio_adr1 = $biblio_adr1."\n";
			if ($biblio_cp != "") $biblio_cp = $biblio_cp." ";
			if (($biblio_cp != "") || ($biblio_town != "")) $biblio_adr2 = $biblio_adr2."\n";
			if ($biblio_state != "") $biblio_state = $biblio_state." ";
			if (($biblio_state != "") || ($biblio_country != "")) $biblio_town = $biblio_town."\n";
			if ($biblio_phone != "") $biblio_phone = $biblio_phone."\n ";
			if ($biblio_email != "") $biblio_email = "@ : ".$biblio_email."\n ";
			if ($biblio_website != "") $biblio_website = "Web : ".$biblio_website."\n ";
			if (($biblio_phone != "") || ($biblio_email != "")) $biblio_country = $biblio_country."\n";
			$txt_biblio_info = $biblio_adr1.$biblio_adr2.$biblio_cp.$biblio_town.$biblio_state.$biblio_country.$biblio_phone.$biblio_email.$biblio_website ;
		}
		return $biblio_name.$txt_biblio_info;
	}
} /* fin biblio_info */
