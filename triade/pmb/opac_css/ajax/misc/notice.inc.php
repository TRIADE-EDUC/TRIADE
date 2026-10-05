<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.inc.php,v 1.6 2021/12/28 13:50:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $id, $type, $popup_map, $show_map;

require_once("$include_path/notice_affichage.inc.php");

$id = intval($id);
if ($id) {
	//droits d'acces utilisateur/notice (lecture)
	$display = '';
	if($type == 'authority') {
	    $auth = new authority($id);
	    $display = $auth->get_detail();
	} else {
    	$requete = "SELECT * FROM notices WHERE notice_id=$id LIMIT 1";
    	$resultat = pmb_mysql_query($requete);
    	if ($resultat) {
    		if(pmb_mysql_num_rows($resultat)) {
    			//Affichage d'une notice
    			$opac_notices_depliable=0;
    			if($popup_map){
    				$display.=aff_notice($id,1,1,0,"","",0,1,0,$show_map);
    			}else{
    				$display.=aff_notice($id,1,1,0,0,0);
    			}
    			
    		}
    	}
	}
	ajax_http_send_response($display);
	
}