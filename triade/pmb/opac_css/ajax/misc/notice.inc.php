<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.inc.php,v 1.4 2019-01-03 09:38:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$include_path/notice_affichage.inc.php");

if ($id) {
	//droits d'acces utilisateur/notice (lecture)
	$display = '';
	$requete = "SELECT * FROM notices WHERE notice_id=$id LIMIT 1";
	$resultat = pmb_mysql_query($requete,$dbh);
	if ($resultat) {
		if(pmb_mysql_num_rows($resultat)) {
			$notice = pmb_mysql_fetch_object($resultat);	
			//Affichage d'une notice
			$opac_notices_depliable=0;
			if($popup_map){
				$display.=aff_notice($id,1,1,0,"","",0,1,0,$show_map);
			}else{
				$display.=aff_notice($id,1,1,0,0,0);
			}
			
		}
	}
	ajax_http_send_response($display);
	
}