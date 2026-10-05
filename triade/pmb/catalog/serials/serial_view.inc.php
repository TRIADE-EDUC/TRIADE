<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_view.inc.php,v 1.14 2022/02/23 08:05:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $serial_header, $PMBuserid, $serial_id;
global $gestion_acces_active, $gestion_acces_user_notice;

if(!isset($page)) $page = 0;
if(!isset($nbr_lignes)) $nbr_lignes = 0;

// résultat de recherche pour gestion des périodiques
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['show'], $serial_header);
		
//print $serial_access_form;

//droits d'acces utilisateur/notice (lecture)
$acces_l=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_l = $dom_1->getRights($PMBuserid,$serial_id,4);	//lecture
}

if ($acces_l==0) {
	error_message('', htmlentities($dom_1->getComment('view_seri_error'), ENT_QUOTES, $charset), 1, '');
} else {
	if($serial_id) {
		$myQuery = pmb_mysql_query("SELECT * FROM notices WHERE notice_id=$serial_id ");
	}
	
	if($serial_id && pmb_mysql_num_rows($myQuery)) {
		$sort_children = 1;
		show_serial_info($serial_id, $page, $nbr_lignes);
	} else {
		print "<div class=\"row\"><div class=\"msg-perio\">".$msg['catalog_serie_impossible_aff']."</div></div>";
	}
}