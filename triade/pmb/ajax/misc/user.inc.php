<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user.inc.php,v 1.4 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $fname, $PMBuserid;

switch($fname) {
	case 'get_group' :
		$q = 'select grp_num from users where userid = '.$PMBuserid.' limit 1';
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$grp = pmb_mysql_result($r,0,0);
			ajax_http_send_response($grp);
		}
		break;
}