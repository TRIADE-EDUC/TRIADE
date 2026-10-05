<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: prix_expl.inc.php,v 1.4 2019/06/05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $pmb_prefill_prix;

if (isset($pmb_prefill_prix) && $pmb_prefill_prix) {
	function prefill_prix ($id_notice=0,$prix='') {
		if (!$prix) {
			$requete = "SELECT prix FROM notices WHERE notice_id='$id_notice' ";
			$result = @pmb_mysql_query($requete);
			$res_prix = pmb_mysql_fetch_object($result);
			return $res_prix->prix;
		} else {
			return $prix;
		}
	}
} else {
	function prefill_prix ($id_notice=0,$prix='') {
		return $prix;
	}
}
