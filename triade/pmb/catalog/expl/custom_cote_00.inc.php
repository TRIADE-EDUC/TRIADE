<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_cote_00.inc.php,v 1.5 2022/01/03 14:10:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function prefill_cote ($id_notice=0,$cote="") {
	global $value_prefix_cote ;
	if (!$cote) {
		$requete = "SELECT indexint_name FROM indexint, notices where notice_id='$id_notice' and indexint=indexint_id ";
		$result = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_num_rows($result);
		if ($nbr_lignes) {
			$res_cote = pmb_mysql_fetch_object($result) ;
			return $value_prefix_cote.$res_cote->indexint_name ;
			} else return $value_prefix_cote ;
		} else  return $cote ;
}
