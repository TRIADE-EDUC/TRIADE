<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.inc.php,v 1.2 2020/10/05 09:49:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


global $fname;
switch($fname) {
	case 'get_empr_by_id' :
		if(empty($id)) {
			break;
		}
		$id = intval($id);
 		if(!$id) {
 			break;
 		}
		$q = 'select id_empr, empr_cb, empr_nom, empr_prenom, empr_mail, empr_tel1 from empr where id_empr = '.$id.' limit 1';
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$empr = pmb_mysql_fetch_assoc($r,0,0);
			ajax_http_send_response($empr);
		}
		break;
	case 'get_empr_by_barcode' :
		if(empty($barcode) || !is_string($barcode)) {
			break;
		}
		$q = 'select id_empr, empr_cb, empr_nom, empr_prenom, empr_mail, empr_tel1 from empr where empr_cb = "'.addslashes($barcode).'" limit 1';
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$empr = pmb_mysql_fetch_assoc($r,0,0);
			ajax_http_send_response($empr);
		}
		break;
}