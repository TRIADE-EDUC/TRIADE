<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_pret_temp.inc.php,v 1.5 2022/01/04 08:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function clean_pret_temp() {
	global $pmb_pret_timeout_temp;
	
	if( $pmb_pret_timeout_temp) {
		$rqt="delete from pret where pret_temp != '' and pret_date > DATE_SUB( sysdate( ) , INTERVAL '$pmb_pret_timeout_temp' MINUTE )";
		pmb_mysql_query($rqt);	
	}	
	$rqt="delete from pret where pret_temp = '".$_SERVER['REMOTE_ADDR']."' and  pret_temp != '' ";
	pmb_mysql_query($rqt);
}