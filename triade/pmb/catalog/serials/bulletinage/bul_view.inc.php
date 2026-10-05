<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_view.inc.php,v 1.13 2022/02/23 14:47:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $bul_id, $PMBuserid;
global $gestion_acces_active, $gestion_acces_user_notice, $serial_header;
global $art_to_show;

if(!isset($art_to_show)) $art_to_show = '';
// page de switch gestion du bulletinage périodiques

// mise à jour de l'entête de page
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4011], $serial_header);

//droits d'acces utilisateur/notice (lecture)
$acces_l=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	if (!$art_to_show) {
		$acces_j = $dom_1->getJoin($PMBuserid,4,'bulletin_notice'); //lecture
		$q = "select count(1) from bulletins $acces_j  where bulletin_id=".$bul_id;
		$r = pmb_mysql_query($q);
		if(pmb_mysql_result($r,0,0)==0) {
			$acces_l=0;
		}
	} else {
		$acces_l = $dom_1->getRights($PMBuserid,$art_to_show,4);
	}
}

if ($acces_l==0) {
	if(!$art_to_show) {
		error_message('', htmlentities($dom_1->getComment('view_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('view_depo_error'), ENT_QUOTES, $charset), 1, '');
	}
} else {
	$form = show_bulletinage_info_catalogage($bul_id);
	
	if($art_to_show) {
		$form.=  "<script>document.location='#anchor_$art_to_show'</script>";
	}
	print $form;
}

?>