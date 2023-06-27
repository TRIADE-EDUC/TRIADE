<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: log.inc.php,v 1.3 2016-06-29 14:50:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $log, $infos_notice, $infos_expl;

if($_SESSION['user_code']) {
	$res=pmb_mysql_query($log->get_empr_query());
	if($res){
		$empr_carac = pmb_mysql_fetch_array($res);
		$log->add_log('empr',$empr_carac);
	}
}
$log->add_log('num_session',session_id());

$log->save();