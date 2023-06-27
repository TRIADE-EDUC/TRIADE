<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: inhtml.inc.php,v 1.2 2017-02-07 12:00:24 dgoron Exp $

require_once ($include_path . "/misc.inc.php");

$func_format['if_logged']= 'aff_if_logged';

function aff_if_logged($param) {
	if ($_SESSION['id_empr_session']) {
		$ret = $param[1];
	}else {
		$ret = $param[2];
	}
	return $ret;
}
?>