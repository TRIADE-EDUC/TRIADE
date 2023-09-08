<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_code_exemplaire.php,v 1.10 2018-07-24 11:19:18 dgoron Exp $

function init_gen_code_exemplaire($notice_id,$bull_id) {
	$query="select max(expl_cb)as cb from exemplaires WHERE expl_cb like 'GEN%'";
	$result = pmb_mysql_query($query);
	$code_exemplaire = pmb_mysql_result($result, 0, 0);
	if(!$code_exemplaire) {
		$query="select max(expl_cb)as cb from exemplaires WHERE expl_cb REGEXP '^[0-9]*$'";
		$result = pmb_mysql_query($query);
		$code_exemplaire = pmb_mysql_result($result, 0, 0);
		if(!$code_exemplaire) {
			$code_exemplaire = "GEN000000";
		}
	}
	return $code_exemplaire;  	   						
}

function gen_code_exemplaire($notice_id,$bull_id,$code_exemplaire) {
	if(preg_match("/(\D*)([0-9]*)/",$code_exemplaire,$matches)){
		$len = strlen($matches[2]);
		$matches[2]++;
		$code_exemplaire=$matches[1].str_pad($matches[2],$len,"0",STR_PAD_LEFT);
	} else{
		$code_exemplaire++;
	}
	return $code_exemplaire;
}